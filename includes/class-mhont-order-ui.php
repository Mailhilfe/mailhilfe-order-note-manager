<?php
/**
 * WooCommerce order screen integration.
 *
 * @package Mailhilfe_Order_Note_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds template selector to order edit pages.
 */
final class MHONT_Order_UI {

	/** @var array<int,string> */
	private static $order_language_cache = array();

	/**
	 * Registers hooks.
	 *
	 * @return void
	 */
	public static function hooks() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_order_meta_box' ), 30 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_filter( 'admin_body_class', array( __CLASS__, 'admin_body_class' ) );
		add_action( 'wp_ajax_mhont_preview_template', array( __CLASS__, 'ajax_preview_template' ) );
		add_action( 'wp_ajax_mhont_add_order_note', array( __CLASS__, 'ajax_add_order_note' ) );
		add_action( 'wp_ajax_mhont_toggle_personal_favorite', array( __CLASS__, 'ajax_toggle_personal_favorite' ) );
	}

	/**
	 * Adds order meta box for legacy and HPOS screens.
	 *
	 * @return void
	 */
	public static function add_order_meta_box() {
		if ( ! current_user_can( MHONT_Capabilities::USE_TEMPLATES ) ) {
			return;
		}

		$screens = array( 'shop_order' );
		if ( class_exists( '\Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController' ) && function_exists( 'wc_get_container' ) ) {
			try {
				$controller = wc_get_container()->get( \Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController::class );
				if ( $controller && method_exists( $controller, 'custom_orders_table_usage_is_enabled' ) && $controller->custom_orders_table_usage_is_enabled() ) {
					$screens[] = wc_get_page_screen_id( 'shop-order' );
				}
			} catch ( Throwable $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
				// The legacy screen remains registered if WooCommerce internals are unavailable.
			}
		}

		foreach ( array_unique( array_filter( $screens ) ) as $screen ) {
			add_meta_box(
				'mhont_order_note_template',
				__( 'Mailhilfe Order Note Manager', 'mailhilfe-order-note-manager' ),
				array( __CLASS__, 'render_order_meta_box' ),
				$screen,
				'normal',
				'high'
			);
		}
	}

	/**
	 * Adds a scoped body class on WooCommerce order edit screens.
	 *
	 * The order editor uses floated meta-box columns. A tall custom meta box can
	 * otherwise extend below the calculated content height and make the WordPress
	 * admin footer appear on top of the box contents.
	 *
	 * @param string $classes Existing body classes.
	 * @return string
	 */
	public static function admin_body_class( $classes ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( self::is_order_screen( $screen ) ) {
			$classes .= ' mhont-order-note-screen';
		}

		return trim( $classes );
	}

	/**
	 * Determines whether the current admin screen edits a WooCommerce order.
	 *
	 * @param WP_Screen|null $screen Current screen object.
	 * @return bool
	 */
	private static function is_order_screen( $screen ) {
		if ( ! is_object( $screen ) ) {
			return false;
		}

		$screen_id = isset( $screen->id ) ? (string) $screen->id : '';

		return false !== strpos( $screen_id, 'shop_order' )
			|| false !== strpos( $screen_id, 'shop-order' )
			|| false !== strpos( $screen_id, 'wc-orders' );
	}

	/**
	 * Enqueues admin assets.
	 *
	 * @param string $hook Current admin hook.
	 * @return void
	 */
	public static function enqueue_assets( $hook ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen ) {
			return;
		}

		$screen_id     = (string) $screen->id;
		$screen_post   = isset( $screen->post_type ) ? (string) $screen->post_type : '';
		$is_order      = self::is_order_screen( $screen );
		$is_templates  = MHONT_Post_Types::POST_TYPE === $screen_post;
		$is_tools_page = in_array( $screen_id, array( 'woocommerce_page_mhont-import-export', 'mhont_template_page_mhont-import-export', 'mhont_template_page_mhont-settings', 'mhont_template_page_mhont-permissions' ), true );

		if ( ! $is_order && ! $is_templates && ! $is_tools_page ) {
			return;
		}

		if ( $is_templates && function_exists( 'wp_enqueue_editor' ) ) {
			wp_enqueue_editor();
		}

		wp_enqueue_style( 'mhont-admin', MHONT_URL . 'assets/css/admin.css', array(), MHONT_VERSION );

		$needs_script = $is_order || $is_templates;
		if ( ! $needs_script ) {
			return;
		}

		$script_dependencies = array( 'jquery' );
		if ( $is_templates && 'edit' === $screen->base ) {
			$script_dependencies[] = 'jquery-ui-sortable';
		}
		wp_enqueue_script( 'mhont-admin', MHONT_URL . 'assets/js/admin.js', $script_dependencies, MHONT_VERSION, true );
		wp_localize_script(
			'mhont-admin',
			'mhontAdmin',
			array(
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'previewNonce' => wp_create_nonce( 'mhont_preview_template' ),
				'sortNonce'    => wp_create_nonce( 'mhont_sort_templates' ),
				'i18n'         => array(
					'loading' => __( 'Loading preview...', 'mailhilfe-order-note-manager' ),
					'error'        => __( 'Preview could not be loaded.', 'mailhilfe-order-note-manager' ),
					'saved'        => __( 'Sorting saved.', 'mailhilfe-order-note-manager' ),
					'sortError'     => __( 'The template order could not be saved.', 'mailhilfe-order-note-manager' ),
					'inserted'     => __( 'Placeholder inserted.', 'mailhilfe-order-note-manager' ),
					'saving'       => __( 'Adding order note...', 'mailhilfe-order-note-manager' ),
					'added'        => __( 'The order note was added successfully.', 'mailhilfe-order-note-manager' ),
					'addError'     => __( 'The order note could not be added.', 'mailhilfe-order-note-manager' ),
					'favoriteSaved' => __( 'Personal favorite updated.', 'mailhilfe-order-note-manager' ),
				),
			)
		);
	}

	/**
	 * Gets order from admin screen callback arguments.
	 *
	 * @param WP_Post|WC_Order $object Post or order object.
	 * @return WC_Order|false
	 */
	private static function get_order_from_object( $object ) {
		if ( ! function_exists( 'wc_get_order' ) ) {
			return false;
		}

		if ( is_a( $object, 'WC_Order' ) ) {
			return $object;
		}

		if ( is_a( $object, 'WP_Post' ) ) {
			return wc_get_order( $object->ID );
		}

		return false;
	}

	/**
	 * Renders order template selector.
	 *
	 * @param WP_Post|WC_Order $object Post or order object.
	 * @return void
	 */
	public static function render_order_meta_box( $object ) {
		$order = self::get_order_from_object( $object );
		if ( ! $order ) {
			esc_html_e( 'Order could not be loaded.', 'mailhilfe-order-note-manager' );
			return;
		}

		if ( ! self::current_user_can_edit_order( $order ) ) {
			esc_html_e( 'You are not allowed to use note templates.', 'mailhilfe-order-note-manager' );
			return;
		}

		$templates = self::get_templates( $order );
		$default_note_type = class_exists( 'MHONT_Settings' ) ? (string) MHONT_Settings::get( 'default_note_type' ) : 'private';
		$default_note_type = 'customer' === $default_note_type ? 'customer' : 'private';
		if ( empty( $templates ) ) {
			esc_html_e( 'No templates are available yet.', 'mailhilfe-order-note-manager' );
			return;
		}

		?>
		<div class="mhont-order-form" data-order-id="<?php echo esc_attr( $order->get_id() ); ?>" data-note-nonce="<?php echo esc_attr( wp_create_nonce( 'mhont_add_order_note_' . $order->get_id() ) ); ?>">

			<div class="mhont-field">
				<label for="mhont_template_search"><strong><?php esc_html_e( 'Search templates', 'mailhilfe-order-note-manager' ); ?></strong></label>
				<input type="search" id="mhont_template_search" class="widefat" placeholder="<?php esc_attr_e( 'Type to filter templates...', 'mailhilfe-order-note-manager' ); ?>">
			</div>

			<div class="mhont-field">
				<label for="mhont_template_id"><strong><?php esc_html_e( 'Template', 'mailhilfe-order-note-manager' ); ?></strong></label>
				<?php $personal_favorite_ids = array_map( 'absint', (array) get_user_meta( get_current_user_id(), '_mhont_personal_favorites', true ) ); ?>
				<select id="mhont_template_id" class="widefat" data-order-id="<?php echo esc_attr( $order->get_id() ); ?>">
					<option value=""><?php esc_html_e( 'Select template', 'mailhilfe-order-note-manager' ); ?></option>
					<?php foreach ( $templates as $template ) : ?>
						<?php
						$note_type  = (string) get_post_meta( $template->ID, '_mhont_note_type', true );
						$is_favorite = 'yes' === get_post_meta( $template->ID, '_mhont_favorite', true );
						?>
						<option value="<?php echo esc_attr( $template->ID ); ?>" data-note-type="<?php echo esc_attr( $note_type ); ?>" data-favorite="<?php echo esc_attr( $is_favorite ? 'yes' : 'no' ); ?>" data-personal-favorite="<?php echo esc_attr( in_array( (int) $template->ID, $personal_favorite_ids, true ) ? 'yes' : 'no' ); ?>"><?php echo esc_html( $is_favorite ? '★ ' . $template->post_title : $template->post_title ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="mhont-field mhont-favorite-field"><button type="button" class="button" id="mhont_personal_favorite_button" data-label="<?php echo esc_attr__( 'Personal favorite', 'mailhilfe-order-note-manager' ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'mhont_toggle_personal_favorite' ) ); ?>">☆ <?php esc_html_e( 'Personal favorite', 'mailhilfe-order-note-manager' ); ?></button></div>

			<div class="mhont-field">
				<label for="mhont_order_note_type"><strong><?php esc_html_e( 'Note type', 'mailhilfe-order-note-manager' ); ?></strong></label>
				<select id="mhont_order_note_type" class="widefat">
					<option value="private" <?php selected( $default_note_type, 'private' ); ?>><?php esc_html_e( 'Internal note', 'mailhilfe-order-note-manager' ); ?></option>
					<option value="customer" <?php selected( $default_note_type, 'customer' ); ?>><?php esc_html_e( 'Customer note', 'mailhilfe-order-note-manager' ); ?></option>
				</select>
			</div>

			<?php self::render_last_customer_notification( $order ); ?>

			<div class="mhont-field mhont-preview-field">
				<strong class="mhont-field-label"><?php esc_html_e( 'Preview', 'mailhilfe-order-note-manager' ); ?></strong>
				<div id="mhont_preview" class="mhont-preview mhont-editable-preview" contenteditable="true" role="textbox" aria-multiline="true" aria-live="polite"><?php esc_html_e( 'Select a template to show the preview.', 'mailhilfe-order-note-manager' ); ?></div>
				<input type="hidden" id="mhont_edited_note" value="">
			</div>

			<div class="mhont-placeholder-buttons">
				<p><strong><?php esc_html_e( 'Insert placeholder', 'mailhilfe-order-note-manager' ); ?></strong></p>
				<?php foreach ( MHONT_Placeholders::get_definitions() as $placeholder => $label ) : ?>
					<button type="button" class="button button-small mhont-insert-placeholder" data-placeholder="<?php echo esc_attr( $placeholder ); ?>" title="<?php echo esc_attr( $label ); ?>"><?php echo esc_html( $placeholder ); ?></button>
				<?php endforeach; ?>
			</div>

			<div id="mhont_action_message" class="notice inline" role="status" aria-live="polite" hidden></div>

			<div class="mhont-field mhont-submit-field">
				<button type="button" id="mhont_add_order_note_button" class="button button-primary widefat" disabled><?php esc_html_e( 'Add note to order', 'mailhilfe-order-note-manager' ); ?></button>
			</div>
		</div>
		<?php
	}

	/**
	 * Returns published templates.
	 *
	 * @return WP_Post[]
	 */
	private static function get_templates( $order = null ) {
		$template_ids = MHONT_Template_Cache::get_published_ids();

		if ( empty( $template_ids ) ) {
			return array();
		}

		if ( function_exists( '_prime_post_caches' ) ) {
			// Prime post objects and metadata, but skip the unused term cache.
			// The previous argument order primed terms instead of metadata and
			// caused one metadata query per template in the order selector.
			_prime_post_caches( $template_ids, false, true );
		} else {
			update_meta_cache( 'post', $template_ids );
		}

		$templates = array_values( array_filter( array_map( 'get_post', $template_ids ) ) );
		$templates = array_values( array_filter( $templates, static function ( $template ) use ( $order ) { return ! $order || self::template_matches_conditions( $template, $order ); } ) );
		$order_language = '';
		if ( class_exists( 'MHONT_Settings' ) && MHONT_Settings::enabled( 'use_order_language' ) ) {
			$order_language = self::get_order_language( $order );
		}

		if ( '' !== $order_language ) {
			$templates = array_values(
				array_filter(
					$templates,
					static function ( $template ) use ( $order_language ) {
						return self::template_matches_language( $template, $order_language );
					}
				)
			);
		}


		$personal_favorites = array_map( 'absint', (array) get_user_meta( get_current_user_id(), '_mhont_personal_favorites', true ) );
		$recent = array_map( 'absint', (array) get_user_meta( get_current_user_id(), '_mhont_recent_templates', true ) );
		$templates = apply_filters( 'mailhilfe_order_note_template_results', $templates, $order, get_current_user_id() );
		$recent_rank = array_flip( $recent );
		$personal_map = array_fill_keys( $personal_favorites, 1 );
		$global_favorites_enabled = ! class_exists( 'MHONT_Settings' ) || MHONT_Settings::enabled( 'favorites_first' );

		$favorites = array();
		foreach ( $templates as $template ) {
			$favorites[ $template->ID ] = ( isset( $personal_map[ $template->ID ] ) ? 2 : 0 ) + ( $global_favorites_enabled && 'yes' === get_post_meta( $template->ID, '_mhont_favorite', true ) ? 1 : 0 );
		}

		usort(
			$templates,
			static function ( $a, $b ) use ( $favorites, $recent_rank ) {
				$a_favorite = isset( $favorites[ $a->ID ] ) ? $favorites[ $a->ID ] : 0;
				$b_favorite = isset( $favorites[ $b->ID ] ) ? $favorites[ $b->ID ] : 0;
				if ( $a_favorite !== $b_favorite ) {
					return $b_favorite <=> $a_favorite;
				}
				$a_recent = isset( $recent_rank[ $a->ID ] ) ? $recent_rank[ $a->ID ] : PHP_INT_MAX;
				$b_recent = isset( $recent_rank[ $b->ID ] ) ? $recent_rank[ $b->ID ] : PHP_INT_MAX;
				if ( $a_recent !== $b_recent ) { return $a_recent <=> $b_recent; }
				if ( (int) $a->menu_order !== (int) $b->menu_order ) {
					return (int) $a->menu_order <=> (int) $b->menu_order;
				}
				return strcasecmp( $a->post_title, $b->post_title );
			}
		);

		return $templates;
	}

	/**
	 * Checks whether a template is available for the current order language.
	 *
	 * This server-side check mirrors the selector filtering so an authorized
	 * request cannot use a template from an unrelated language by changing the
	 * template ID in the AJAX payload.
	 *
	 * @param WP_Post  $template Template post.
	 * @param WC_Order $order    WooCommerce order.
	 * @return bool
	 */
	private static function template_is_available_for_order( $template, $order ) {
		if ( ! is_a( $template, 'WP_Post' ) || ! $order || ! is_a( $order, 'WC_Order' ) ) {
			return false;
		}

		if ( ! self::template_matches_conditions( $template, $order ) ) { return false; }

		if ( ! class_exists( 'MHONT_Settings' ) || ! MHONT_Settings::enabled( 'use_order_language' ) ) {
			return true;
		}
		$order_language = self::get_order_language( $order );
		return '' === $order_language ? true : self::template_matches_language( $template, $order_language );
	}


	/**
	 * Compares one template locale with a precomputed order locale.
	 *
	 * @param WP_Post $template       Template post.
	 * @param string  $order_language Normalized order language.
	 * @return bool
	 */
	private static function template_matches_language( $template, $order_language ) {
		$template_language = get_post_meta( $template->ID, '_mhont_language', true );
		$template_language = is_string( $template_language ) ? strtolower( str_replace( '-', '_', trim( $template_language ) ) ) : '';
		if ( '' === $template_language ) {
			return true;
		}

		$order_language = strtolower( str_replace( '-', '_', $order_language ) );
		return $template_language === $order_language || strtok( $template_language, '_' ) === strtok( $order_language, '_' );
	}


	/**
	 * Tries to detect the order language from WPML, Polylang or common order meta.
	 *
	 * @param WC_Order|null $order WooCommerce order.
	 * @return string
	 */
	private static function get_order_language( $order ) {
		if ( ! $order || ! is_a( $order, 'WC_Order' ) ) {
			return '';
		}

		$order_id = absint( $order->get_id() );
		if ( isset( self::$order_language_cache[ $order_id ] ) ) {
			return self::$order_language_cache[ $order_id ];
		}

		$language = '';
		if ( function_exists( 'pll_get_post_language' ) ) {
			$language = pll_get_post_language( $order->get_id(), 'locale' );
		}

		if ( '' === $language && has_filter( 'wpml_post_language_details' ) ) {
			$details = apply_filters( 'wpml_post_language_details', null, $order->get_id() );
			if ( is_array( $details ) && ! empty( $details['locale'] ) ) {
				$language = $details['locale'];
			} elseif ( is_array( $details ) && ! empty( $details['language_code'] ) ) {
				$language = $details['language_code'];
			}
		}

		foreach ( array( 'wpml_language', '_wpml_language', 'pll_language', '_locale', 'locale' ) as $meta_key ) {
			if ( '' !== $language ) {
				break;
			}
			$value = $order->get_meta( $meta_key, true );
			if ( is_string( $value ) && '' !== $value ) {
				$language = $value;
			}
		}

		$language = is_string( $language ) ? sanitize_key( str_replace( '-', '_', $language ) ) : '';
		self::$order_language_cache[ $order_id ] = $language;
		return $language;
	}

	/**
	 * AJAX preview callback.
	 *
	 * @return void
	 */
	public static function ajax_preview_template() {
		check_ajax_referer( 'mhont_preview_template', 'nonce' );

		if ( ! current_user_can( MHONT_Capabilities::USE_TEMPLATES ) ) {
			wp_send_json_error( array( 'message' => __( 'You are not allowed to use note templates.', 'mailhilfe-order-note-manager' ) ), 403 );
		}

		$template_id = isset( $_POST['template_id'] ) ? absint( $_POST['template_id'] ) : 0;
		$order_id    = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;

		if ( ! function_exists( 'wc_get_order' ) ) {
			wp_send_json_error( array( 'message' => __( 'WooCommerce is required for this action.', 'mailhilfe-order-note-manager' ) ), 503 );
		}

		$template = get_post( $template_id );
		$order    = wc_get_order( $order_id );

		if ( ! $template || MHONT_Post_Types::POST_TYPE !== $template->post_type || 'publish' !== $template->post_status || ! $order ) {
			wp_send_json_error( array( 'message' => __( 'Template or order could not be found.', 'mailhilfe-order-note-manager' ) ), 404 );
		}

		if ( ! self::current_user_can_edit_order( $order ) ) {
			wp_send_json_error( array( 'message' => __( 'You are not allowed to use note templates.', 'mailhilfe-order-note-manager' ) ), 403 );
		}

		if ( ! self::template_is_available_for_order( $template, $order ) ) {
			wp_send_json_error( array( 'message' => __( 'The selected template is not available for this order language.', 'mailhilfe-order-note-manager' ) ), 400 );
		}

		$content = get_post_meta( $template_id, '_mhont_content', true );
		$allow_html = ! class_exists( 'MHONT_Settings' ) || MHONT_Settings::enabled( 'allow_html' );
		$preview    = wp_kses_post( wpautop( MHONT_Placeholders::replace( $content, $order ) ) );
		if ( ! $allow_html ) {
			$preview = self::html_to_plain_text( $preview );
		}
		$note_type = get_post_meta( $template_id, '_mhont_note_type', true );
		if ( ! in_array( $note_type, array( 'private', 'customer' ), true ) ) {
			$note_type = class_exists( 'MHONT_Settings' ) ? (string) MHONT_Settings::get( 'default_note_type' ) : 'private';
			$note_type = 'customer' === $note_type ? 'customer' : 'private';
		}

		wp_send_json_success(
			array(
				'preview'      => $preview,
				'preview_html' => $allow_html,
				'note_type'    => $note_type,
			)
		);
	}

	/**
	 * Handles note creation from an AJAX request.
	 *
	 * The WooCommerce order screen already contains a form. Using a nested form
	 * causes browsers to submit the outer order form to edit.php instead of the
	 * plugin action. AJAX avoids invalid nested forms and works on both legacy
	 * and HPOS order screens.
	 *
	 * @return void
	 */
	public static function ajax_add_order_note() {
		$result = self::create_order_note_from_request();

		if ( is_wp_error( $result ) ) {
			$status = (int) $result->get_error_data( 'status' );
			wp_send_json_error(
				array( 'message' => $result->get_error_message() ),
				$status > 0 ? $status : 400
			);
		}

		wp_send_json_success(
			array(
				'message'    => __( 'The order note was added successfully.', 'mailhilfe-order-note-manager' ),
				'notes_html' => self::render_order_notes_html( isset( $result['note_ids'] ) ? $result['note_ids'] : array() ),
			)
		);
	}

	/**
	 * Validates the request and creates the order note.
	 *
	 * @return array|WP_Error
	 */
	private static function create_order_note_from_request() {
		$order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
		$nonce    = isset( $_POST['mhont_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['mhont_nonce'] ) ) : '';

		if ( ! $order_id || ! wp_verify_nonce( $nonce, 'mhont_add_order_note_' . $order_id ) ) {
			return new WP_Error( 'mhont_security_failed', __( 'Security check failed.', 'mailhilfe-order-note-manager' ), array( 'status' => 403 ) );
		}

		if ( ! current_user_can( MHONT_Capabilities::USE_TEMPLATES ) ) {
			return new WP_Error( 'mhont_forbidden', __( 'You are not allowed to use note templates.', 'mailhilfe-order-note-manager' ), array( 'status' => 403 ) );
		}

		$template_id = isset( $_POST['template_id'] ) ? absint( $_POST['template_id'] ) : 0;
		$note_type   = isset( $_POST['note_type'] ) ? sanitize_key( wp_unslash( $_POST['note_type'] ) ) : 'private';
		if ( ! in_array( $note_type, array( 'private', 'customer' ), true ) ) {
			$note_type = 'private';
		}

		if ( ! function_exists( 'wc_get_order' ) ) {
			return new WP_Error( 'mhont_woocommerce_missing', __( 'WooCommerce is required for this action.', 'mailhilfe-order-note-manager' ), array( 'status' => 503 ) );
		}

		$template = get_post( $template_id );
		$order    = wc_get_order( $order_id );

		if ( ! $template || MHONT_Post_Types::POST_TYPE !== $template->post_type || 'publish' !== $template->post_status || ! $order ) {
			return new WP_Error( 'mhont_not_found', __( 'Template or order could not be found.', 'mailhilfe-order-note-manager' ), array( 'status' => 404 ) );
		}

		if ( ! self::current_user_can_edit_order( $order ) ) {
			return new WP_Error( 'mhont_forbidden', __( 'You are not allowed to use note templates.', 'mailhilfe-order-note-manager' ), array( 'status' => 403 ) );
		}

		if ( ! self::template_is_available_for_order( $template, $order ) ) {
			return new WP_Error( 'mhont_language_mismatch', __( 'The selected template is not available for this order language.', 'mailhilfe-order-note-manager' ), array( 'status' => 400 ) );
		}

		$content = get_post_meta( $template_id, '_mhont_content', true );
		$note    = wp_kses_post( wpautop( MHONT_Placeholders::replace( $content, $order ) ) );
		if ( class_exists( 'MHONT_Settings' ) && ! MHONT_Settings::enabled( 'allow_html' ) ) {
			$note = self::html_to_plain_text( $note );
		}

		if ( isset( $_POST['edited_note'] ) ) {
			$raw_edited_note = wp_unslash( $_POST['edited_note'] );
			if ( ! is_string( $raw_edited_note ) || strlen( $raw_edited_note ) > 100000 ) {
				return new WP_Error( 'mhont_note_too_large', __( 'The edited note is too large.', 'mailhilfe-order-note-manager' ), array( 'status' => 413 ) );
			}
			$raw_edited_note = MHONT_Placeholders::replace( $raw_edited_note, $order );
			$edited_note = ( class_exists( 'MHONT_Settings' ) && ! MHONT_Settings::enabled( 'allow_html' ) )
				? self::html_to_plain_text( $raw_edited_note )
				: wp_kses_post( $raw_edited_note );
			if ( '' !== trim( wp_strip_all_tags( $edited_note ) ) ) {
				$note = $edited_note;
			}
		}

		if ( '' === trim( wp_strip_all_tags( $note ) ) ) {
			return new WP_Error( 'mhont_empty_note', __( 'The selected template is empty.', 'mailhilfe-order-note-manager' ), array( 'status' => 400 ) );
		}

		$is_customer_note = 'customer' === $note_type;
		$note = apply_filters( 'mailhilfe_order_note_content', $note, $order, $template, $note_type );
		do_action( 'mailhilfe_order_note_before_add', $order, $template, $note, $note_type );
		if ( $is_customer_note && class_exists( 'MHONT_History' ) ) { MHONT_History::mark_pending_customer_note( $order_id, $template_id ); }
		try {
			$note_id = $order->add_order_note( $note, $is_customer_note, true );
		} catch ( Throwable $error ) {
			$note_id = 0;
		}

		if ( ! $note_id ) {
			return new WP_Error( 'mhont_save_failed', __( 'The order note could not be saved.', 'mailhilfe-order-note-manager' ), array( 'status' => 500 ) );
		}

		$note_ids = array( absint( $note_id ) );

		if ( $is_customer_note ) {
			try {
				self::store_customer_notification_timestamp( $order, $template );
				if ( ! class_exists( 'MHONT_Settings' ) || MHONT_Settings::enabled( 'log_customer_notes' ) ) {
					$log_note_id = self::add_customer_notification_log_note( $order, $template );
					if ( $log_note_id ) {
						$note_ids[] = absint( $log_note_id );
					}
				}
			} catch ( Throwable $error ) {
				// The customer note was created successfully; auxiliary logging must not cause duplicate retries.
			}
		}


		$recent = array_values( array_unique( array_merge( array( $template_id ), array_map( 'absint', (array) get_user_meta( get_current_user_id(), '_mhont_recent_templates', true ) ) ) ) );
		update_user_meta( get_current_user_id(), '_mhont_recent_templates', array_slice( $recent, 0, 10 ) );
		if ( class_exists( 'MHONT_History' ) ) {
			MHONT_History::record( 'note', 'created', $order_id, $template_id, array( 'note_type' => $note_type, 'note_id' => absint( $note_id ) ), $is_customer_note ? $order->get_billing_email() : '' );
		}
		do_action( 'mailhilfe_order_note_after_add', $order, $template, $note, $note_type, absint( $note_id ) );
		$usage_count = absint( get_post_meta( $template_id, '_mhont_usage_count', true ) );
		update_post_meta( $template_id, '_mhont_usage_count', $usage_count + 1 );
		update_post_meta( $template_id, '_mhont_last_used', current_time( 'mysql', true ) );

		return array( 'note_ids' => $note_ids );
	}


	/**
	 * Converts safe or encoded HTML into readable plain text while preserving
	 * common paragraph and line-break boundaries.
	 *
	 * @param string $value HTML or text value.
	 * @return string
	 */
	private static function html_to_plain_text( $value ) {
		$value = is_string( $value ) ? $value : '';
		$value = html_entity_decode( $value, ENT_QUOTES | ENT_HTML5, get_bloginfo( 'charset' ) );
		$value = preg_replace( '/<\s*br\s*\/?\s*>/i', "\n", $value );
		$value = preg_replace( '/<\/\s*(?:p|div|li|h[1-6]|tr)\s*>/i', "\n", $value );
		$value = wp_strip_all_tags( $value );
		$value = preg_replace( "/[ \t]+\n/", "\n", $value );
		$value = preg_replace( "/\n{3,}/", "\n\n", $value );

		return sanitize_textarea_field( trim( $value ) );
	}

	/**
	 * Stores the latest customer-notification details on the order.
	 *
	 * @param WC_Order $order    WooCommerce order.
	 * @param WP_Post  $template Used note template.
	 * @return void
	 */
	private static function store_customer_notification_timestamp( $order, $template ) {
		$current_user = wp_get_current_user();
		$order->update_meta_data( '_mhont_last_customer_notification_time', current_time( 'mysql', true ) );
		$order->update_meta_data( '_mhont_last_customer_notification_user', ( $current_user && $current_user->exists() ) ? $current_user->display_name : '' );
		$order->update_meta_data( '_mhont_last_customer_notification_template', is_a( $template, 'WP_Post' ) ? get_the_title( $template ) : '' );
		$order->save();
	}

	/**
	 * Displays the most recently recorded customer notification.
	 *
	 * @param WC_Order $order WooCommerce order.
	 * @return void
	 */
	private static function render_last_customer_notification( $order ) {
		$stored_time = $order->get_meta( '_mhont_last_customer_notification_time', true );
		if ( ! is_string( $stored_time ) || '' === $stored_time ) {
			return;
		}

		$timestamp = strtotime( $stored_time . ' UTC' );
		$display_time = $timestamp ? wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $timestamp ) : $stored_time;
		$user_name = (string) $order->get_meta( '_mhont_last_customer_notification_user', true );
		$template_title = (string) $order->get_meta( '_mhont_last_customer_notification_template', true );

		echo '<div class="notice inline notice-success mhont-last-notification"><p><strong>' . esc_html__( 'Last customer notification:', 'mailhilfe-order-note-manager' ) . '</strong> ';
		echo esc_html( $display_time );
		if ( '' !== $user_name ) {
			echo ' &ndash; ' . esc_html( $user_name );
		}
		if ( '' !== $template_title ) {
			echo ' &ndash; ' . esc_html( $template_title );
		}
		echo '</p></div>';
	}


	/**
	 * Adds an internal audit note after a customer note has been created.
	 *
	 * WooCommerce sends the customer note notification when customer note emails
	 * are enabled. This internal note records when the notification was triggered
	 * from this template action.
	 *
	 * @param WC_Order $order    WooCommerce order.
	 * @param WP_Post  $template Used note template.
	 * @return int Order note ID, or 0 on failure.
	 */
	private static function add_customer_notification_log_note( $order, $template ) {
		$current_user = wp_get_current_user();
		$user_name    = ( $current_user && $current_user->exists() ) ? $current_user->display_name : __( 'Unknown user', 'mailhilfe-order-note-manager' );
		$timestamp    = current_time( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) );
		$template_title = is_a( $template, 'WP_Post' ) ? get_the_title( $template ) : '';

		$log_note = sprintf(
			/* translators: 1: date and time, 2: WordPress user display name, 3: template title. */
			__( 'Customer notification sent on %1$s by %2$s using template "%3$s".', 'mailhilfe-order-note-manager' ),
			$timestamp,
			$user_name,
			$template_title
		);

		return absint( $order->add_order_note( wp_strip_all_tags( $log_note ), false, true ) );
	}

	/**
	 * Renders newly created WooCommerce order notes for immediate insertion into
	 * the existing order-notes list without reloading the order screen.
	 *
	 * @param int[] $note_ids Order note IDs in creation order.
	 * @return string
	 */
	private static function render_order_notes_html( $note_ids ) {
		if ( ! function_exists( 'wc_get_order_note' ) ) {
			return '';
		}

		$note_ids = array_values( array_filter( array_map( 'absint', (array) $note_ids ) ) );
		if ( empty( $note_ids ) ) {
			return '';
		}

		ob_start();
		foreach ( array_reverse( $note_ids ) as $note_id ) {
			$note = wc_get_order_note( $note_id );
			if ( ! $note || empty( $note->date_created ) ) {
				continue;
			}

			$css_class   = array( 'note' );
			$css_class[] = ! empty( $note->customer_note ) ? 'customer-note' : '';
			$css_class[] = 'system' === $note->added_by ? 'system-note' : '';
			$css_class   = apply_filters( 'woocommerce_order_note_class', array_filter( $css_class ), $note );
			$content     = wp_kses_post( $note->content );
			if ( function_exists( 'wc_wptexturize_order_note' ) ) {
				$content = wc_wptexturize_order_note( $content );
			}
			$note_date_label = $note->date_created->date_i18n( function_exists( 'wc_date_format' ) ? wc_date_format() : get_option( 'date_format' ) );
			$note_time_label = $note->date_created->date_i18n( function_exists( 'wc_time_format' ) ? wc_time_format() : get_option( 'time_format' ) );
			$delete_aria_label = 'system' === $note->added_by
				? sprintf( __( 'Delete system note from %s', 'woocommerce' ), $note_date_label )
				: sprintf( __( 'Delete note from %1$s on %2$s', 'woocommerce' ), $note->added_by, $note_date_label );
			?>
			<li rel="<?php echo absint( $note->id ); ?>" class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
				<div class="note_content">
					<div class="note_body"><?php echo wp_kses_post( wpautop( $content ) ); ?></div>
				</div>
				<p class="meta">
					<abbr class="exact-date" title="<?php echo esc_attr( $note->date_created->date( 'Y-m-d H:i:s' ) ); ?>"><?php echo esc_html( sprintf( __( '%1$s at %2$s', 'woocommerce' ), $note_date_label, $note_time_label ) ); ?></abbr>
					<?php if ( 'system' !== $note->added_by ) : ?><?php echo esc_html( sprintf( ' ' . __( 'by %s', 'woocommerce' ), $note->added_by ) ); ?><?php endif; ?>
					<a href="#" class="delete_note" role="button" aria-label="<?php echo esc_attr( $delete_aria_label ); ?>"><?php esc_html_e( 'Delete', 'woocommerce' ); ?></a>
				</p>
			</li>
			<?php
		}

		return (string) ob_get_clean();
	}

	/**
	 * Checks whether the current user is allowed to edit the given order.
	 *
	 * The plugin capability only controls access to template functionality.
	 * This additional WooCommerce/WordPress capability check prevents a user
	 * who only has the template capability from reading or changing orders.
	 *
	 * @param WC_Order $order WooCommerce order.
	 * @return bool
	 */
	private static function current_user_can_edit_order( $order ) {
		if ( ! $order || ! is_a( $order, 'WC_Order' ) ) {
			return false;
		}

		$order_id = absint( $order->get_id() );
		if ( ! $order_id ) {
			return false;
		}

		// WooCommerce uses the plural edit_shop_orders meta capability for both
		// legacy and HPOS order screens. The edit_post fallback covers legacy
		// post-backed orders without granting the broader manage_woocommerce cap.
		return current_user_can( 'edit_shop_orders', $order_id ) || current_user_can( 'edit_post', $order_id );
	}

	/** Checks configured template conditions against an order. */
	private static function template_matches_conditions( $template, $order ) {
		$conditions = get_post_meta( $template->ID, '_mhont_conditions', true );
		if ( ! is_array( $conditions ) || empty( array_filter( $conditions ) ) ) { return (bool) apply_filters( 'mailhilfe_order_note_conditions_match', true, $template, $order, $conditions ); }
		$match = true;
		if ( ! empty( $conditions['statuses'] ) && ! in_array( $order->get_status(), (array) $conditions['statuses'], true ) ) { $match = false; }
		if ( $match && ! empty( $conditions['payment_methods'] ) && ! in_array( $order->get_payment_method(), (array) $conditions['payment_methods'], true ) ) { $match = false; }
		if ( $match && ! empty( $conditions['countries'] ) && ! in_array( strtoupper( $order->get_billing_country() ), (array) $conditions['countries'], true ) ) { $match = false; }
		if ( $match && ! empty( $conditions['shipping_methods'] ) ) {
			$ids = array(); foreach ( $order->get_shipping_methods() as $item ) { if ( method_exists( $item, 'get_method_id' ) ) { $ids[] = $item->get_method_id(); } }
			if ( ! array_intersect( $ids, (array) $conditions['shipping_methods'] ) ) { $match = false; }
		}
		$total = (float) $order->get_total();
		if ( $match && isset( $conditions['min_total'] ) && '' !== $conditions['min_total'] && $total < (float) $conditions['min_total'] ) { $match = false; }
		if ( $match && isset( $conditions['max_total'] ) && '' !== $conditions['max_total'] && $total > (float) $conditions['max_total'] ) { $match = false; }
		return (bool) apply_filters( 'mailhilfe_order_note_conditions_match', $match, $template, $order, $conditions );
	}

	/** Toggle a per-user favorite template. */
	public static function ajax_toggle_personal_favorite() {
		check_ajax_referer( 'mhont_toggle_personal_favorite', 'nonce' );
		if ( ! current_user_can( MHONT_Capabilities::USE_TEMPLATES ) ) { wp_send_json_error( array( 'message' => __( 'You are not allowed to use note templates.', 'mailhilfe-order-note-manager' ) ), 403 ); }
		$template_id = isset( $_POST['template_id'] ) ? absint( $_POST['template_id'] ) : 0;
		if ( MHONT_Post_Types::POST_TYPE !== get_post_type( $template_id ) ) { wp_send_json_error( array( 'message' => __( 'Template could not be found.', 'mailhilfe-order-note-manager' ) ), 404 ); }
		$favorites = array_map( 'absint', (array) get_user_meta( get_current_user_id(), '_mhont_personal_favorites', true ) );
		if ( in_array( $template_id, $favorites, true ) ) { $favorites = array_values( array_diff( $favorites, array( $template_id ) ) ); $active = false; } else { array_unshift( $favorites, $template_id ); $favorites = array_slice( array_values( array_unique( $favorites ) ), 0, 100 ); $active = true; }
		update_user_meta( get_current_user_id(), '_mhont_personal_favorites', $favorites );
		wp_send_json_success( array( 'active' => $active ) );
	}

}
