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

		$filename = 'mailhilfe-order-note-manager-' . gmdate( 'Y-m-d-H-i-s' ) . '.json';
		nocache_headers();
		header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'X-Content-Type-Options: nosniff' );
		echo wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON download is encoded by wp_json_encode().
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
				<li><?php printf( esc_html__( '%d templates found.', 'mailhilfe-order-note-manager' ), absint( $summary['total'] ) ); ?></li>
				<li><?php printf( esc_html__( '%d templates will be created.', 'mailhilfe-order-note-manager' ), absint( $summary['create'] ) ); ?></li>
				<li><?php printf( esc_html__( '%d templates will be updated.', 'mailhilfe-order-note-manager' ), absint( $summary['update'] ) ); ?></li>
				<li><?php printf( esc_html__( '%d templates will be skipped.', 'mailhilfe-order-note-manager' ), absint( $summary['skip'] ) ); ?></li>
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
		$file  = wp_unslash( $_FILES['mhont_json_file'] );
		$name  = isset( $file['name'] ) ? sanitize_file_name( $file['name'] ) : '';
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
			$title = is_array( $template_data ) && array_key_exists( 'title', $template_data ) ? substr( self::sanitize_scalar_text( $template_data['title'] ), 0, 200 ) : '';
			if ( '' === $title ) {
				$summary['skip']++;
				continue;
			}
			$demo_key     = is_array( $template_data ) && array_key_exists( 'demo_key', $template_data ) ? self::sanitize_scalar_key( $template_data['demo_key'] ) : '';
			$legacy_title = is_array( $template_data ) && array_key_exists( 'legacy_title', $template_data ) ? self::sanitize_scalar_text( $template_data['legacy_title'] ) : '';
			$language     = is_array( $template_data ) && array_key_exists( 'language', $template_data ) ? MHONT_Post_Types::sanitize_template_language( self::scalar_to_string( $template_data['language'] ) ) : '';
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
	 * Installs or updates demo templates in the current admin language.
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
	 * Returns demo templates for the current locale.
	 *
	 * Demo templates are stored as normal WordPress posts. Because stored post
	 * content is not dynamically translated by WordPress, the plugin creates the
	 * demo records directly in the active admin language. The stable demo_key
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
	 * Gets the best matching supported locale for demo content.
	 *
	 * @return string
	 */
	private static function get_demo_locale() {
		$locales = array( determine_locale(), get_user_locale(), get_locale() );
		$map     = array(
			'de' => 'de_DE',
			'fr' => 'fr_FR',
			'es' => 'es_ES',
			'it' => 'it_IT',
			'pt' => 'pt_BR',
			'nl' => 'nl_NL',
			'pl' => 'pl_PL',
			'ru' => 'ru_RU',
			'zh' => 'zh_CN',
			'ja' => 'ja',
			'ko' => 'ko_KR',
			'tr' => 'tr_TR',
			'ar' => 'ar',
			'hi' => 'hi_IN',
			'id' => 'id_ID',
			'vi' => 'vi',
			'th' => 'th',
			'uk' => 'uk',
			'sv' => 'sv_SE',
			'da' => 'da_DK',
		);

		foreach ( $locales as $locale ) {
			if ( ! is_string( $locale ) || '' === $locale ) {
				continue;
			}

			$normalized = str_replace( '-', '_', $locale );
			if ( 'de_DE_formal' === $normalized ) {
				return 'de_DE';
			}

			$language = strtolower( strtok( $normalized, '_' ) );
			if ( isset( $map[ $language ] ) ) {
				return $map[ $language ];
			}
		}

		return 'en_US';
	}

	/**
	 * Returns translated demo template sets.
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
				'shipping' => array( 'title' => 'Demo: Versandupdate', 'content' => 'Hallo {customer}, Ihre Bestellung {order_number} wird für den Versand mit {shipping_method} vorbereitet.', 'category' => 'Versand' ),
				'payment'  => array( 'title' => 'Demo: Zahlungserinnerung', 'content' => 'Zahlungserinnerung für Bestellung {order_number}. Zahlungsart: {payment_method}. Bitte prüfen Sie den Zahlungsstatus vor der Bearbeitung.', 'category' => 'Zahlung' ),
				'called'   => array( 'title' => 'Demo: Kunde hat angerufen', 'content' => 'Kunde {customer} hat am {date} wegen Bestellung {order_number} angerufen. Bitte Gespräch vor dem nächsten Update prüfen.', 'category' => 'Kundendienst' ),
				'delay'    => array( 'title' => 'Demo: Verzögerungsinformation', 'content' => 'Hallo {customer}, es tut uns leid, dass sich Bestellung {order_number} verzögert. Wir senden so bald wie möglich ein weiteres Update.', 'category' => 'Kundendienst' ),
			),
			'fr_FR' => array(
				'shipping' => array( 'title' => 'Démo : mise à jour d’expédition', 'content' => 'Bonjour {customer}, votre commande {order_number} est en préparation pour une expédition via {shipping_method}.', 'category' => 'Expédition' ),
				'payment'  => array( 'title' => 'Démo : rappel de paiement', 'content' => 'Rappel de paiement pour la commande {order_number}. Mode de paiement : {payment_method}. Veuillez vérifier le statut du paiement avant le traitement.', 'category' => 'Paiement' ),
				'called'   => array( 'title' => 'Démo : appel du client', 'content' => 'Le client {customer} a appelé le {date} au sujet de la commande {order_number}. Veuillez consulter l’échange avant la prochaine mise à jour.', 'category' => 'Service client' ),
				'delay'    => array( 'title' => 'Démo : information de retard', 'content' => 'Bonjour {customer}, nous sommes désolés que la commande {order_number} soit retardée. Nous enverrons une nouvelle mise à jour dès que possible.', 'category' => 'Service client' ),
			),
			'es_ES' => array(
				'shipping' => array( 'title' => 'Demo: actualización de envío', 'content' => 'Hola {customer}, tu pedido {order_number} se está preparando para el envío mediante {shipping_method}.', 'category' => 'Envío' ),
				'payment'  => array( 'title' => 'Demo: recordatorio de pago', 'content' => 'Recordatorio de pago para el pedido {order_number}. Método de pago: {payment_method}. Comprueba el estado del pago antes de procesarlo.', 'category' => 'Pago' ),
				'called'   => array( 'title' => 'Demo: llamada del cliente', 'content' => 'El cliente {customer} llamó el {date} sobre el pedido {order_number}. Revisa la conversación antes de la próxima actualización.', 'category' => 'Atención al cliente' ),
				'delay'    => array( 'title' => 'Demo: información de retraso', 'content' => 'Hola {customer}, sentimos que el pedido {order_number} se haya retrasado. Enviaremos otra actualización lo antes posible.', 'category' => 'Atención al cliente' ),
			),
			'it_IT' => array(
				'shipping' => array( 'title' => 'Demo: aggiornamento spedizione', 'content' => 'Ciao {customer}, il tuo ordine {order_number} è in preparazione per la spedizione tramite {shipping_method}.', 'category' => 'Spedizione' ),
				'payment'  => array( 'title' => 'Demo: promemoria di pagamento', 'content' => 'Promemoria di pagamento per l’ordine {order_number}. Metodo di pagamento: {payment_method}. Verifica lo stato del pagamento prima di procedere.', 'category' => 'Pagamento' ),
				'called'   => array( 'title' => 'Demo: cliente ha chiamato', 'content' => 'Il cliente {customer} ha chiamato il {date} riguardo all’ordine {order_number}. Rivedi la conversazione prima del prossimo aggiornamento.', 'category' => 'Servizio clienti' ),
				'delay'    => array( 'title' => 'Demo: informazioni sul ritardo', 'content' => 'Ciao {customer}, ci dispiace che l’ordine {order_number} sia in ritardo. Invieremo un altro aggiornamento appena possibile.', 'category' => 'Servizio clienti' ),
			),
			'pt_BR' => array(
				'shipping' => array( 'title' => 'Demo: atualização de envio', 'content' => 'Olá {customer}, seu pedido {order_number} está sendo preparado para envio por {shipping_method}.', 'category' => 'Envio' ),
				'payment'  => array( 'title' => 'Demo: lembrete de pagamento', 'content' => 'Lembrete de pagamento do pedido {order_number}. Forma de pagamento: {payment_method}. Verifique o status do pagamento antes de processar.', 'category' => 'Pagamento' ),
				'called'   => array( 'title' => 'Demo: cliente ligou', 'content' => 'O cliente {customer} ligou em {date} sobre o pedido {order_number}. Revise a conversa antes da próxima atualização.', 'category' => 'Atendimento ao cliente' ),
				'delay'    => array( 'title' => 'Demo: informação de atraso', 'content' => 'Olá {customer}, sentimos que o pedido {order_number} esteja atrasado. Enviaremos outra atualização assim que possível.', 'category' => 'Atendimento ao cliente' ),
			),
			'nl_NL' => array(
				'shipping' => array( 'title' => 'Demo: verzendupdate', 'content' => 'Hallo {customer}, je bestelling {order_number} wordt voorbereid voor verzending via {shipping_method}.', 'category' => 'Verzending' ),
				'payment'  => array( 'title' => 'Demo: betalingsherinnering', 'content' => 'Betalingsherinnering voor bestelling {order_number}. Betaalmethode: {payment_method}. Controleer de betaalstatus voordat je verdergaat.', 'category' => 'Betaling' ),
				'called'   => array( 'title' => 'Demo: klant belde', 'content' => 'Klant {customer} belde op {date} over bestelling {order_number}. Bekijk het gesprek vóór de volgende update.', 'category' => 'Klantenservice' ),
				'delay'    => array( 'title' => 'Demo: vertragingsinformatie', 'content' => 'Hallo {customer}, het spijt ons dat bestelling {order_number} vertraagd is. We sturen zo snel mogelijk een nieuwe update.', 'category' => 'Klantenservice' ),
			),
			'pl_PL' => array(
				'shipping' => array( 'title' => 'Demo: aktualizacja wysyłki', 'content' => 'Witaj {customer}, zamówienie {order_number} jest przygotowywane do wysyłki przez {shipping_method}.', 'category' => 'Wysyłka' ),
				'payment'  => array( 'title' => 'Demo: przypomnienie o płatności', 'content' => 'Przypomnienie o płatności dla zamówienia {order_number}. Metoda płatności: {payment_method}. Sprawdź status płatności przed dalszą obsługą.', 'category' => 'Płatność' ),
				'called'   => array( 'title' => 'Demo: klient dzwonił', 'content' => 'Klient {customer} dzwonił {date} w sprawie zamówienia {order_number}. Sprawdź rozmowę przed kolejną aktualizacją.', 'category' => 'Obsługa klienta' ),
				'delay'    => array( 'title' => 'Demo: informacja o opóźnieniu', 'content' => 'Witaj {customer}, przepraszamy, że zamówienie {order_number} jest opóźnione. Wyślemy kolejną aktualizację tak szybko, jak to możliwe.', 'category' => 'Obsługa klienta' ),
			),
			'ru_RU' => array(
				'shipping' => array( 'title' => 'Демо: обновление доставки', 'content' => 'Здравствуйте, {customer}. Ваш заказ {order_number} готовится к отправке через {shipping_method}.', 'category' => 'Доставка' ),
				'payment'  => array( 'title' => 'Демо: напоминание об оплате', 'content' => 'Напоминание об оплате заказа {order_number}. Способ оплаты: {payment_method}. Проверьте статус оплаты перед обработкой.', 'category' => 'Оплата' ),
				'called'   => array( 'title' => 'Демо: клиент звонил', 'content' => 'Клиент {customer} звонил {date} по поводу заказа {order_number}. Проверьте разговор перед следующим обновлением.', 'category' => 'Служба поддержки' ),
				'delay'    => array( 'title' => 'Демо: информация о задержке', 'content' => 'Здравствуйте, {customer}. К сожалению, заказ {order_number} задерживается. Мы отправим новое обновление как можно скорее.', 'category' => 'Служба поддержки' ),
			),
			'zh_CN' => array(
				'shipping' => array( 'title' => '演示：发货更新', 'content' => '您好 {customer}，您的订单 {order_number} 正在准备通过 {shipping_method} 发货。', 'category' => '发货' ),
				'payment'  => array( 'title' => '演示：付款提醒', 'content' => '订单 {order_number} 的付款提醒。付款方式：{payment_method}。处理前请检查付款状态。', 'category' => '付款' ),
				'called'   => array( 'title' => '演示：客户来电', 'content' => '客户 {customer} 于 {date} 就订单 {order_number} 来电。请在下次更新前查看沟通记录。', 'category' => '客户服务' ),
				'delay'    => array( 'title' => '演示：延迟信息', 'content' => '您好 {customer}，很抱歉订单 {order_number} 发生延迟。我们会尽快发送新的更新。', 'category' => '客户服务' ),
			),
			'ja' => array(
				'shipping' => array( 'title' => 'デモ: 発送状況', 'content' => '{customer} 様、注文 {order_number} は {shipping_method} での発送準備中です。', 'category' => '発送' ),
				'payment'  => array( 'title' => 'デモ: 支払いリマインダー', 'content' => '注文 {order_number} の支払いリマインダーです。支払い方法: {payment_method}。処理前に支払い状況を確認してください。', 'category' => '支払い' ),
				'called'   => array( 'title' => 'デモ: 顧客からの電話', 'content' => '{customer} 様が {date} に注文 {order_number} について電話しました。次回更新前に内容を確認してください。', 'category' => 'カスタマーサービス' ),
				'delay'    => array( 'title' => 'デモ: 遅延情報', 'content' => '{customer} 様、注文 {order_number} が遅れており申し訳ありません。できるだけ早く次の更新をお送りします。', 'category' => 'カスタマーサービス' ),
			),
			'ko_KR' => array(
				'shipping' => array( 'title' => '데모: 배송 업데이트', 'content' => '안녕하세요 {customer}님, 주문 {order_number}이(가) {shipping_method} 배송을 위해 준비 중입니다.', 'category' => '배송' ),
				'payment'  => array( 'title' => '데모: 결제 알림', 'content' => '주문 {order_number} 결제 알림입니다. 결제 방법: {payment_method}. 처리 전에 결제 상태를 확인하세요.', 'category' => '결제' ),
				'called'   => array( 'title' => '데모: 고객 전화', 'content' => '고객 {customer}님이 {date}에 주문 {order_number} 관련으로 전화했습니다. 다음 업데이트 전에 내용을 확인하세요.', 'category' => '고객 서비스' ),
				'delay'    => array( 'title' => '데모: 지연 안내', 'content' => '안녕하세요 {customer}님, 주문 {order_number}이(가) 지연되어 죄송합니다. 가능한 한 빨리 추가 안내를 보내겠습니다.', 'category' => '고객 서비스' ),
			),
			'tr_TR' => array(
				'shipping' => array( 'title' => 'Demo: gönderim güncellemesi', 'content' => 'Merhaba {customer}, {order_number} numaralı siparişiniz {shipping_method} ile gönderim için hazırlanıyor.', 'category' => 'Gönderim' ),
				'payment'  => array( 'title' => 'Demo: ödeme hatırlatması', 'content' => '{order_number} numaralı sipariş için ödeme hatırlatması. Ödeme yöntemi: {payment_method}. İşleme almadan önce ödeme durumunu kontrol edin.', 'category' => 'Ödeme' ),
				'called'   => array( 'title' => 'Demo: müşteri aradı', 'content' => 'Müşteri {customer}, {date} tarihinde {order_number} numaralı sipariş hakkında aradı. Sonraki güncellemeden önce görüşmeyi inceleyin.', 'category' => 'Müşteri hizmetleri' ),
				'delay'    => array( 'title' => 'Demo: gecikme bilgisi', 'content' => 'Merhaba {customer}, {order_number} numaralı siparişin geciktiği için üzgünüz. En kısa sürede yeni bir güncelleme göndereceğiz.', 'category' => 'Müşteri hizmetleri' ),
			),
			'ar' => array(
				'shipping' => array( 'title' => 'تجريبي: تحديث الشحن', 'content' => 'مرحباً {customer}، يتم تجهيز طلبك {order_number} للشحن عبر {shipping_method}.', 'category' => 'الشحن' ),
				'payment'  => array( 'title' => 'تجريبي: تذكير بالدفع', 'content' => 'تذكير بالدفع للطلب {order_number}. طريقة الدفع: {payment_method}. يرجى التحقق من حالة الدفع قبل المعالجة.', 'category' => 'الدفع' ),
				'called'   => array( 'title' => 'تجريبي: اتصال العميل', 'content' => 'اتصل العميل {customer} في {date} بخصوص الطلب {order_number}. يرجى مراجعة المحادثة قبل التحديث التالي.', 'category' => 'خدمة العملاء' ),
				'delay'    => array( 'title' => 'تجريبي: معلومات التأخير', 'content' => 'مرحباً {customer}، نأسف لتأخر الطلب {order_number}. سنرسل تحديثاً آخر في أقرب وقت ممكن.', 'category' => 'خدمة العملاء' ),
			),
			'hi_IN' => array(
				'shipping' => array( 'title' => 'डेमो: शिपिंग अपडेट', 'content' => 'नमस्ते {customer}, आपका ऑर्डर {order_number} {shipping_method} के माध्यम से भेजने के लिए तैयार किया जा रहा है।', 'category' => 'शिपिंग' ),
				'payment'  => array( 'title' => 'डेमो: भुगतान अनुस्मारक', 'content' => 'ऑर्डर {order_number} के लिए भुगतान अनुस्मारक। भुगतान विधि: {payment_method}। प्रोसेस करने से पहले भुगतान स्थिति जांचें।', 'category' => 'भुगतान' ),
				'called'   => array( 'title' => 'डेमो: ग्राहक ने कॉल किया', 'content' => 'ग्राहक {customer} ने {date} को ऑर्डर {order_number} के बारे में कॉल किया। अगले अपडेट से पहले बातचीत की समीक्षा करें।', 'category' => 'ग्राहक सेवा' ),
				'delay'    => array( 'title' => 'डेमो: देरी की सूचना', 'content' => 'नमस्ते {customer}, हमें खेद है कि ऑर्डर {order_number} में देरी हो रही है। हम जल्द से जल्द एक और अपडेट भेजेंगे।', 'category' => 'ग्राहक सेवा' ),
			),
			'id_ID' => array(
				'shipping' => array( 'title' => 'Demo: pembaruan pengiriman', 'content' => 'Halo {customer}, pesanan Anda {order_number} sedang disiapkan untuk dikirim melalui {shipping_method}.', 'category' => 'Pengiriman' ),
				'payment'  => array( 'title' => 'Demo: pengingat pembayaran', 'content' => 'Pengingat pembayaran untuk pesanan {order_number}. Metode pembayaran: {payment_method}. Periksa status pembayaran sebelum diproses.', 'category' => 'Pembayaran' ),
				'called'   => array( 'title' => 'Demo: pelanggan menelepon', 'content' => 'Pelanggan {customer} menelepon pada {date} tentang pesanan {order_number}. Tinjau percakapan sebelum pembaruan berikutnya.', 'category' => 'Layanan pelanggan' ),
				'delay'    => array( 'title' => 'Demo: informasi keterlambatan', 'content' => 'Halo {customer}, mohon maaf pesanan {order_number} mengalami keterlambatan. Kami akan mengirim pembaruan lagi sesegera mungkin.', 'category' => 'Layanan pelanggan' ),
			),
			'vi' => array(
				'shipping' => array( 'title' => 'Demo: cập nhật vận chuyển', 'content' => 'Xin chào {customer}, đơn hàng {order_number} của bạn đang được chuẩn bị để gửi qua {shipping_method}.', 'category' => 'Vận chuyển' ),
				'payment'  => array( 'title' => 'Demo: nhắc thanh toán', 'content' => 'Nhắc thanh toán cho đơn hàng {order_number}. Phương thức thanh toán: {payment_method}. Vui lòng kiểm tra trạng thái thanh toán trước khi xử lý.', 'category' => 'Thanh toán' ),
				'called'   => array( 'title' => 'Demo: khách hàng đã gọi', 'content' => 'Khách hàng {customer} đã gọi vào {date} về đơn hàng {order_number}. Vui lòng xem lại cuộc trao đổi trước lần cập nhật tiếp theo.', 'category' => 'Dịch vụ khách hàng' ),
				'delay'    => array( 'title' => 'Demo: thông tin chậm trễ', 'content' => 'Xin chào {customer}, chúng tôi xin lỗi vì đơn hàng {order_number} bị chậm trễ. Chúng tôi sẽ gửi cập nhật tiếp theo sớm nhất có thể.', 'category' => 'Dịch vụ khách hàng' ),
			),
			'th' => array(
				'shipping' => array( 'title' => 'ตัวอย่าง: อัปเดตการจัดส่ง', 'content' => 'สวัสดี {customer} คำสั่งซื้อ {order_number} กำลังเตรียมจัดส่งผ่าน {shipping_method}', 'category' => 'การจัดส่ง' ),
				'payment'  => array( 'title' => 'ตัวอย่าง: แจ้งเตือนการชำระเงิน', 'content' => 'แจ้งเตือนการชำระเงินสำหรับคำสั่งซื้อ {order_number} วิธีชำระเงิน: {payment_method} โปรดตรวจสอบสถานะการชำระเงินก่อนดำเนินการ', 'category' => 'การชำระเงิน' ),
				'called'   => array( 'title' => 'ตัวอย่าง: ลูกค้าโทรมา', 'content' => 'ลูกค้า {customer} โทรมาเมื่อ {date} เกี่ยวกับคำสั่งซื้อ {order_number} โปรดตรวจสอบการสนทนาก่อนการอัปเดตครั้งถัดไป', 'category' => 'บริการลูกค้า' ),
				'delay'    => array( 'title' => 'ตัวอย่าง: ข้อมูลความล่าช้า', 'content' => 'สวัสดี {customer} ขออภัยที่คำสั่งซื้อ {order_number} ล่าช้า เราจะส่งอัปเดตเพิ่มเติมโดยเร็วที่สุด', 'category' => 'บริการลูกค้า' ),
			),
			'uk' => array(
				'shipping' => array( 'title' => 'Демо: оновлення доставки', 'content' => 'Вітаємо, {customer}. Ваше замовлення {order_number} готується до відправлення через {shipping_method}.', 'category' => 'Доставка' ),
				'payment'  => array( 'title' => 'Демо: нагадування про оплату', 'content' => 'Нагадування про оплату замовлення {order_number}. Спосіб оплати: {payment_method}. Перевірте статус оплати перед обробкою.', 'category' => 'Оплата' ),
				'called'   => array( 'title' => 'Демо: клієнт телефонував', 'content' => 'Клієнт {customer} телефонував {date} щодо замовлення {order_number}. Перегляньте розмову перед наступним оновленням.', 'category' => 'Служба підтримки' ),
				'delay'    => array( 'title' => 'Демо: інформація про затримку', 'content' => 'Вітаємо, {customer}. Нам шкода, що замовлення {order_number} затримується. Ми надішлемо нове оновлення якомога швидше.', 'category' => 'Служба підтримки' ),
			),
			'sv_SE' => array(
				'shipping' => array( 'title' => 'Demo: leveransuppdatering', 'content' => 'Hej {customer}, din order {order_number} förbereds för leverans med {shipping_method}.', 'category' => 'Leverans' ),
				'payment'  => array( 'title' => 'Demo: betalningspåminnelse', 'content' => 'Betalningspåminnelse för order {order_number}. Betalningsmetod: {payment_method}. Kontrollera betalningsstatus innan hantering.', 'category' => 'Betalning' ),
				'called'   => array( 'title' => 'Demo: kund ringde', 'content' => 'Kund {customer} ringde den {date} angående order {order_number}. Läs igenom samtalet före nästa uppdatering.', 'category' => 'Kundservice' ),
				'delay'    => array( 'title' => 'Demo: förseningsinformation', 'content' => 'Hej {customer}, vi beklagar att order {order_number} är försenad. Vi skickar en ny uppdatering så snart som möjligt.', 'category' => 'Kundservice' ),
			),
			'da_DK' => array(
				'shipping' => array( 'title' => 'Demo: forsendelsesopdatering', 'content' => 'Hej {customer}, din ordre {order_number} klargøres til forsendelse via {shipping_method}.', 'category' => 'Forsendelse' ),
				'payment'  => array( 'title' => 'Demo: betalingspåmindelse', 'content' => 'Betalingspåmindelse for ordre {order_number}. Betalingsmetode: {payment_method}. Kontrollér betalingsstatus før behandling.', 'category' => 'Betaling' ),
				'called'   => array( 'title' => 'Demo: kunde ringede', 'content' => 'Kunden {customer} ringede den {date} vedrørende ordre {order_number}. Gennemgå samtalen før næste opdatering.', 'category' => 'Kundeservice' ),
				'delay'    => array( 'title' => 'Demo: forsinkelsesinformation', 'content' => 'Hej {customer}, vi beklager, at ordre {order_number} er forsinket. Vi sender en ny opdatering så hurtigt som muligt.', 'category' => 'Kundeservice' ),
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
		$title = array_key_exists( 'title', $template_data ) ? substr( self::sanitize_scalar_text( $template_data['title'] ), 0, 200 ) : '';
		if ( '' === $title ) {
			return 0;
		}

		$has_content    = array_key_exists( 'content', $template_data );
		$has_note_type  = array_key_exists( 'note_type', $template_data );
		$has_favorite   = array_key_exists( 'favorite', $template_data );
		$has_menu_order = array_key_exists( 'menu_order', $template_data );
		$has_language   = array_key_exists( 'language', $template_data );

		$demo_key    = array_key_exists( 'demo_key', $template_data ) ? self::sanitize_scalar_key( $template_data['demo_key'] ) : '';
		$demo_locale = array_key_exists( 'demo_locale', $template_data ) ? self::sanitize_scalar_key( str_replace( '_', '-', self::scalar_to_string( $template_data['demo_locale'] ) ) ) : '';
		$language    = $has_language ? MHONT_Post_Types::sanitize_template_language( self::scalar_to_string( $template_data['language'] ) ) : '';
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
		$content       = $has_content ? wp_kses_post( substr( self::scalar_to_string( $template_data['content'] ), 0, 50000 ) ) : '';
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
				if ( isset( $raw_conditions[ $condition_key ] ) && is_array( $raw_conditions[ $condition_key ] ) ) {
					if ( 'countries' === $condition_key ) {
					$conditions[ $condition_key ] = array_values( array_unique( array_filter( array_map( static function ( $value ) { return is_scalar( $value ) ? strtoupper( sanitize_text_field( (string) $value ) ) : ''; }, $raw_conditions[ $condition_key ] ) ) ) );
				} else {
					$conditions[ $condition_key ] = array_values( array_unique( array_filter( array_map( 'sanitize_key', $raw_conditions[ $condition_key ] ) ) ) );
				}
				}
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
