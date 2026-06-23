<?php
/**
 * Template post type and taxonomy.
 *
 * @package Mailhilfe_Order_Note_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and manages templates.
 */
final class MHONT_Post_Types {

	const POST_TYPE = 'mhont_template';
	const TAXONOMY  = 'mhont_category';

	/**
	 * Registers hooks.
	 *
	 * @return void
	 */
	public static function hooks() {
		add_action( 'add_meta_boxes_' . self::POST_TYPE, array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post_' . self::POST_TYPE, array( __CLASS__, 'save_meta' ), 10, 2 );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( __CLASS__, 'column_content' ), 10, 2 );
		add_action( 'wp_ajax_mhont_sort_templates', array( __CLASS__, 'ajax_sort_templates' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'set_default_admin_order' ) );
		add_filter( 'post_row_actions', array( __CLASS__, 'row_actions' ), 10, 2 );
		add_action( 'admin_post_mhont_duplicate_template', array( __CLASS__, 'duplicate_template_action' ) );
		add_action( 'wp_ajax_mhont_test_template_preview', array( __CLASS__, 'ajax_test_template_preview' ) );
		add_action( 'wp_restore_post_revision', array( __CLASS__, 'restore_revision_content' ), 10, 2 );
	}

	/**
	 * Registers post type and category taxonomy.
	 *
	 * @return void
	 */
	public static function register() {
		$labels = array(
			'name'               => __( 'Mailhilfe Order Notes', 'mailhilfe-order-note-manager' ),
			'singular_name'      => __( 'Mailhilfe Order Note Manager', 'mailhilfe-order-note-manager' ),
			'menu_name'          => __( 'Mailhilfe Order Notes', 'mailhilfe-order-note-manager' ),
			'add_new'            => __( 'Add New', 'mailhilfe-order-note-manager' ),
			'add_new_item'       => __( 'Add New Template', 'mailhilfe-order-note-manager' ),
			'edit_item'          => __( 'Edit Template', 'mailhilfe-order-note-manager' ),
			'new_item'           => __( 'New Template', 'mailhilfe-order-note-manager' ),
			'view_item'          => __( 'View Template', 'mailhilfe-order-note-manager' ),
			'search_items'       => __( 'Search Templates', 'mailhilfe-order-note-manager' ),
			'not_found'          => __( 'No templates found.', 'mailhilfe-order-note-manager' ),
			'not_found_in_trash' => __( 'No templates found in Trash.', 'mailhilfe-order-note-manager' ),
		);

		register_post_type(
			self::POST_TYPE,
			array(
				'labels'             => $labels,
				'public'             => false,
				'publicly_queryable' => false,
				'exclude_from_search' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_nav_menus'  => false,
				'show_in_rest'       => false,
				'query_var'          => false,
				'rewrite'            => false,
				'menu_icon'          => 'dashicons-media-text',
				'supports'           => array( 'title', 'page-attributes', 'revisions' ),
				'capability_type'    => 'post',
				'capabilities'       => array(
					'edit_post'              => MHONT_Capabilities::MANAGE_TEMPLATES,
					'read_post'              => MHONT_Capabilities::MANAGE_TEMPLATES,
					'delete_post'            => MHONT_Capabilities::MANAGE_TEMPLATES,
					'edit_posts'             => MHONT_Capabilities::MANAGE_TEMPLATES,
					'edit_others_posts'      => MHONT_Capabilities::MANAGE_TEMPLATES,
					'publish_posts'          => MHONT_Capabilities::MANAGE_TEMPLATES,
					'read_private_posts'     => MHONT_Capabilities::MANAGE_TEMPLATES,
					'delete_posts'           => MHONT_Capabilities::MANAGE_TEMPLATES,
					'delete_private_posts'   => MHONT_Capabilities::MANAGE_TEMPLATES,
					'delete_published_posts' => MHONT_Capabilities::MANAGE_TEMPLATES,
					'delete_others_posts'    => MHONT_Capabilities::MANAGE_TEMPLATES,
					'edit_private_posts'     => MHONT_Capabilities::MANAGE_TEMPLATES,
					'edit_published_posts'   => MHONT_Capabilities::MANAGE_TEMPLATES,
					'create_posts'           => MHONT_Capabilities::MANAGE_TEMPLATES,
				),
				'map_meta_cap'       => false,
			)
		);

		register_taxonomy(
			self::TAXONOMY,
			self::POST_TYPE,
			array(
				'labels'            => array(
					'name'          => __( 'Categories', 'mailhilfe-order-note-manager' ),
					'singular_name' => __( 'Category', 'mailhilfe-order-note-manager' ),
					'search_items'  => __( 'Search Categories', 'mailhilfe-order-note-manager' ),
					'all_items'     => __( 'All Categories', 'mailhilfe-order-note-manager' ),
					'edit_item'     => __( 'Edit Category', 'mailhilfe-order-note-manager' ),
					'update_item'   => __( 'Update Category', 'mailhilfe-order-note-manager' ),
					'add_new_item'  => __( 'Add New Category', 'mailhilfe-order-note-manager' ),
					'new_item_name' => __( 'New Category Name', 'mailhilfe-order-note-manager' ),
					'menu_name'     => __( 'Categories', 'mailhilfe-order-note-manager' ),
				),
				'public'            => false,
				'publicly_queryable'=> false,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => false,
				'query_var'         => false,
				'rewrite'           => false,
				'hierarchical'      => true,
				'capabilities'      => array(
					'manage_terms' => MHONT_Capabilities::MANAGE_TEMPLATES,
					'edit_terms'   => MHONT_Capabilities::MANAGE_TEMPLATES,
					'delete_terms' => MHONT_Capabilities::MANAGE_TEMPLATES,
					'assign_terms' => MHONT_Capabilities::MANAGE_TEMPLATES,
				),
			)
		);
	}

	/**
	 * Returns selectable template language options.
	 *
	 * Empty value means the template is available for all order languages.
	 *
	 * @return array<string,string>
	 */
	private static function get_language_options() {
		return array(
			''             => __( 'All languages', 'mailhilfe-order-note-manager' ),
			'de_DE'        => 'Deutsch (Deutschland)',
			'de_DE_formal' => 'Deutsch (Sie)',
			'en_US'        => 'English (United States)',
			'fr_FR'        => 'Français',
			'es_ES'        => 'Español',
			'it_IT'        => 'Italiano',
			'pt_BR'        => 'Português (Brasil)',
			'nl_NL'        => 'Nederlands',
			'pl_PL'        => 'Polski',
			'cs_CZ'        => 'Čeština',
			'ru_RU'        => 'Русский',
			'zh_CN'        => '简体中文',
			'ja'           => '日本語',
			'ko_KR'        => '한국어',
			'tr_TR'        => 'Türkçe',
			'fa_IR'        => 'فارسی',
			'ar'           => 'العربية',
			'hi_IN'        => 'हिन्दी',
			'id_ID'        => 'Bahasa Indonesia',
			'vi'           => 'Tiếng Việt',
			'th'           => 'ไทย',
			'uk'           => 'Українська',
			'sv_SE'        => 'Svenska',
			'da_DK'        => 'Dansk',
		);
	}

	/**
	 * Validates a stored template language value.
	 *
	 * @param string $language Language/locale value.
	 * @return string
	 */
	public static function sanitize_template_language( $language ) {
		if ( ! is_string( $language ) ) {
			return '';
		}

		$language = str_replace( '-', '_', trim( $language ) );
		if ( '' === $language ) {
			return '';
		}

		foreach ( array_keys( self::get_language_options() ) as $allowed_locale ) {
			if ( '' !== $allowed_locale && 0 === strcasecmp( $allowed_locale, $language ) ) {
				return $allowed_locale;
			}
		}

		return '';
	}

	/**
	 * Adds template meta box.
	 *
	 * @return void
	 */
	public static function add_meta_boxes() {
		add_meta_box(
			'mhont_template_content',
			__( 'Template Settings', 'mailhilfe-order-note-manager' ),
			array( __CLASS__, 'render_meta_box' ),
			self::POST_TYPE,
			'normal',
			'high'
		);
	}

	/**
	 * Renders template meta box.
	 *
	 * @param WP_Post $post Post object.
	 * @return void
	 */
	public static function render_meta_box( $post ) {
		if ( ! class_exists( 'MHONT_Placeholders', false ) ) {
			require_once MHONT_PATH . 'includes/class-mhont-placeholders.php';
		}
		wp_nonce_field( 'mhont_save_template', 'mhont_template_nonce' );

		$note_type = get_post_meta( $post->ID, '_mhont_note_type', true );
		if ( ! in_array( $note_type, array( 'private', 'customer' ), true ) ) {
			$note_type = class_exists( 'MHONT_Settings' ) ? (string) MHONT_Settings::get( 'default_note_type' ) : 'private';
		}
		$content = get_post_meta( $post->ID, '_mhont_content', true );
		if ( '' === $content && '' !== (string) $post->post_content ) {
			$content = (string) $post->post_content;
		}
		$favorite  = get_post_meta( $post->ID, '_mhont_favorite', true );
		$language  = self::sanitize_template_language( (string) get_post_meta( $post->ID, '_mhont_language', true ) );
		if ( ! in_array( $note_type, array( 'private', 'customer' ), true ) ) {
			$note_type = 'private';
		}
		?>
		<p>
			<label for="mhont_note_type"><strong><?php esc_html_e( 'Default note type', 'mailhilfe-order-note-manager' ); ?></strong></label><br>
			<select name="mhont_note_type" id="mhont_note_type">
				<option value="private" <?php selected( $note_type, 'private' ); ?>><?php esc_html_e( 'Internal note', 'mailhilfe-order-note-manager' ); ?></option>
				<option value="customer" <?php selected( $note_type, 'customer' ); ?>><?php esc_html_e( 'Customer note', 'mailhilfe-order-note-manager' ); ?></option>
			</select>
		</p>

		<p>
			<label>
				<input type="checkbox" name="mhont_favorite" value="yes" <?php checked( $favorite, 'yes' ); ?>>
				<strong><?php esc_html_e( 'Mark as favorite', 'mailhilfe-order-note-manager' ); ?></strong>
			</label>
		</p>
		<p>
			<label for="mhont_language"><strong><?php esc_html_e( 'Template language', 'mailhilfe-order-note-manager' ); ?></strong></label><br>
			<select name="mhont_language" id="mhont_language" class="regular-text">
				<?php foreach ( self::get_language_options() as $locale => $label ) : ?>
					<option value="<?php echo esc_attr( $locale ); ?>" <?php selected( $language, $locale ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
			<span class="description"><?php esc_html_e( 'Leave empty to show this template for all languages. WPML and Polylang order language values can be matched here.', 'mailhilfe-order-note-manager' ); ?></span>
		</p>


		<hr>
		<h3><?php esc_html_e( 'Template conditions', 'mailhilfe-order-note-manager' ); ?></h3>
		<?php
		$conditions = get_post_meta( $post->ID, '_mhont_conditions', true );
		$conditions = is_array( $conditions ) ? $conditions : array();
		$statuses = isset( $conditions['statuses'] ) && is_array( $conditions['statuses'] ) ? $conditions['statuses'] : array();
		$payment_methods = isset( $conditions['payment_methods'] ) && is_array( $conditions['payment_methods'] ) ? $conditions['payment_methods'] : array();
		$shipping_methods = isset( $conditions['shipping_methods'] ) && is_array( $conditions['shipping_methods'] ) ? $conditions['shipping_methods'] : array();
		$countries = isset( $conditions['countries'] ) && is_array( $conditions['countries'] ) ? $conditions['countries'] : array();
		?>
		<p><label for="mhont_condition_statuses"><strong><?php esc_html_e( 'Allowed order statuses', 'mailhilfe-order-note-manager' ); ?></strong></label><br>
		<select name="mhont_conditions[statuses][]" id="mhont_condition_statuses" multiple class="regular-text" size="5">
		<?php if ( function_exists( 'wc_get_order_statuses' ) ) : foreach ( wc_get_order_statuses() as $status_key => $status_label ) : $status_value = 0 === strpos( $status_key, 'wc-' ) ? substr( $status_key, 3 ) : $status_key; ?>
		<option value="<?php echo esc_attr( $status_value ); ?>" <?php selected( in_array( $status_value, $statuses, true ) ); ?>><?php echo esc_html( $status_label ); ?></option>
		<?php endforeach; endif; ?>
		</select></p>
		<p><label for="mhont_condition_payment"><strong><?php esc_html_e( 'Allowed payment method IDs', 'mailhilfe-order-note-manager' ); ?></strong></label><br>
		<input type="text" name="mhont_conditions[payment_methods]" id="mhont_condition_payment" class="regular-text" value="<?php echo esc_attr( implode( ', ', $payment_methods ) ); ?>"><br><span class="description"><?php esc_html_e( 'Comma-separated, for example bacs, paypal, stripe.', 'mailhilfe-order-note-manager' ); ?></span></p>
		<p><label for="mhont_condition_shipping"><strong><?php esc_html_e( 'Allowed shipping method IDs', 'mailhilfe-order-note-manager' ); ?></strong></label><br>
		<input type="text" name="mhont_conditions[shipping_methods]" id="mhont_condition_shipping" class="regular-text" value="<?php echo esc_attr( implode( ', ', $shipping_methods ) ); ?>"></p>
		<p><label for="mhont_condition_countries"><strong><?php esc_html_e( 'Allowed billing countries', 'mailhilfe-order-note-manager' ); ?></strong></label><br>
		<input type="text" name="mhont_conditions[countries]" id="mhont_condition_countries" class="regular-text" value="<?php echo esc_attr( implode( ', ', $countries ) ); ?>"><br><span class="description"><?php esc_html_e( 'Comma-separated ISO country codes, for example DE, AT, CH.', 'mailhilfe-order-note-manager' ); ?></span></p>
		<p><label><?php esc_html_e( 'Minimum order total', 'mailhilfe-order-note-manager' ); ?> <input type="number" step="0.01" min="0" name="mhont_conditions[min_total]" value="<?php echo esc_attr( isset( $conditions['min_total'] ) ? $conditions['min_total'] : '' ); ?>"></label>
		&nbsp; <label><?php esc_html_e( 'Maximum order total', 'mailhilfe-order-note-manager' ); ?> <input type="number" step="0.01" min="0" name="mhont_conditions[max_total]" value="<?php echo esc_attr( isset( $conditions['max_total'] ) ? $conditions['max_total'] : '' ); ?>"></label></p>
		<hr>
		<h3><?php esc_html_e( 'Test order preview', 'mailhilfe-order-note-manager' ); ?></h3>
		<p><label for="mhont_test_order_id"><?php esc_html_e( 'Order ID', 'mailhilfe-order-note-manager' ); ?></label> <input type="number" min="1" id="mhont_test_order_id"> <button type="button" class="button" id="mhont_test_preview_button" data-template-id="<?php echo esc_attr( $post->ID ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'mhont_test_preview_' . $post->ID ) ); ?>"><?php esc_html_e( 'Generate test preview', 'mailhilfe-order-note-manager' ); ?></button></p>
		<div id="mhont_test_preview_result" class="mhont-preview" hidden></div>
		<div class="mhont-editor-field">
			<label for="mhont_content_editor"><strong><?php esc_html_e( 'Template text', 'mailhilfe-order-note-manager' ); ?></strong></label>
			<?php
			wp_editor(
				$content,
				'mhont_content_editor',
				array(
					'textarea_name' => 'mhont_content',
					'textarea_rows' => 12,
					'media_buttons' => false,
					'teeny'         => false,
					'tinymce'       => true,
					'quicktags'     => true,
				)
			);
			?>
		</div>
		<div class="mhont-placeholders">
			<p><strong><?php esc_html_e( 'Available placeholders:', 'mailhilfe-order-note-manager' ); ?></strong></p>
			<ul>
				<?php foreach ( MHONT_Placeholders::get_definitions() as $placeholder => $label ) : ?>
					<li><button type="button" class="button button-small mhont-insert-placeholder" data-placeholder="<?php echo esc_attr( $placeholder ); ?>"><code><?php echo esc_html( $placeholder ); ?></code></button> <span><?php echo esc_html( $label ); ?></span></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Saves template meta.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @return void
	 */
	public static function save_meta( $post_id, $post ) {
		if ( ! isset( $_POST['mhont_template_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mhont_template_nonce'] ) ), 'mhont_save_template' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( self::POST_TYPE !== $post->post_type || ! current_user_can( MHONT_Capabilities::MANAGE_TEMPLATES ) ) {
			return;
		}

		$note_type = isset( $_POST['mhont_note_type'] ) ? sanitize_key( wp_unslash( $_POST['mhont_note_type'] ) ) : 'private';
		if ( ! in_array( $note_type, array( 'private', 'customer' ), true ) ) {
			$note_type = 'private';
		}

		$content = isset( $_POST['mhont_content'] ) ? wp_kses_post( wp_unslash( $_POST['mhont_content'] ) ) : '';
		if ( function_exists( 'mb_substr' ) ) {
			$content = mb_substr( $content, 0, 100000 );
		} else {
			$content = substr( $content, 0, 100000 );
		}
		$favorite = isset( $_POST['mhont_favorite'] ) && 'yes' === sanitize_key( wp_unslash( $_POST['mhont_favorite'] ) ) ? 'yes' : 'no';
		$language = isset( $_POST['mhont_language'] ) ? sanitize_text_field( wp_unslash( $_POST['mhont_language'] ) ) : '';
		$language = self::sanitize_template_language( $language );


		$raw_conditions = isset( $_POST['mhont_conditions'] ) && is_array( $_POST['mhont_conditions'] ) ? wp_unslash( $_POST['mhont_conditions'] ) : array();
		$sanitize_csv = static function ( $value, $uppercase = false, $allow_instance_id = false ) {
			$items = is_string( $value ) ? explode( ',', $value ) : ( is_array( $value ) ? $value : array() );
			$out = array();
			foreach ( $items as $item ) {
				if ( ! is_scalar( $item ) ) {
					continue;
				}
				if ( $uppercase ) {
					$item = strtoupper( sanitize_text_field( (string) $item ) );
				} elseif ( $allow_instance_id ) {
					$item = strtolower( trim( (string) $item ) );
					$item = preg_replace( '/[^a-z0-9_:-]/', '', $item );
				} else {
					$item = sanitize_key( (string) $item );
				}
				if ( $uppercase && 2 !== strlen( $item ) ) {
					continue;
				}
				if ( '' !== $item ) {
					$out[] = $item;
				}
			}
			return array_values( array_unique( $out ) );
		};
		$conditions = array(
			'statuses'         => $sanitize_csv( isset( $raw_conditions['statuses'] ) ? $raw_conditions['statuses'] : array() ),
			'payment_methods'  => $sanitize_csv( isset( $raw_conditions['payment_methods'] ) ? $raw_conditions['payment_methods'] : '' ),
			'shipping_methods' => $sanitize_csv( isset( $raw_conditions['shipping_methods'] ) ? $raw_conditions['shipping_methods'] : '', false, true ),
			'countries'        => $sanitize_csv( isset( $raw_conditions['countries'] ) ? $raw_conditions['countries'] : '', true ),
			'min_total'        => isset( $raw_conditions['min_total'] ) && is_scalar( $raw_conditions['min_total'] ) && '' !== (string) $raw_conditions['min_total'] ? (string) max( 0, (float) $raw_conditions['min_total'] ) : '',
			'max_total'        => isset( $raw_conditions['max_total'] ) && is_scalar( $raw_conditions['max_total'] ) && '' !== (string) $raw_conditions['max_total'] ? (string) max( 0, (float) $raw_conditions['max_total'] ) : '',
		);
		update_post_meta( $post_id, '_mhont_note_type', $note_type );
		update_post_meta( $post_id, '_mhont_content', $content );
		update_post_meta( $post_id, '_mhont_favorite', $favorite );
		update_post_meta( $post_id, '_mhont_language', $language );
		update_post_meta( $post_id, '_mhont_conditions', $conditions );

		remove_action( 'save_post_' . self::POST_TYPE, array( __CLASS__, 'save_meta' ), 10 );
		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_content' => wp_slash( $content ),
			)
		);
		add_action( 'save_post_' . self::POST_TYPE, array( __CLASS__, 'save_meta' ), 10, 2 );
	}

	/**
	 * Synchronizes restored WordPress revision content with the template meta field.
	 *
	 * @param int $post_id     Restored template post ID.
	 * @param int $revision_id Revision post ID.
	 * @return void
	 */
	public static function restore_revision_content( $post_id, $revision_id ) {
		if ( self::POST_TYPE !== get_post_type( $post_id ) ) {
			return;
		}

		$revision = get_post( $revision_id );
		if ( ! $revision ) {
			return;
		}

		$content = wp_kses_post( (string) $revision->post_content );
		if ( function_exists( 'mb_substr' ) ) {
			$content = mb_substr( $content, 0, 100000 );
		} else {
			$content = substr( $content, 0, 100000 );
		}

		update_post_meta( $post_id, '_mhont_content', $content );
	}


	/**
	 * Adds admin columns.
	 *
	 * @param array $columns Columns.
	 * @return array
	 */
	public static function columns( $columns ) {
		$columns['mhont_drag']        = __( 'Sort', 'mailhilfe-order-note-manager' );
		$columns['mhont_favorite']    = __( 'Favorite', 'mailhilfe-order-note-manager' );
		$columns['mhont_note_type']   = __( 'Note type', 'mailhilfe-order-note-manager' );
		$columns['mhont_language']    = __( 'Template language', 'mailhilfe-order-note-manager' );
		if ( ! class_exists( 'MHONT_Settings' ) || MHONT_Settings::enabled( 'show_usage_count' ) ) {
			$columns['mhont_usage_count'] = __( 'Usage', 'mailhilfe-order-note-manager' );
		}
		return $columns;
	}

	/**
	 * Renders custom column content.
	 *
	 * @param string $column  Column name.
	 * @param int    $post_id Post ID.
	 * @return void
	 */
	public static function column_content( $column, $post_id ) {
		if ( 'mhont_drag' === $column ) {
			echo '<span class="dashicons dashicons-menu mhont-sort-handle" title="' . esc_attr__( 'Drag to sort', 'mailhilfe-order-note-manager' ) . '"></span>';
			return;
		}

		if ( 'mhont_favorite' === $column ) {
			$is_favorite = 'yes' === get_post_meta( $post_id, '_mhont_favorite', true );
			echo '<span class="dashicons ' . esc_attr( $is_favorite ? 'dashicons-star-filled' : 'dashicons-star-empty' ) . '" aria-hidden="true"></span>';
			echo '<span class="screen-reader-text">' . esc_html( $is_favorite ? __( 'Favorite', 'mailhilfe-order-note-manager' ) : __( 'Not favorite', 'mailhilfe-order-note-manager' ) ) . '</span>';
			return;
		}

		if ( 'mhont_language' === $column ) {
			$language = self::sanitize_template_language( (string) get_post_meta( $post_id, '_mhont_language', true ) );
			$options  = self::get_language_options();
			echo esc_html( isset( $options[ $language ] ) ? $options[ $language ] : $language );
			return;
		}

		if ( 'mhont_usage_count' === $column ) {
			echo esc_html( absint( get_post_meta( $post_id, '_mhont_usage_count', true ) ) );
			return;
		}

		if ( 'mhont_note_type' === $column ) {
			$note_type = get_post_meta( $post_id, '_mhont_note_type', true );
			echo esc_html( 'customer' === $note_type ? __( 'Customer note', 'mailhilfe-order-note-manager' ) : __( 'Internal note', 'mailhilfe-order-note-manager' ) );
		}
	}

	/**
	 * Uses the saved manual order on the unfiltered template overview.
	 *
	 * @param WP_Query $query Current query.
	 * @return void
	 */
	public static function set_default_admin_order( $query ) {
		if ( ! is_admin() || ! $query instanceof WP_Query || ! $query->is_main_query() ) {
			return;
		}

		$post_type = $query->get( 'post_type' );
		if ( self::POST_TYPE !== $post_type ) {
			return;
		}

		// Respect explicit sorting selected by the user or another plugin.
		if ( isset( $_GET['orderby'] ) && '' !== sanitize_key( wp_unslash( $_GET['orderby'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only list-table sorting parameter.
			return;
		}

		$query->set(
			'orderby',
			array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			)
		);
		$query->set( 'order', 'ASC' );
	}

	/**
	 * Saves drag-and-drop sorting.
	 *
	 * @return void
	 */
	public static function ajax_sort_templates() {
		check_ajax_referer( 'mhont_sort_templates', 'nonce' );

		if ( ! current_user_can( MHONT_Capabilities::MANAGE_TEMPLATES ) ) {
			wp_send_json_error( array( 'message' => __( 'You are not allowed to manage note templates.', 'mailhilfe-order-note-manager' ) ), 403 );
		}

		$order = isset( $_POST['order'] ) && is_array( $_POST['order'] ) ? array_map( 'absint', wp_unslash( $_POST['order'] ) ) : array();
		$order = array_slice( array_values( array_unique( array_filter( $order ) ) ), 0, 500 );
		if ( empty( $order ) ) {
			wp_send_json_error( array( 'message' => __( 'No template order was received.', 'mailhilfe-order-note-manager' ) ), 400 );
		}

		$original_orders = array();
		foreach ( $order as $post_id ) {
			if ( ! $post_id || self::POST_TYPE !== get_post_type( $post_id ) ) {
				wp_send_json_error( array( 'message' => __( 'The template order could not be saved.', 'mailhilfe-order-note-manager' ) ), 400 );
			}
			$original_orders[ $post_id ] = (int) get_post_field( 'menu_order', $post_id );
		}

		$index       = 0;
		$updated     = 0;
		$changed_ids = array();
		foreach ( $order as $post_id ) {
			$result = wp_update_post(
				array(
					'ID'         => $post_id,
					'menu_order' => $index,
				),
				true
			);
			if ( is_wp_error( $result ) ) {
				// Avoid leaving a partially saved order if one update fails midway.
				foreach ( $changed_ids as $changed_id ) {
					wp_update_post(
						array(
							'ID'         => $changed_id,
							'menu_order' => $original_orders[ $changed_id ],
						)
					);
				}
				wp_send_json_error( array( 'message' => __( 'The template order could not be saved.', 'mailhilfe-order-note-manager' ) ), 500 );
			}

			$changed_ids[] = $post_id;
			$index += 10;
			++$updated;
		}

		if ( 0 === $updated ) {
			wp_send_json_error( array( 'message' => __( 'The template order could not be saved.', 'mailhilfe-order-note-manager' ) ), 400 );
		}

		if ( class_exists( 'MHONT_Template_Cache' ) ) {
			MHONT_Template_Cache::clear();
		}

		wp_send_json_success( array( 'message' => __( 'Sorting saved.', 'mailhilfe-order-note-manager' ) ) );
	}

	/**
	 * Adds row actions to duplicate templates quickly.
	 *
	 * @param array   $actions Existing row actions.
	 * @param WP_Post $post    Post object.
	 * @return array
	 */
	public static function row_actions( $actions, $post ) {
		if ( ! is_a( $post, 'WP_Post' ) || self::POST_TYPE !== $post->post_type || ! current_user_can( MHONT_Capabilities::MANAGE_TEMPLATES ) ) {
			return $actions;
		}

		$url = wp_nonce_url(
			admin_url( 'admin-post.php?action=mhont_duplicate_template&template_id=' . absint( $post->ID ) ),
			'mhont_duplicate_template_' . absint( $post->ID ),
			'mhont_nonce'
		);

		$actions['mhont_duplicate'] = sprintf( '<a href="%1$s">%2$s</a>', esc_url( $url ), esc_html__( 'Duplicate', 'mailhilfe-order-note-manager' ) );
		return $actions;
	}

	/**
	 * Duplicates a template including metadata and categories.
	 *
	 * @return void
	 */
	public static function duplicate_template_action() {
		$template_id = isset( $_GET['template_id'] ) ? absint( $_GET['template_id'] ) : 0;
		if ( ! $template_id || ! isset( $_GET['mhont_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['mhont_nonce'] ) ), 'mhont_duplicate_template_' . $template_id ) ) {
			wp_die( esc_html__( 'Security check failed.', 'mailhilfe-order-note-manager' ), '', array( 'response' => 403 ) );
		}

		if ( ! current_user_can( MHONT_Capabilities::MANAGE_TEMPLATES ) ) {
			wp_die( esc_html__( 'You are not allowed to manage note templates.', 'mailhilfe-order-note-manager' ), '', array( 'response' => 403 ) );
		}

		$template = get_post( $template_id );
		if ( ! $template || self::POST_TYPE !== $template->post_type ) {
			wp_die( esc_html__( 'Template could not be found.', 'mailhilfe-order-note-manager' ), '', array( 'response' => 404 ) );
		}

		$new_id = wp_insert_post(
			wp_slash(
				array(
					'post_type'    => self::POST_TYPE,
					'post_status'  => 'draft',
					'post_title'   => sprintf( /* translators: %s: original template title. */ __( 'Copy of %s', 'mailhilfe-order-note-manager' ), get_the_title( $template ) ),
					'post_content' => get_post_meta( $template_id, '_mhont_content', true ),
					'menu_order'   => (int) $template->menu_order + 1,
				)
			),
			true
		);

		if ( is_wp_error( $new_id ) || ! $new_id ) {
			wp_die( esc_html__( 'Template could not be duplicated.', 'mailhilfe-order-note-manager' ), '', array( 'response' => 500 ) );
		}

		foreach ( array( '_mhont_content', '_mhont_note_type', '_mhont_favorite', '_mhont_language', '_mhont_conditions' ) as $meta_key ) {
			update_post_meta( $new_id, $meta_key, get_post_meta( $template_id, $meta_key, true ) );
		}
		update_post_meta( $new_id, '_mhont_usage_count', 0 );

		$terms = wp_get_post_terms( $template_id, self::TAXONOMY, array( 'fields' => 'ids' ) );
		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			wp_set_object_terms( $new_id, array_map( 'absint', $terms ), self::TAXONOMY, false );
		}

		$redirect_url = get_edit_post_link( $new_id, 'raw' );
		if ( ! is_string( $redirect_url ) || '' === $redirect_url ) {
			$redirect_url = admin_url( 'edit.php?post_type=' . self::POST_TYPE );
		}

		wp_safe_redirect( $redirect_url );
		exit;
	}


	/** AJAX preview using a real order while editing a template. */
	public static function ajax_test_template_preview() {
		$template_id = isset( $_POST['template_id'] ) ? absint( $_POST['template_id'] ) : 0;
		$order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! $template_id || ! wp_verify_nonce( $nonce, 'mhont_test_preview_' . $template_id ) || ! current_user_can( MHONT_Capabilities::MANAGE_TEMPLATES ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'mailhilfe-order-note-manager' ) ), 403 );
		}
		if ( ! function_exists( 'wc_get_order' ) ) { wp_send_json_error( array( 'message' => __( 'WooCommerce is required for this action.', 'mailhilfe-order-note-manager' ) ), 503 ); }
		$template = get_post( $template_id );
		$order    = wc_get_order( $order_id );
		if ( ! $template || self::POST_TYPE !== $template->post_type ) {
			wp_send_json_error( array( 'message' => __( 'Template or order could not be found.', 'mailhilfe-order-note-manager' ) ), 404 );
		}
		if ( ! $order || ( ! current_user_can( 'edit_shop_orders', $order_id ) && ! current_user_can( 'edit_post', $order_id ) ) ) { wp_send_json_error( array( 'message' => __( 'Order could not be loaded.', 'mailhilfe-order-note-manager' ) ), 404 ); }
		$content = isset( $_POST['content'] ) && is_string( $_POST['content'] ) ? wp_unslash( $_POST['content'] ) : get_post_meta( $template_id, '_mhont_content', true );
		if ( ! is_string( $content ) || strlen( $content ) > 100000 ) {
			wp_send_json_error( array( 'message' => __( 'The edited note is too large.', 'mailhilfe-order-note-manager' ) ), 413 );
		}
		$preview = apply_filters( 'mailhilfe_order_note_preview_content', MHONT_Placeholders::replace( $content, $order ), $order, $template );
		if ( ! is_scalar( $preview ) || strlen( (string) $preview ) > 100000 ) {
			wp_send_json_error( array( 'message' => __( 'The edited note is too large.', 'mailhilfe-order-note-manager' ) ), 413 );
		}
		$allow_html = ! class_exists( 'MHONT_Settings' ) || MHONT_Settings::enabled( 'allow_html' );
		$preview    = $allow_html ? wp_kses_post( wpautop( (string) $preview ) ) : self::html_to_plain_text( (string) $preview );
		wp_send_json_success( array( 'preview' => $preview, 'preview_html' => $allow_html ) );
	}

	/**
	 * Converts HTML or encoded HTML to readable plain text for test previews.
	 *
	 * @param string $value HTML or text value.
	 * @return string
	 */
	private static function html_to_plain_text( $value ) {
		$value = html_entity_decode( (string) $value, ENT_QUOTES | ENT_HTML5, get_bloginfo( 'charset' ) );
		$value = preg_replace( '/<\s*br\s*\/?\s*>/i', "\n", $value );
		$value = preg_replace( '/<\/\s*(?:p|div|li|h[1-6]|tr)\s*>/i', "\n", $value );
		$value = wp_strip_all_tags( $value );
		$value = preg_replace( "/[ \t]+\n/", "\n", $value );
		$value = preg_replace( "/\n{3,}/", "\n\n", $value );

		return sanitize_textarea_field( trim( $value ) );
	}

}
