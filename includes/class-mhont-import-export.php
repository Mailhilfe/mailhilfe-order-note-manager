<?php
/**
 * JSON import/export and demo templates.
 *
 * @package Mailhilfe_Order_Note_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles JSON import/export and demo templates.
 */
final class MHONT_Import_Export {

	/**
	 * Registers hooks.
	 *
	 * @return void
	 */
	public static function hooks() {
		add_action( 'admin_menu', array( __CLASS__, 'add_submenu' ) );
		add_action( 'admin_post_mhont_export_json', array( __CLASS__, 'export_json' ) );
		add_action( 'admin_post_mhont_import_json', array( __CLASS__, 'import_json' ) );
		add_action( 'admin_post_mhont_confirm_import_json', array( __CLASS__, 'confirm_import_json' ) );
		add_action( 'admin_post_mhont_install_demo_templates', array( __CLASS__, 'install_demo_templates_action' ) );
	}

	/**
	 * Adds import/export submenu.
	 *
	 * @return void
	 */
	public static function add_submenu() {
		add_submenu_page(
			'edit.php?post_type=' . MHONT_Post_Types::POST_TYPE,
			__( 'Mailhilfe Order Note Manager Import/Export', 'mailhilfe-order-note-manager' ),
			__( 'Template Import/Export', 'mailhilfe-order-note-manager' ),
			MHONT_Capabilities::MANAGE_TEMPLATES,
			'mhont-import-export',
			array( __CLASS__, 'render_page' )
		);
	}

	/**
	 * Renders import/export page.
	 *
	 * @return void
	 */
	public static function render_page() {
		if ( ! current_user_can( MHONT_Capabilities::MANAGE_TEMPLATES ) ) {
			wp_die( esc_html__( 'You are not allowed to manage note templates.', 'mailhilfe-order-note-manager' ), '', array( 'response' => 403 ) );
		}

		$message = self::get_admin_message();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Mailhilfe Order Note Manager Import/Export', 'mailhilfe-order-note-manager' ); ?></h1>

			<?php if ( 'imported' === $message ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Templates imported successfully.', 'mailhilfe-order-note-manager' ); ?></p></div>
			<?php elseif ( 'demo-installed' === $message ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Demo templates installed successfully.', 'mailhilfe-order-note-manager' ); ?></p></div>
			<?php elseif ( 'error' === $message ) : ?>
				<div class="notice notice-error is-dismissible"><p><?php esc_html_e( 'The JSON file could not be imported.', 'mailhilfe-order-note-manager' ); ?></p></div>
			<?php endif; ?>

			<?php self::render_import_preview(); ?>

			<div class="mhont-tools-grid">
				<div class="mhont-tool-card">
					<h2><?php esc_html_e( 'Export templates', 'mailhilfe-order-note-manager' ); ?></h2>
					<p><?php esc_html_e( 'Download all published note templates, categories, favorites, sorting values and usage counters as a JSON file.', 'mailhilfe-order-note-manager' ); ?></p>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="mhont_export_json">
						<?php wp_nonce_field( 'mhont_export_json', 'mhont_nonce' ); ?>
						<?php submit_button( __( 'Export JSON', 'mailhilfe-order-note-manager' ), 'primary', 'submit', false ); ?>
					</form>
				</div>

				<div class="mhont-tool-card">
					<h2><?php esc_html_e( 'Import templates', 'mailhilfe-order-note-manager' ); ?></h2>
					<p><?php esc_html_e( 'Upload a JSON export file to review a preview before the import is applied. Existing templates with the same title will be updated.', 'mailhilfe-order-note-manager' ); ?></p>
					<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="mhont_import_json">
						<?php wp_nonce_field( 'mhont_import_json', 'mhont_nonce' ); ?>
						<p><input type="file" name="mhont_json_file" accept="application/json,.json" required></p>
						<?php submit_button( __( 'Import JSON', 'mailhilfe-order-note-manager' ), 'secondary', 'submit', false ); ?>
					</form>
				</div>

				<div class="mhont-tool-card">
					<h2><?php esc_html_e( 'Demo templates', 'mailhilfe-order-note-manager' ); ?></h2>
					<p><?php esc_html_e( 'Install practical demo templates for shipping updates, payment reminders and customer service notes.', 'mailhilfe-order-note-manager' ); ?></p>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="mhont_install_demo_templates">
						<?php wp_nonce_field( 'mhont_install_demo_templates', 'mhont_nonce' ); ?>
						<?php submit_button( __( 'Install demo templates', 'mailhilfe-order-note-manager' ), 'secondary', 'submit', false ); ?>
					</form>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Exports templates as JSON.
	 *
	 * @return void
	 */
	public static function export_json() {
		self::verify_admin_action( 'mhont_export_json' );

		$templates = get_posts(
			array(
				'post_type'        => MHONT_Post_Types::POST_TYPE,
				'post_status'      => 'publish',
				'numberposts'      => -1,
				'orderby'          => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
				'suppress_filters' => true,
			)
		);

		$data = array(
			'plugin'    => 'mailhilfe-order-note-manager',
			'version'   => MHONT_VERSION,
			'exported'  => gmdate( 'c' ),
			'templates' => array(),
		);

		foreach ( $templates as $template ) {
			$terms = wp_get_post_terms( $template->ID, MHONT_Post_Types::TAXONOMY, array( 'fields' => 'names' ) );
			$data['templates'][] = array(
				'title'       => get_the_title( $template ),
				'content'     => get_post_meta( $template->ID, '_mhont_content', true ),
				'note_type'   => self::normalize_note_type( get_post_meta( $template->ID, '_mhont_note_type', true ) ),
				'favorite'    => 'yes' === get_post_meta( $template->ID, '_mhont_favorite', true ),
				'usage_count' => absint( get_post_meta( $template->ID, '_mhont_usage_count', true ) ),
				'menu_order'  => (int) $template->menu_order,
				'language'    => get_post_meta( $template->ID, '_mhont_language', true ),
				'conditions'  => get_post_meta( $template->ID, '_mhont_conditions', true ),
				'categories'  => is_wp_error( $terms ) ? array() : array_values( array_map( 'sanitize_text_field', $terms ) ),
			);
		}

		$json = wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE );
		if ( false === $json ) {
			wp_die( esc_html__( 'The JSON file could not be exported.', 'mailhilfe-order-note-manager' ), '', array( 'response' => 500 ) );
		}

		$filename = 'mailhilfe-order-note-manager-' . gmdate( 'Y-m-d-H-i-s' ) . '.json';
		nocache_headers();
		header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'X-Content-Type-Options: nosniff' );
		echo $json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON output is encoded by wp_json_encode().
		exit;
	}

	/**
	 * Imports templates from JSON.
	 *
	 * @return void
	 */
	public static function import_json() {
		self::verify_admin_action( 'mhont_import_json' );

		if ( class_exists( 'MHONT_Settings' ) && ! MHONT_Settings::enabled( 'allow_json_import' ) ) {
			self::redirect_with_message( 'error' );
		}

		$data = self::read_uploaded_json_file();
		if ( empty( $data['templates'] ) || ! is_array( $data['templates'] ) ) {
			self::redirect_with_message( 'error' );
		}

		$templates = array_slice( $data['templates'], 0, 200 );
		$summary   = self::build_import_summary( $templates );
		set_transient(
			self::get_import_transient_key(),
			array(
				'templates' => $templates,
				'summary'   => $summary,
			),
			15 * MINUTE_IN_SECONDS
		);

		self::redirect_with_message( 'preview' );
	}

	/**
	 * Confirms and applies a previously previewed import.
	 *
	 * @return void
	 */
	public static function confirm_import_json() {
		self::verify_admin_action( 'mhont_confirm_import_json' );

		if ( class_exists( 'MHONT_Settings' ) && ! MHONT_Settings::enabled( 'allow_json_import' ) ) {
			self::redirect_with_message( 'error' );
		}

		$payload = get_transient( self::get_import_transient_key() );
		delete_transient( self::get_import_transient_key() );
		if ( ! is_array( $payload ) || empty( $payload['templates'] ) || ! is_array( $payload['templates'] ) ) {
			self::redirect_with_message( 'error' );
		}

		foreach ( $payload['templates'] as $template_data ) {
			self::upsert_template_from_array( is_array( $template_data ) ? $template_data : array() );
		}

		self::redirect_with_message( 'imported' );
	}


	/**
	 * Renders an import preview card when an uploaded JSON file is waiting for confirmation.
	 *
	 * @return void
	 */
	private static function render_import_preview() {
		$payload = get_transient( self::get_import_transient_key() );
		if ( ! is_array( $payload ) || empty( $payload['summary'] ) || ! is_array( $payload['summary'] ) ) {
			return;
		}

		$summary = $payload['summary'];
		?>
		<div class="notice notice-info mhont-import-preview">
			<h2><?php esc_html_e( 'Import preview', 'mailhilfe-order-note-manager' ); ?></h2>
			<p><?php esc_html_e( 'Review the summary before applying the import. Nothing has been changed yet.', 'mailhilfe-order-note-manager' ); ?></p>
			<ul>
				<li>
					<?php
					$total = absint( $summary['total'] );
					/* translators: %d: number of templates found. */
					printf( esc_html( _n( '%d template found.', '%d templates found.', $total, 'mailhilfe-order-note-manager' ) ), $total );
					?>
				</li>
				<li>
					<?php
					$create = absint( $summary['create'] );
					/* translators: %d: number of templates that will be created. */
					printf( esc_html( _n( '%d template will be created.', '%d templates will be created.', $create, 'mailhilfe-order-note-manager' ) ), $create );
					?>
				</li>
				<li>
					<?php
					$update = absint( $summary['update'] );
					/* translators: %d: number of templates that will be updated. */
					printf( esc_html( _n( '%d template will be updated.', '%d templates will be updated.', $update, 'mailhilfe-order-note-manager' ) ), $update );
					?>
				</li>
				<li>
					<?php
					$skip = absint( $summary['skip'] );
					/* translators: %d: number of templates that will be skipped. */
					printf( esc_html( _n( '%d template will be skipped.', '%d templates will be skipped.', $skip, 'mailhilfe-order-note-manager' ) ), $skip );
					?>
				</li>
			</ul>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="mhont_confirm_import_json">
				<?php wp_nonce_field( 'mhont_confirm_import_json', 'mhont_nonce' ); ?>
				<?php submit_button( __( 'Apply import now', 'mailhilfe-order-note-manager' ), 'primary', 'submit', false ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Reads and validates the uploaded JSON file.
	 *
	 * @return array
	 */
	private static function read_uploaded_json_file() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce was verified by verify_admin_action(); uploaded file fields are validated below.
		if ( ! isset( $_FILES['mhont_json_file'] ) || ! is_array( $_FILES['mhont_json_file'] ) ) {
			return array();
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce was verified; each file field is validated before use.
		$file  = $_FILES['mhont_json_file']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Individual upload fields are validated below; temporary paths must not be unslashed.
		$name  = isset( $file['name'] ) ? sanitize_file_name( wp_unslash( (string) $file['name'] ) ) : '';
		$tmp   = isset( $file['tmp_name'] ) ? (string) $file['tmp_name'] : '';
		$size  = isset( $file['size'] ) ? absint( $file['size'] ) : 0;
		$error = isset( $file['error'] ) ? absint( $file['error'] ) : UPLOAD_ERR_NO_FILE;
		$allowed_mimes = array( 'json' => 'application/json' );
		$name_type     = wp_check_filetype( $name, $allowed_mimes );
		$checked_type  = wp_check_filetype_and_ext( $tmp, $name, $allowed_mimes );
		$has_json_ext  = isset( $name_type['ext'] ) && 'json' === $name_type['ext'];
		$real_ext_ok   = empty( $checked_type['ext'] ) || 'json' === $checked_type['ext'];

		// Some hosts identify JSON uploads as text/plain. The .json extension is
		// therefore checked separately and the file contents are still required to
		// decode as JSON before any data is accepted.
		if ( UPLOAD_ERR_OK !== $error || empty( $tmp ) || ! is_uploaded_file( $tmp ) || ! $has_json_ext || ! $real_ext_ok || $size < 1 || $size > 1048576 ) {
			return array();
		}

		$raw = file_get_contents( $tmp ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading a validated uploaded JSON temp file.
		if ( false === $raw ) {
			return array();
		}

		$data = json_decode( $raw, true, 20 );
		return is_array( $data ) ? $data : array();
	}

	/**
	 * Builds a non-destructive summary for the import preview.
	 *
	 * @param array $templates Templates from JSON.
	 * @return array<string,int>
	 */
	private static function build_import_summary( $templates ) {
		$summary = array( 'total' => 0, 'create' => 0, 'update' => 0, 'skip' => 0 );
		foreach ( $templates as $template_data ) {
			$summary['total']++;
			$title = is_array( $template_data ) && array_key_exists( 'title', $template_data ) ? self::truncate_string( self::sanitize_scalar_text( $template_data['title'] ), 200 ) : '';
			if ( '' === $title ) {
				$summary['skip']++;
				continue;
			}
			$demo_key     = is_array( $template_data ) && array_key_exists( 'demo_key', $template_data ) ? self::sanitize_scalar_key( $template_data['demo_key'] ) : '';
			$legacy_title = is_array( $template_data ) && array_key_exists( 'legacy_title', $template_data ) ? self::sanitize_scalar_text( $template_data['legacy_title'] ) : '';
			$raw_language = is_array( $template_data ) && array_key_exists( 'language', $template_data ) ? trim( self::scalar_to_string( $template_data['language'] ) ) : '';
			$language     = '' !== $raw_language ? MHONT_Post_Types::sanitize_template_language( $raw_language ) : '';
			if ( '' !== $raw_language && '' === $language ) {
				$summary['skip']++;
				continue;
			}
			$demo_locale  = is_array( $template_data ) && array_key_exists( 'demo_locale', $template_data ) ? self::sanitize_scalar_key( str_replace( '_', '-', self::scalar_to_string( $template_data['demo_locale'] ) ) ) : '';
			if ( '' === $language && '' !== $demo_locale ) {
				$language = MHONT_Post_Types::sanitize_template_language( str_replace( '-', '_', $demo_locale ) );
			}
			$existing_id  = '' !== $demo_key ? self::find_template_by_demo_key( $demo_key ) : 0;
			if ( ! $existing_id && '' !== $legacy_title ) {
				$existing_id = self::find_template_by_title( $legacy_title, null );
			}
			if ( ! $existing_id ) {
				$existing_id = self::find_template_by_title( $title, $language );
			}
			$existing_id ? $summary['update']++ : $summary['create']++;
		}
		return $summary;
	}

	/**
	 * Installs demo templates from admin action.
	 *
	 * @return void
	 */
	public static function install_demo_templates_action() {
		self::verify_admin_action( 'mhont_install_demo_templates' );
		self::install_demo_templates();
		self::redirect_with_message( 'demo-installed' );
	}

	/**
	 * Installs demo templates if no templates exist yet.
	 *
	 * @return void
	 */
	public static function maybe_install_demo_templates() {
		$existing = get_posts(
			array(
				'post_type'        => MHONT_Post_Types::POST_TYPE,
				'post_status'      => array_keys( get_post_stati() ),
				'numberposts'      => 1,
				'suppress_filters' => true,
			)
		);

		if ( empty( $existing ) ) {
			self::install_demo_templates();
		}
	}

	/**
	 * Installs or updates bundled English, German, Spanish, French, Italian, Hindi, Russian, Brazilian Portuguese, Simplified Chinese, Japanese, Dutch, Polish, Turkish, Persian, Vietnamese or Czech demo templates.
	 *
	 * @return void
	 */
	public static function install_demo_templates() {
		$templates = self::get_demo_templates();
		foreach ( $templates as $template ) {
			self::upsert_template_from_array( $template );
		}
	}

	/**
	 * Returns bundled demo templates for English, German, Spanish, French, Italian, Hindi, Russian, Brazilian Portuguese, Simplified Chinese, Japanese, Dutch, Polish, Turkish, Persian, Vietnamese or Czech.
	 *
	 * Demo templates are stored as normal WordPress posts. Because stored post
	 * content is not dynamically translated by WordPress, the plugin creates the
	 * demo records in German, Spanish, French, Italian, Hindi, Russian, Brazilian Portuguese, Simplified Chinese, Japanese, Dutch, Polish, Turkish, Persian, Vietnamese or Czech for matching admin locales and in English otherwise. The stable demo_key
	 * value lets later installs update older English demo templates instead of
	 * creating duplicates.
	 *
	 * @return array[]
	 */
	private static function get_demo_templates() {
		$locale = self::get_demo_locale();
		$sets   = self::get_demo_template_sets();
		$items  = isset( $sets[ $locale ] ) ? $sets[ $locale ] : $sets['en_US'];

		return array(
			array(
				'demo_key'     => 'shipping-update',
				'demo_locale'  => $locale,
				'legacy_title' => 'Demo: Shipping update',
				'title'        => $items['shipping']['title'],
				'content'      => $items['shipping']['content'],
				'note_type'    => 'customer',
				'favorite'     => true,
				'menu_order'   => 10,
				'categories'   => array( $items['shipping']['category'] ),
			),
			array(
				'demo_key'     => 'payment-reminder',
				'demo_locale'  => $locale,
				'legacy_title' => 'Demo: Payment reminder',
				'title'        => $items['payment']['title'],
				'content'      => $items['payment']['content'],
				'note_type'    => 'private',
				'favorite'     => true,
				'menu_order'   => 20,
				'categories'   => array( $items['payment']['category'] ),
			),
			array(
				'demo_key'     => 'customer-called',
				'demo_locale'  => $locale,
				'legacy_title' => 'Demo: Customer called',
				'title'        => $items['called']['title'],
				'content'      => $items['called']['content'],
				'note_type'    => 'private',
				'favorite'     => false,
				'menu_order'   => 30,
				'categories'   => array( $items['called']['category'] ),
			),
			array(
				'demo_key'     => 'delay-information',
				'demo_locale'  => $locale,
				'legacy_title' => 'Demo: Delay information',
				'title'        => $items['delay']['title'],
				'content'      => $items['delay']['content'],
				'note_type'    => 'customer',
				'favorite'     => false,
				'menu_order'   => 40,
				'categories'   => array( $items['delay']['category'] ),
			),
		);
	}

	/**
	 * Chooses German, Spanish, French, Italian, Hindi, Russian, Brazilian Portuguese, Simplified Chinese, Japanese, Dutch, Polish, Turkish, Persian, Vietnamese or Czech demo content for matching locales and English otherwise.
	 *
	 * @return string
	 */
	private static function get_demo_locale() {
		$locales = array( determine_locale(), get_user_locale(), get_locale() );

		foreach ( $locales as $locale ) {
			if ( ! is_string( $locale ) || '' === $locale ) {
				continue;
			}

			$normalized = str_replace( '-', '_', $locale );
			$language   = strtolower( strtok( $normalized, '_' ) );
			if ( 'de' === $language && false !== stripos( $normalized, 'formal' ) ) {
				return 'de_DE_formal';
			}
			if ( 'de' === $language ) {
				return 'de_DE';
			}
			if ( 'es' === $language ) {
				return 'es_ES';
			}
			if ( 'fr' === $language ) {
				return 'fr_FR';
			}
			if ( 'it' === $language ) {
				return 'it_IT';
			}
			if ( 'hi' === $language ) {
				return 'hi_IN';
			}
			if ( 'zh' === $language && in_array( strtolower( $normalized ), array( 'zh', 'zh_cn', 'zh_sg', 'zh_hans' ), true ) ) {
				return 'zh_CN';
			}
			if ( 'ja' === $language ) {
				return 'ja';
			}
			if ( 'nl' === $language ) {
				return 'nl_NL';
			}
			if ( 'pl' === $language ) {
				return 'pl_PL';
			}
			if ( 'tr' === $language ) {
				return 'tr_TR';
			}
			if ( 'fa' === $language ) {
				return 'fa_IR';
			}
			if ( 'vi' === $language ) {
				return 'vi';
			}
			if ( 'cs' === $language ) {
				return 'cs_CZ';
			}
			if ( 'ru' === $language ) {
				return 'ru_RU';
			}
			if ( 'pt' === $language ) {
				return 'pt_BR';
			}
		}

		return 'en_US';
	}

	/**
	 * Returns the reviewed English, German, Spanish, French, Italian, Russian and Brazilian Portuguese demo template sets.
	 *
	 * @return array<string,array<string,array<string,string>>>
	 */
	private static function get_demo_template_sets() {
		return array(
			'en_US' => array(
				'shipping' => array( 'title' => 'Demo: Shipping update', 'content' => 'Hello {customer}, your order {order_number} is being prepared for shipping via {shipping_method}.', 'category' => 'Shipping' ),
				'payment'  => array( 'title' => 'Demo: Payment reminder', 'content' => 'Payment reminder for order {order_number}. Payment method: {payment_method}. Please check payment status before processing.', 'category' => 'Payment' ),
				'called'   => array( 'title' => 'Demo: Customer called', 'content' => 'Customer {customer} called on {date} regarding order {order_number}. Please review the conversation before the next update.', 'category' => 'Customer service' ),
				'delay'    => array( 'title' => 'Demo: Delay information', 'content' => 'Hello {customer}, we are sorry that order {order_number} is delayed. We will send another update as soon as possible.', 'category' => 'Customer service' ),
			),
			'de_DE' => array(
				'shipping' => array( 'title' => 'Demo: Versandinformation', 'content' => 'Hallo {customer}, deine Bestellung {order_number} wird für den Versand mit {shipping_method} vorbereitet.', 'category' => 'Versand' ),
				'payment'  => array( 'title' => 'Demo: Zahlungserinnerung', 'content' => 'Zahlungserinnerung für Bestellung {order_number}. Zahlungsart: {payment_method}. Bitte prüfe den Zahlungsstatus vor der Bearbeitung.', 'category' => 'Zahlung' ),
				'called'   => array( 'title' => 'Demo: Kundenanruf', 'content' => 'Kunde {customer} hat am {date} wegen Bestellung {order_number} angerufen. Bitte prüfe das Gespräch vor der nächsten Aktualisierung.', 'category' => 'Kundendienst' ),
				'delay'    => array( 'title' => 'Demo: Lieferverzögerung', 'content' => 'Hallo {customer}, es tut uns leid, dass sich deine Bestellung {order_number} verzögert. Wir senden so bald wie möglich eine weitere Nachricht.', 'category' => 'Kundendienst' ),
			),
			'de_DE_formal' => array(
				'shipping' => array( 'title' => 'Demo: Versandinformation', 'content' => 'Hallo {customer}, Ihre Bestellung {order_number} wird für den Versand mit {shipping_method} vorbereitet.', 'category' => 'Versand' ),
				'payment'  => array( 'title' => 'Demo: Zahlungserinnerung', 'content' => 'Zahlungserinnerung für Bestellung {order_number}. Zahlungsart: {payment_method}. Bitte prüfen Sie den Zahlungsstatus vor der Bearbeitung.', 'category' => 'Zahlung' ),
				'called'   => array( 'title' => 'Demo: Kundenanruf', 'content' => 'Kunde {customer} hat am {date} wegen Bestellung {order_number} angerufen. Bitte prüfen Sie das Gespräch vor der nächsten Aktualisierung.', 'category' => 'Kundendienst' ),
				'delay'    => array( 'title' => 'Demo: Lieferverzögerung', 'content' => 'Hallo {customer}, es tut uns leid, dass sich Ihre Bestellung {order_number} verzögert. Wir senden so bald wie möglich eine weitere Nachricht.', 'category' => 'Kundendienst' ),
			),
			'es_ES' => array(
				'shipping' => array( 'title' => 'Demostración: Actualización del envío', 'content' => 'Hola {customer}, su pedido {order_number} se está preparando para enviarse mediante {shipping_method}.', 'category' => 'Envío' ),
				'payment'  => array( 'title' => 'Demostración: Recordatorio de pago', 'content' => 'Recordatorio de pago del pedido {order_number}. Método de pago: {payment_method}. Compruebe el estado del pago antes de continuar.', 'category' => 'Pago' ),
				'called'   => array( 'title' => 'Demostración: Llamada del cliente', 'content' => 'El cliente {customer} llamó el {date} por el pedido {order_number}. Revise la conversación antes de la próxima actualización.', 'category' => 'Atención al cliente' ),
				'delay'    => array( 'title' => 'Demostración: Información sobre el retraso', 'content' => 'Hola {customer}, lamentamos que el pedido {order_number} se haya retrasado. Enviaremos otra actualización lo antes posible.', 'category' => 'Atención al cliente' ),
			),
			'fr_FR' => array(
				'shipping' => array( 'title' => 'Démonstration : information de livraison', 'content' => 'Bonjour {customer}, votre commande {order_number} est en cours de préparation pour une expédition avec {shipping_method}.', 'category' => 'Livraison' ),
				'payment'  => array( 'title' => 'Démonstration : rappel de paiement', 'content' => 'Rappel de paiement pour la commande {order_number}. Mode de paiement : {payment_method}. Veuillez vérifier l’état du paiement avant de poursuivre.', 'category' => 'Paiement' ),
				'called'   => array( 'title' => 'Démonstration : appel du client', 'content' => 'Le client {customer} a appelé le {date} au sujet de la commande {order_number}. Veuillez consulter le compte rendu avant la prochaine mise à jour.', 'category' => 'Service client' ),
				'delay'    => array( 'title' => 'Démonstration : retard de livraison', 'content' => 'Bonjour {customer}, nous sommes désolés que la commande {order_number} soit retardée. Nous vous enverrons de nouvelles informations dès que possible.', 'category' => 'Service client' ),
			),
			'it_IT' => array(
				'shipping' => array( 'title' => 'Demo: aggiornamento sulla spedizione', 'content' => 'Buongiorno {customer}, il tuo ordine {order_number} è in preparazione per la spedizione tramite {shipping_method}.', 'category' => 'Spedizione' ),
				'payment'  => array( 'title' => 'Demo: promemoria di pagamento', 'content' => 'Promemoria di pagamento per l’ordine {order_number}. Metodo di pagamento: {payment_method}. Controlla lo stato del pagamento prima di procedere.', 'category' => 'Pagamento' ),
				'called'   => array( 'title' => 'Demo: chiamata del cliente', 'content' => 'Il cliente {customer} ha chiamato il {date} in merito all’ordine {order_number}. Controlla il resoconto della conversazione prima del prossimo aggiornamento.', 'category' => 'Servizio clienti' ),
				'delay'    => array( 'title' => 'Demo: ritardo nella consegna', 'content' => 'Buongiorno {customer}, ci dispiace che l’ordine {order_number} sia in ritardo. Invieremo un nuovo aggiornamento appena possibile.', 'category' => 'Servizio clienti' ),
			),
			'hi_IN' => array(
				'shipping' => array( 'title' => 'डेमो: शिपिंग अपडेट', 'content' => 'नमस्ते {customer}, आपका ऑर्डर {order_number}, {shipping_method} द्वारा भेजने के लिए तैयार किया जा रहा है।', 'category' => 'शिपिंग' ),
				'payment'  => array( 'title' => 'डेमो: भुगतान अनुस्मारक', 'content' => 'ऑर्डर {order_number} के लिए भुगतान अनुस्मारक। भुगतान विधि: {payment_method}। आगे बढ़ने से पहले भुगतान स्थिति जाँचें।', 'category' => 'भुगतान' ),
				'called'   => array( 'title' => 'डेमो: ग्राहक का फ़ोन', 'content' => 'ग्राहक {customer} ने {date} को ऑर्डर {order_number} के बारे में फ़ोन किया। अगले अपडेट से पहले बातचीत का विवरण जाँचें।', 'category' => 'ग्राहक सेवा' ),
				'delay'    => array( 'title' => 'डेमो: डिलीवरी में देरी', 'content' => 'नमस्ते {customer}, हमें खेद है कि ऑर्डर {order_number} में देरी हो रही है। नई जानकारी उपलब्ध होते ही हम आपको सूचित करेंगे।', 'category' => 'ग्राहक सेवा' ),
			),
			'zh_CN' => array(
				'shipping' => array( 'title' => '演示：配送更新', 'content' => '您好 {customer}，您的订单 {order_number} 正在准备通过 {shipping_method} 发出。', 'category' => '配送' ),
				'payment'  => array( 'title' => '演示：付款提醒', 'content' => '订单 {order_number} 的付款提醒。付款方式：{payment_method}。处理订单前请检查付款状态。', 'category' => '付款' ),
				'called'   => array( 'title' => '演示：客户来电', 'content' => '客户 {customer} 于 {date} 来电咨询订单 {order_number}。下次更新前请检查沟通记录。', 'category' => '客户服务' ),
				'delay'    => array( 'title' => '演示：延迟通知', 'content' => '您好 {customer}，很抱歉订单 {order_number} 出现延迟。我们会尽快向您发送进一步通知。', 'category' => '客户服务' ),
			),
			'ja' => array(
				'shipping' => array( 'title' => 'デモ: 配送状況', 'content' => '{customer} 様、ご注文 {order_number} は {shipping_method} での発送準備中です。', 'category' => '配送' ),
				'payment'  => array( 'title' => 'デモ: 支払いリマインダー', 'content' => 'ご注文 {order_number} のお支払いについてのご案内です。支払い方法: {payment_method}。処理を進める前に支払い状況をご確認ください。', 'category' => '支払い' ),
				'called'   => array( 'title' => 'デモ: 顧客からの電話', 'content' => '{customer} 様から {date} にご注文 {order_number} について電話がありました。次回の対応前に会話内容を確認してください。', 'category' => 'カスタマーサービス' ),
				'delay'    => array( 'title' => 'デモ: 配送遅延のお知らせ', 'content' => '{customer} 様、ご注文 {order_number} の配送が遅れており、申し訳ございません。新しい情報が分かり次第、改めてご案内いたします。', 'category' => 'カスタマーサービス' ),
			),
			'nl_NL' => array(
				'shipping' => array( 'title' => 'Demo: verzendupdate', 'content' => 'Hallo {customer}, je bestelling {order_number} wordt voorbereid voor verzending via {shipping_method}.', 'category' => 'Verzending' ),
				'payment'  => array( 'title' => 'Demo: betalingsherinnering', 'content' => 'Dit is een herinnering over de betaling van bestelling {order_number}. Betaalmethode: {payment_method}. Controleer de betaalstatus voordat de bestelling verder wordt verwerkt.', 'category' => 'Betaling' ),
				'called'   => array( 'title' => 'Demo: klant heeft gebeld', 'content' => 'Klant {customer} heeft op {date} gebeld over bestelling {order_number}. Controleer de gespreksnotities vóór de volgende update.', 'category' => 'Klantenservice' ),
				'delay'    => array( 'title' => 'Demo: melding van vertraging', 'content' => 'Hallo {customer}, onze excuses voor de vertraging van bestelling {order_number}. We sturen je zo snel mogelijk een nieuwe update.', 'category' => 'Klantenservice' ),
			),
			'pl_PL' => array(
				'shipping' => array( 'title' => 'Demo: aktualizacja wysyłki', 'content' => 'Dzień dobry {customer}, zamówienie {order_number} jest przygotowywane do wysyłki metodą {shipping_method}.', 'category' => 'Wysyłka' ),
				'payment' => array( 'title' => 'Demo: przypomnienie o płatności', 'content' => 'Przypomnienie o płatności za zamówienie {order_number}. Metoda płatności: {payment_method}. Przed dalszą realizacją sprawdź status płatności.', 'category' => 'Płatność' ),
				'called' => array( 'title' => 'Demo: telefon od klienta', 'content' => 'Klient {customer} zadzwonił {date} w sprawie zamówienia {order_number}. Przed kolejną aktualizacją sprawdź notatki z rozmowy.', 'category' => 'Obsługa klienta' ),
				'delay' => array( 'title' => 'Demo: opóźnienie dostawy', 'content' => 'Dzień dobry {customer}, przepraszamy za opóźnienie zamówienia {order_number}. Przekażemy kolejną informację najszybciej, jak to możliwe.', 'category' => 'Obsługa klienta' ),
			),
			'tr_TR' => array(
				'shipping' => array( 'title' => 'Demo: gönderim güncellemesi', 'content' => 'Merhaba {customer}, {order_number} numaralı siparişiniz {shipping_method} ile gönderilmek üzere hazırlanıyor.', 'category' => 'Gönderim' ),
				'payment'  => array( 'title' => 'Demo: ödeme hatırlatması', 'content' => '{order_number} numaralı sipariş için ödeme hatırlatması. Ödeme yöntemi: {payment_method}. İşleme devam etmeden önce ödeme durumunu kontrol edin.', 'category' => 'Ödeme' ),
				'called'   => array( 'title' => 'Demo: müşteri araması', 'content' => 'Müşteri {customer}, {date} tarihinde {order_number} numaralı sipariş hakkında aradı. Sonraki güncellemeden önce görüşme notlarını inceleyin.', 'category' => 'Müşteri hizmetleri' ),
				'delay'    => array( 'title' => 'Demo: teslimat gecikmesi', 'content' => 'Merhaba {customer}, {order_number} numaralı siparişinizdeki gecikme için üzgünüz. Yeni bilgi oluştuğunda sizi en kısa sürede bilgilendireceğiz.', 'category' => 'Müşteri hizmetleri' ),
			),
			'fa_IR' => array(
				'shipping' => array( 'title' => 'نمایشی: به‌روزرسانی ارسال', 'content' => 'سلام {customer}، سفارش {order_number} شما برای ارسال با روش {shipping_method} آماده می‌شود.', 'category' => 'حمل‌ونقل' ),
				'payment'  => array( 'title' => 'نمایشی: یادآوری پرداخت', 'content' => 'یادآوری پرداخت برای سفارش {order_number}. روش پرداخت: {payment_method}. پیش از ادامه پردازش، وضعیت پرداخت را بررسی کنید.', 'category' => 'پرداخت' ),
				'called'   => array( 'title' => 'نمایشی: تماس مشتری', 'content' => 'مشتری {customer} در تاریخ {date} درباره سفارش {order_number} تماس گرفت. پیش از به‌روزرسانی بعدی، یادداشت‌های گفت‌وگو را بررسی کنید.', 'category' => 'پشتیبانی مشتری' ),
				'delay'    => array( 'title' => 'نمایشی: تأخیر در تحویل', 'content' => 'سلام {customer}، بابت تأخیر سفارش {order_number} پوزش می‌خواهیم. در سریع‌ترین زمان ممکن اطلاعات تازه‌ای برای شما ارسال خواهیم کرد.', 'category' => 'پشتیبانی مشتری' ),
			),
			'vi' => array(
				'shipping' => array( 'title' => 'Minh họa: cập nhật giao hàng', 'content' => 'Xin chào {customer}, đơn hàng {order_number} của bạn đang được chuẩn bị để gửi bằng {shipping_method}.', 'category' => 'Giao hàng' ),
				'payment'  => array( 'title' => 'Minh họa: nhắc thanh toán', 'content' => 'Nhắc thanh toán cho đơn hàng {order_number}. Phương thức thanh toán: {payment_method}. Vui lòng kiểm tra trạng thái thanh toán trước khi tiếp tục xử lý.', 'category' => 'Thanh toán' ),
				'called'   => array( 'title' => 'Minh họa: khách hàng đã gọi', 'content' => 'Khách hàng {customer} đã gọi vào {date} về đơn hàng {order_number}. Vui lòng xem lại nội dung cuộc gọi trước lần cập nhật tiếp theo.', 'category' => 'Chăm sóc khách hàng' ),
				'delay'    => array( 'title' => 'Minh họa: chậm giao hàng', 'content' => 'Xin chào {customer}, chúng tôi rất tiếc vì đơn hàng {order_number} bị chậm. Chúng tôi sẽ gửi thông tin cập nhật ngay khi có thể.', 'category' => 'Chăm sóc khách hàng' ),
			),
			'cs_CZ' => array(
				'shipping' => array( 'title' => 'Ukázka: informace o dopravě', 'content' => 'Dobrý den, {customer}, vaši objednávku {order_number} připravujeme k odeslání způsobem {shipping_method}.', 'category' => 'Doprava' ),
				'payment'  => array( 'title' => 'Ukázka: připomínka platby', 'content' => 'Připomínka platby k objednávce {order_number}. Platební metoda: {payment_method}. Před dalším zpracováním zkontrolujte stav platby.', 'category' => 'Platba' ),
				'called'   => array( 'title' => 'Ukázka: telefonát zákazníka', 'content' => 'Zákazník {customer} volal dne {date} ohledně objednávky {order_number}. Před další aktualizací zkontrolujte záznam rozhovoru.', 'category' => 'Zákaznická podpora' ),
				'delay'    => array( 'title' => 'Ukázka: zpoždění dodávky', 'content' => 'Dobrý den, {customer}, omlouváme se za zpoždění objednávky {order_number}. Další informace vám pošleme co nejdříve.', 'category' => 'Zákaznická podpora' ),
			),
			'ru_RU' => array(
				'shipping' => array( 'title' => 'Демо: информация о доставке', 'content' => 'Здравствуйте, {customer}! Ваш заказ {order_number} готовится к отправке способом {shipping_method}.', 'category' => 'Доставка' ),
				'payment'  => array( 'title' => 'Демо: напоминание об оплате', 'content' => 'Напоминание об оплате заказа {order_number}. Способ оплаты: {payment_method}. Проверьте статус оплаты перед дальнейшей обработкой.', 'category' => 'Оплата' ),
				'called'   => array( 'title' => 'Демо: звонок клиента', 'content' => 'Клиент {customer} позвонил {date} по поводу заказа {order_number}. Перед следующим обновлением проверьте сведения о разговоре.', 'category' => 'Поддержка клиентов' ),
				'delay'    => array( 'title' => 'Демо: задержка доставки', 'content' => 'Здравствуйте, {customer}! К сожалению, заказ {order_number} задерживается. Мы сообщим новые сведения, как только они появятся.', 'category' => 'Поддержка клиентов' ),
			),
			'pt_BR' => array(
				'shipping' => array( 'title' => 'Demonstração: atualização da entrega', 'content' => 'Olá {customer}, seu pedido {order_number} está sendo preparado para envio por {shipping_method}.', 'category' => 'Entrega' ),
				'payment'  => array( 'title' => 'Demonstração: lembrete de pagamento', 'content' => 'Lembrete de pagamento do pedido {order_number}. Método de pagamento: {payment_method}. Verifique o status do pagamento antes de continuar o processamento.', 'category' => 'Pagamento' ),
				'called'   => array( 'title' => 'Demonstração: ligação do cliente', 'content' => 'O cliente {customer} ligou em {date} sobre o pedido {order_number}. Revise o conteúdo da conversa antes da próxima atualização.', 'category' => 'Atendimento ao cliente' ),
				'delay'    => array( 'title' => 'Demonstração: atraso na entrega', 'content' => 'Olá {customer}, lamentamos que o pedido {order_number} esteja atrasado. Enviaremos uma nova atualização assim que possível.', 'category' => 'Atendimento ao cliente' ),
			),
		);
	}

	/**
	 * Verifies nonce and capability.
	 *
	 * @param string $action Action name.
	 * @return void
	 */
	private static function verify_admin_action( $action ) {
		if ( ! current_user_can( MHONT_Capabilities::MANAGE_TEMPLATES ) ) {
			wp_die( esc_html__( 'You are not allowed to manage note templates.', 'mailhilfe-order-note-manager' ), '', array( 'response' => 403 ) );
		}

		if ( ! isset( $_POST['mhont_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mhont_nonce'] ) ), $action ) ) {
			wp_die( esc_html__( 'Security check failed.', 'mailhilfe-order-note-manager' ), '', array( 'response' => 403 ) );
		}
	}

	/**
	 * Creates or updates a template from array data.
	 *
	 * @param array $template_data Template data.
	 * @return int
	 */
	private static function upsert_template_from_array( $template_data ) {
		$title = array_key_exists( 'title', $template_data ) ? self::truncate_string( self::sanitize_scalar_text( $template_data['title'] ), 200 ) : '';
		if ( '' === $title ) {
			return 0;
		}

		$has_content    = array_key_exists( 'content', $template_data );
		$has_note_type  = array_key_exists( 'note_type', $template_data );
		$has_favorite   = array_key_exists( 'favorite', $template_data );
		$has_menu_order = array_key_exists( 'menu_order', $template_data );
		$has_language   = array_key_exists( 'language', $template_data );

		$demo_key     = array_key_exists( 'demo_key', $template_data ) ? self::sanitize_scalar_key( $template_data['demo_key'] ) : '';
		$demo_locale  = array_key_exists( 'demo_locale', $template_data ) ? self::sanitize_scalar_key( str_replace( '_', '-', self::scalar_to_string( $template_data['demo_locale'] ) ) ) : '';
		$raw_language = $has_language ? trim( self::scalar_to_string( $template_data['language'] ) ) : '';
		$language     = '' !== $raw_language ? MHONT_Post_Types::sanitize_template_language( $raw_language ) : '';
		if ( $has_language && '' !== $raw_language && '' === $language ) {
			return 0;
		}
		if ( ! $has_language && '' !== $demo_locale ) {
			$language = MHONT_Post_Types::sanitize_template_language( str_replace( '-', '_', $demo_locale ) );
			$has_language = true;
		}

		$legacy_title = array_key_exists( 'legacy_title', $template_data ) ? self::sanitize_scalar_text( $template_data['legacy_title'] ) : '';
		$existing_id  = '' !== $demo_key ? self::find_template_by_demo_key( $demo_key ) : 0;

		if ( ! $existing_id && '' !== $legacy_title ) {
			// Legacy demo templates may predate the language field, so allow any
			// language for this one-time migration lookup.
			$existing_id = self::find_template_by_title( $legacy_title, null );
		}

		if ( ! $existing_id ) {
			$existing_id = self::find_template_by_title( $title, $language );
		}

		$existing_post = $existing_id ? get_post( $existing_id ) : null;
		$content       = $has_content ? wp_kses_post( self::truncate_string( self::scalar_to_string( $template_data['content'] ), 50000 ) ) : '';
		$note_type     = $has_note_type ? self::normalize_note_type( self::sanitize_scalar_key( $template_data['note_type'] ) ) : 'private';
		$favorite      = $has_favorite && self::normalize_boolean( $template_data['favorite'] ) ? 'yes' : 'no';
		$menu_order    = 0;

		if ( $existing_id ) {
			if ( ! $has_content ) {
				$content = (string) get_post_meta( $existing_id, '_mhont_content', true );
			}
			if ( ! $has_note_type ) {
				$note_type = self::normalize_note_type( (string) get_post_meta( $existing_id, '_mhont_note_type', true ) );
			}
			if ( ! $has_favorite ) {
				$favorite = 'yes' === get_post_meta( $existing_id, '_mhont_favorite', true ) ? 'yes' : 'no';
			}
			if ( ! $has_language ) {
				$language = MHONT_Post_Types::sanitize_template_language( (string) get_post_meta( $existing_id, '_mhont_language', true ) );
			}
			$menu_order = $existing_post ? (int) $existing_post->menu_order : 0;
		}

		if ( $has_menu_order ) {
			$menu_value = self::scalar_to_string( $template_data['menu_order'] );
			if ( is_numeric( $menu_value ) ) {
				$menu_order = max( -100000, min( 100000, (int) $menu_value ) );
			}
		}

		$usage_count = null;
		if ( class_exists( 'MHONT_Settings' ) && MHONT_Settings::enabled( 'import_usage_counts' ) && array_key_exists( 'usage_count', $template_data ) ) {
			$usage_value = self::scalar_to_string( $template_data['usage_count'] );
			if ( is_numeric( $usage_value ) ) {
				$usage_count = absint( $usage_value );
			}
		}
		if ( null === $usage_count ) {
			$usage_count = $existing_id ? absint( get_post_meta( $existing_id, '_mhont_usage_count', true ) ) : 0;
		}

		$post_status = 'publish';
		if ( $existing_post && is_string( $existing_post->post_status ) && '' !== $existing_post->post_status ) {
			// Importing updated content must not unexpectedly publish an existing
			// draft, private template or template currently in the Trash.
			$post_status = $existing_post->post_status;
		}

		$post_data = array(
			'post_type'    => MHONT_Post_Types::POST_TYPE,
			'post_status'  => $post_status,
			'post_title'   => $title,
			'post_content' => $content,
			'menu_order'   => $menu_order,
		);

		if ( $existing_id ) {
			$post_data['ID'] = $existing_id;
			$post_id         = wp_update_post( wp_slash( $post_data ), true );
		} else {
			$post_id = wp_insert_post( wp_slash( $post_data ), true );
		}

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			return 0;
		}

		update_post_meta( $post_id, '_mhont_content', $content );
		update_post_meta( $post_id, '_mhont_note_type', $note_type );
		update_post_meta( $post_id, '_mhont_favorite', $favorite );
		update_post_meta( $post_id, '_mhont_usage_count', $usage_count );

		$conditions = array();
		if ( isset( $template_data['conditions'] ) && is_array( $template_data['conditions'] ) ) {
			$raw_conditions = $template_data['conditions'];
			foreach ( array( 'statuses', 'payment_methods', 'shipping_methods', 'countries' ) as $condition_key ) {
				if ( ! isset( $raw_conditions[ $condition_key ] ) || ! is_array( $raw_conditions[ $condition_key ] ) ) {
					continue;
				}

				$condition_values = array_slice( $raw_conditions[ $condition_key ], 0, 100 );
				if ( 'countries' === $condition_key ) {
					$condition_values = array_map(
						static function ( $value ) {
							if ( ! is_scalar( $value ) ) {
								return '';
							}
							$country = strtoupper( sanitize_text_field( (string) $value ) );
							return 2 === strlen( $country ) ? $country : '';
						},
						$condition_values
					);
				} elseif ( 'shipping_methods' === $condition_key ) {
					$condition_values = array_map(
						static function ( $value ) {
							if ( ! is_scalar( $value ) ) {
								return '';
							}
							$value = strtolower( trim( (string) $value ) );
							return preg_replace( '/[^a-z0-9_:-]/', '', $value );
						},
						$condition_values
					);
				} else {
					$condition_values = array_map(
						static function ( $value ) {
							return is_scalar( $value ) ? sanitize_key( (string) $value ) : '';
						},
						$condition_values
					);
				}

				$conditions[ $condition_key ] = array_values( array_unique( array_filter( $condition_values ) ) );
			}
			$conditions['min_total'] = isset( $raw_conditions['min_total'] ) && is_scalar( $raw_conditions['min_total'] ) ? (string) max( 0, (float) $raw_conditions['min_total'] ) : '';
			$conditions['max_total'] = isset( $raw_conditions['max_total'] ) && is_scalar( $raw_conditions['max_total'] ) ? (string) max( 0, (float) $raw_conditions['max_total'] ) : '';
		} elseif ( $existing_id ) {
			$conditions = get_post_meta( $existing_id, '_mhont_conditions', true );
			$conditions = is_array( $conditions ) ? $conditions : array();
		}
		update_post_meta( $post_id, '_mhont_language', $language );
		update_post_meta( $post_id, '_mhont_conditions', $conditions );

		if ( '' !== $demo_key ) {
			update_post_meta( $post_id, '_mhont_demo_key', $demo_key );
			self::store_demo_template_id( $demo_key, (int) $post_id );
		}

		if ( '' !== $demo_locale ) {
			update_post_meta( $post_id, '_mhont_demo_locale', $demo_locale );
		}

		if ( array_key_exists( 'categories', $template_data ) && is_array( $template_data['categories'] ) ) {
			$categories = array();
			foreach ( array_slice( $template_data['categories'], 0, 50 ) as $category ) {
				$category = self::sanitize_scalar_text( $category );
				if ( '' !== $category ) {
					$categories[] = $category;
				}
			}
			wp_set_object_terms( $post_id, array_values( array_unique( $categories ) ), MHONT_Post_Types::TAXONOMY, false );
		}

		return (int) $post_id;
	}

	/**
	 * Stores the relationship between a stable demo key and a template post ID.
	 *
	 * Keeping this small lookup option avoids a post-meta query when demo templates are
	 * installed or updated. PluginCheck correctly flags post-meta query usage as a
	 * possible slow query, so existing demo records are resolved through this map
	 * first and only fall back to a lightweight post scan if an older installation
	 * has not stored the map yet.
	 *
	 * @param string $demo_key Stable demo key.
	 * @param int    $post_id Template post ID.
	 * @return void
	 */
	private static function store_demo_template_id( $demo_key, $post_id ) {
		$demo_key = sanitize_key( $demo_key );
		$post_id  = absint( $post_id );

		if ( '' === $demo_key || ! $post_id ) {
			return;
		}

		$demo_map = get_option( 'mhont_demo_template_ids', array() );
		if ( ! is_array( $demo_map ) ) {
			$demo_map = array();
		}

		$demo_map[ $demo_key ] = $post_id;
		update_option( 'mhont_demo_template_ids', $demo_map, false );
	}

	/**
	 * Finds an existing demo template by its stable demo key.
	 *
	 * @param string $demo_key Stable demo key.
	 * @return int
	 */
	private static function find_template_by_demo_key( $demo_key ) {
		if ( '' === $demo_key ) {
			return 0;
		}

		$demo_map = get_option( 'mhont_demo_template_ids', array() );
		if ( ! is_array( $demo_map ) ) {
			$demo_map = array();
		}

		if ( ! empty( $demo_map[ $demo_key ] ) ) {
			$post_id = absint( $demo_map[ $demo_key ] );
			if ( $post_id && MHONT_Post_Types::POST_TYPE === get_post_type( $post_id ) && $demo_key === get_post_meta( $post_id, '_mhont_demo_key', true ) ) {
				return $post_id;
			}
		}

		$all_template_ids = get_posts(
			array(
				'post_type'        => MHONT_Post_Types::POST_TYPE,
				'post_status'      => array_keys( get_post_stati() ),
				'numberposts'      => -1,
				'fields'           => 'ids',
				'suppress_filters' => true,
			)
		);

		foreach ( $all_template_ids as $template_id ) {
			$template_id = absint( $template_id );
			if ( $template_id && $demo_key === get_post_meta( $template_id, '_mhont_demo_key', true ) ) {
				$demo_map[ $demo_key ] = $template_id;
				update_option( 'mhont_demo_template_ids', $demo_map, false );
				return $template_id;
			}
		}

		return 0;
	}

	/**
	 * Finds existing template by title.
	 *
	 * @param string      $title    Template title.
	 * @param string|null $language Template language, or null for a legacy wildcard lookup.
	 * @return int
	 */
	private static function find_template_by_title( $title, $language = '' ) {
		$existing = get_posts(
			array(
				'post_type'        => MHONT_Post_Types::POST_TYPE,
				'post_status'      => array_keys( get_post_stati() ),
				'title'            => $title,
				'numberposts'      => -1,
				'fields'           => 'ids',
				'no_found_rows'    => true,
				'suppress_filters' => true,
			)
		);

		foreach ( $existing as $post_id ) {
			$post_id = absint( $post_id );
			if ( ! $post_id ) {
				continue;
			}

			// A null language is used only for legacy demo migration. Regular
			// imports must match the template language as well as the title so
			// identically named translations do not overwrite each other.
			if ( null === $language ) {
				return $post_id;
			}

			$stored_language = MHONT_Post_Types::sanitize_template_language( (string) get_post_meta( $post_id, '_mhont_language', true ) );
			if ( $stored_language === $language ) {
				return $post_id;
			}
		}

		return 0;
	}

	/**
	 * Truncates imported text without breaking multibyte characters.
	 *
	 * @param string $value  Text value.
	 * @param int    $length Maximum character count.
	 * @return string
	 */
	private static function truncate_string( $value, $length ) {
		$value  = (string) $value;
		$length = max( 0, absint( $length ) );

		if ( function_exists( 'mb_substr' ) ) {
			return mb_substr( $value, 0, $length );
		}

		return substr( $value, 0, $length );
	}


	/**
	 * Converts an imported scalar value to a string without PHP warnings.
	 *
	 * @param mixed $value Imported value.
	 * @return string
	 */
	private static function scalar_to_string( $value ) {
		if ( is_string( $value ) || is_numeric( $value ) ) {
			return (string) $value;
		}

		if ( is_bool( $value ) ) {
			return $value ? '1' : '';
		}

		return '';
	}

	/**
	 * Sanitizes an imported scalar text value.
	 *
	 * @param mixed $value Imported value.
	 * @return string
	 */
	private static function sanitize_scalar_text( $value ) {
		return sanitize_text_field( self::scalar_to_string( $value ) );
	}

	/**
	 * Sanitizes an imported scalar key value.
	 *
	 * @param mixed $value Imported value.
	 * @return string
	 */
	private static function sanitize_scalar_key( $value ) {
		return sanitize_key( self::scalar_to_string( $value ) );
	}

	/**
	 * Normalizes imported boolean values without treating strings such as
	 * "false" or "no" as enabled.
	 *
	 * @param mixed $value Imported value.
	 * @return bool
	 */
	private static function normalize_boolean( $value ) {
		if ( is_bool( $value ) ) {
			return $value;
		}

		if ( is_int( $value ) || is_float( $value ) ) {
			return 1 === (int) $value;
		}

		if ( is_string( $value ) ) {
			return in_array( strtolower( trim( $value ) ), array( '1', 'yes', 'true', 'on' ), true );
		}

		return false;
	}

	/**
	 * Normalizes note type.
	 *
	 * @param string $note_type Note type.
	 * @return string
	 */
	private static function normalize_note_type( $note_type ) {
		return 'customer' === $note_type ? 'customer' : 'private';
	}

	/**
	 * Redirects back to import/export page.
	 *
	 * @param string $message Message key.
	 * @return void
	 */
	private static function redirect_with_message( $message ) {
		$message = sanitize_key( $message );
		if ( in_array( $message, array( 'imported', 'demo-installed', 'error', 'preview' ), true ) ) {
			set_transient( self::get_message_transient_key(), $message, MINUTE_IN_SECONDS );
		}

		wp_safe_redirect( admin_url( 'edit.php?post_type=' . MHONT_Post_Types::POST_TYPE . '&page=mhont-import-export' ) );
		exit;
	}

	/**
	 * Returns and clears the current user's admin message.
	 *
	 * @return string
	 */
	private static function get_admin_message() {
		$key     = self::get_message_transient_key();
		$message = get_transient( $key );
		delete_transient( $key );

		return is_string( $message ) ? sanitize_key( $message ) : '';
	}

	/**
	 * Gets the current user's transient key for pending imports.
	 *
	 * @return string
	 */
	private static function get_import_transient_key() {
		return 'mhont_import_preview_' . get_current_user_id();
	}

	/**
	 * Gets the current user's transient key for admin messages.
	 *
	 * @return string
	 */
	private static function get_message_transient_key() {
		return 'mhont_admin_message_' . get_current_user_id();
	}
}
