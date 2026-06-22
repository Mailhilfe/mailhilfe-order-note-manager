<?php
/**
 * Plugin settings and role management.
 *
 * @package Mailhilfe_Order_Note_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles configurable plugin behavior.
 */
final class MHONT_Settings {

	const OPTION_NAME = 'mhont_settings';

	/**
	 * Request-local settings cache.
	 *
	 * @var array<string,mixed>|null
	 */
	private static $settings_cache = null;

	/**
	 * Registers hooks.
	 *
	 * @return void
	 */
	public static function hooks() {
		add_action( 'admin_menu', array( __CLASS__, 'add_submenu' ) );
		add_action( 'admin_post_mhont_save_settings', array( __CLASS__, 'save_settings' ) );
		add_action( 'admin_post_mhont_save_roles', array( __CLASS__, 'save_roles' ) );
	}

	/**
	 * Adds settings and role pages.
	 *
	 * @return void
	 */
	public static function add_submenu() {
		add_submenu_page(
			'edit.php?post_type=' . MHONT_Post_Types::POST_TYPE,
			__( 'Mailhilfe Order Note Manager Settings', 'mailhilfe-order-note-manager' ),
			__( 'Settings', 'mailhilfe-order-note-manager' ),
			MHONT_Capabilities::MANAGE_TEMPLATES,
			'mhont-settings',
			array( __CLASS__, 'render_settings_page' )
		);

		add_submenu_page(
			'edit.php?post_type=' . MHONT_Post_Types::POST_TYPE,
			__( 'Mailhilfe Order Note Manager Permissions', 'mailhilfe-order-note-manager' ),
			__( 'Permissions', 'mailhilfe-order-note-manager' ),
			'manage_options',
			'mhont-permissions',
			array( __CLASS__, 'render_permissions_page' )
		);
	}

	/**
	 * Default settings.
	 *
	 * @return array<string,mixed>
	 */
	private static function defaults() {
		return array(
			'install_demo_on_activation' => 'yes',
			'default_note_type'          => 'private',
			'log_customer_notes'         => 'yes',
			'allow_html'                 => 'yes',
			'show_usage_count'           => 'yes',
			'favorites_first'            => 'yes',
			'allow_json_import'          => 'yes',
			'import_usage_counts'        => 'no',
			'use_order_language'         => 'yes',
		);
	}

	/**
	 * Returns all settings.
	 *
	 * @return array<string,mixed>
	 */
	public static function get_all() {
		if ( null !== self::$settings_cache ) {
			return self::$settings_cache;
		}

		$settings = get_option( self::OPTION_NAME, array() );
		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		unset( $settings['force_customer_warning'] );
		self::$settings_cache = wp_parse_args( $settings, self::defaults() );
		return self::$settings_cache;
	}

	/**
	 * Returns one setting value.
	 *
	 * @param string $key Setting key.
	 * @return mixed
	 */
	public static function get( $key ) {
		$settings = self::get_all();
		return isset( $settings[ $key ] ) ? $settings[ $key ] : null;
	}

	/**
	 * Returns whether a checkbox setting is enabled.
	 *
	 * @param string $key Setting key.
	 * @return bool
	 */
	public static function enabled( $key ) {
		return 'yes' === self::get( $key );
	}

	/**
	 * Sanitizes settings.
	 *
	 * @param array $raw Raw settings.
	 * @return array<string,string>
	 */
	private static function sanitize_settings( $raw ) {
		$raw      = is_array( $raw ) ? $raw : array();
		$settings = self::defaults();
		$checks   = array( 'install_demo_on_activation', 'log_customer_notes', 'allow_html', 'show_usage_count', 'favorites_first', 'allow_json_import', 'import_usage_counts', 'use_order_language' );

		foreach ( $checks as $key ) {
			$settings[ $key ] = ! empty( $raw[ $key ] ) ? 'yes' : 'no';
		}

		$note_type = isset( $raw['default_note_type'] ) ? sanitize_key( $raw['default_note_type'] ) : 'private';
		$settings['default_note_type'] = 'customer' === $note_type ? 'customer' : 'private';

		return $settings;
	}

	/**
	 * Renders settings page.
	 *
	 * @return void
	 */
	public static function render_settings_page() {
		if ( ! current_user_can( MHONT_Capabilities::MANAGE_TEMPLATES ) ) {
			wp_die( esc_html__( 'You are not allowed to manage note templates.', 'mailhilfe-order-note-manager' ), '', array( 'response' => 403 ) );
		}

		$settings = self::get_all();
		?>
		<div class="wrap mhont-settings-page">
			<h1><?php esc_html_e( 'Mailhilfe Order Note Manager Settings', 'mailhilfe-order-note-manager' ); ?></h1>
			<p><?php esc_html_e( 'Configure default behavior for template selection, customer notes, imports and admin display.', 'mailhilfe-order-note-manager' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="mhont_save_settings">
				<?php wp_nonce_field( 'mhont_save_settings', 'mhont_nonce' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Default note type', 'mailhilfe-order-note-manager' ); ?></th>
						<td>
							<select name="mhont_settings[default_note_type]">
								<option value="private" <?php selected( $settings['default_note_type'], 'private' ); ?>><?php esc_html_e( 'Internal note', 'mailhilfe-order-note-manager' ); ?></option>
								<option value="customer" <?php selected( $settings['default_note_type'], 'customer' ); ?>><?php esc_html_e( 'Customer note', 'mailhilfe-order-note-manager' ); ?></option>
							</select>
							<p class="description"><?php esc_html_e( 'This value is used when a template does not define its own note type.', 'mailhilfe-order-note-manager' ); ?></p>
						</td>
					</tr>
					<?php
					$checkboxes = array(
						'install_demo_on_activation' => __( 'Install demo templates automatically when no templates exist', 'mailhilfe-order-note-manager' ),
						'log_customer_notes'         => __( 'Create an internal log note after a customer note is created', 'mailhilfe-order-note-manager' ),
						'allow_html'                 => __( 'Allow safe HTML formatting in notes', 'mailhilfe-order-note-manager' ),
						'show_usage_count'           => __( 'Show the usage counter column in the template list', 'mailhilfe-order-note-manager' ),
						'favorites_first'            => __( 'Show favorites first in the order template selector', 'mailhilfe-order-note-manager' ),
						'allow_json_import'          => __( 'Allow JSON imports', 'mailhilfe-order-note-manager' ),
						'import_usage_counts'        => __( 'Import usage counters from JSON files', 'mailhilfe-order-note-manager' ),
						'use_order_language'         => __( 'Prefer the order/customer language for multilingual templates when available', 'mailhilfe-order-note-manager' ),
					);
					foreach ( $checkboxes as $key => $label ) :
						?>
						<tr>
							<th scope="row"><?php echo esc_html( $label ); ?></th>
							<td><label><input type="checkbox" name="mhont_settings[<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( $settings[ $key ], 'yes' ); ?>> <?php esc_html_e( 'Enabled', 'mailhilfe-order-note-manager' ); ?></label></td>
						</tr>
						<?php
					endforeach;
					?>
				</table>
				<?php submit_button( __( 'Save settings', 'mailhilfe-order-note-manager' ) ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Saves settings.
	 *
	 * @return void
	 */
	public static function save_settings() {
		if ( ! current_user_can( MHONT_Capabilities::MANAGE_TEMPLATES ) ) {
			wp_die( esc_html__( 'You are not allowed to manage note templates.', 'mailhilfe-order-note-manager' ), '', array( 'response' => 403 ) );
		}
		if ( ! isset( $_POST['mhont_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mhont_nonce'] ) ), 'mhont_save_settings' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'mailhilfe-order-note-manager' ), '', array( 'response' => 403 ) );
		}

		$raw = isset( $_POST['mhont_settings'] ) && is_array( $_POST['mhont_settings'] ) ? wp_unslash( $_POST['mhont_settings'] ) : array();
		self::$settings_cache = self::sanitize_settings( $raw );
		update_option( self::OPTION_NAME, self::$settings_cache, false );
		wp_safe_redirect( admin_url( 'edit.php?post_type=' . MHONT_Post_Types::POST_TYPE . '&page=mhont-settings&updated=1' ) );
		exit;
	}

	/**
	 * Renders the role permissions page.
	 *
	 * @return void
	 */
	public static function render_permissions_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to manage permissions.', 'mailhilfe-order-note-manager' ), '', array( 'response' => 403 ) );
		}

		global $wp_roles;
		$roles = is_object( $wp_roles ) ? $wp_roles->roles : array();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Mailhilfe Order Note Manager Permissions', 'mailhilfe-order-note-manager' ); ?></h1>
			<p><?php esc_html_e( 'Grant template permissions only to trusted roles. Users also need the normal WooCommerce permission to edit orders before they can add notes to an order.', 'mailhilfe-order-note-manager' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="mhont_save_roles">
				<?php wp_nonce_field( 'mhont_save_roles', 'mhont_nonce' ); ?>
				<table class="widefat striped">
					<thead><tr><th><?php esc_html_e( 'Role', 'mailhilfe-order-note-manager' ); ?></th><th><?php esc_html_e( 'Manage templates', 'mailhilfe-order-note-manager' ); ?></th><th><?php esc_html_e( 'Use templates', 'mailhilfe-order-note-manager' ); ?></th></tr></thead>
					<tbody>
					<?php foreach ( $roles as $role_key => $role_data ) :
						$role = get_role( $role_key );
						if ( ! $role ) {
							continue;
						}
						?>
						<tr>
							<td><strong><?php echo esc_html( translate_user_role( $role_data['name'] ) ); ?></strong><br><code><?php echo esc_html( $role_key ); ?></code></td>
							<td><input type="checkbox" name="mhont_roles[<?php echo esc_attr( $role_key ); ?>][manage]" value="1" <?php checked( $role->has_cap( MHONT_Capabilities::MANAGE_TEMPLATES ) ); ?> <?php disabled( 'administrator', $role_key ); ?>></td>
							<td><input type="checkbox" name="mhont_roles[<?php echo esc_attr( $role_key ); ?>][use]" value="1" <?php checked( $role->has_cap( MHONT_Capabilities::USE_TEMPLATES ) ); ?> <?php disabled( 'administrator', $role_key ); ?>></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<?php submit_button( __( 'Save permissions', 'mailhilfe-order-note-manager' ) ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Saves role permissions.
	 *
	 * @return void
	 */
	public static function save_roles() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to manage permissions.', 'mailhilfe-order-note-manager' ), '', array( 'response' => 403 ) );
		}
		if ( ! isset( $_POST['mhont_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mhont_nonce'] ) ), 'mhont_save_roles' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'mailhilfe-order-note-manager' ), '', array( 'response' => 403 ) );
		}

		$posted = isset( $_POST['mhont_roles'] ) && is_array( $_POST['mhont_roles'] ) ? wp_unslash( $_POST['mhont_roles'] ) : array();
		global $wp_roles;
		$roles = is_object( $wp_roles ) ? array_keys( $wp_roles->roles ) : array();

		foreach ( $roles as $role_key ) {
			$role = get_role( $role_key );
			if ( ! $role ) {
				continue;
			}
			if ( 'administrator' === $role_key ) {
				$role->add_cap( MHONT_Capabilities::MANAGE_TEMPLATES );
				$role->add_cap( MHONT_Capabilities::USE_TEMPLATES );
				continue;
			}

			$role_data = isset( $posted[ $role_key ] ) && is_array( $posted[ $role_key ] ) ? $posted[ $role_key ] : array();
			! empty( $role_data['manage'] ) ? $role->add_cap( MHONT_Capabilities::MANAGE_TEMPLATES ) : $role->remove_cap( MHONT_Capabilities::MANAGE_TEMPLATES );
			! empty( $role_data['use'] ) ? $role->add_cap( MHONT_Capabilities::USE_TEMPLATES ) : $role->remove_cap( MHONT_Capabilities::USE_TEMPLATES );
		}

		wp_safe_redirect( admin_url( 'edit.php?post_type=' . MHONT_Post_Types::POST_TYPE . '&page=mhont-permissions&updated=1' ) );
		exit;
	}
}
