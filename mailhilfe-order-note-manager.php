<?php
/**
 * Plugin Name:       Mailhilfe Order Note Manager for WooCommerce
 * Description:       Create reusable WooCommerce order note templates with categories, placeholders, preview, role permissions and HPOS compatibility.
 * Version:           2.0.6
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Mailhilfe.de
 * Text Domain:       mailhilfe-order-note-manager
 * Domain Path:       /languages
 * Requires Plugins:  woocommerce
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * WC requires at least: 8.2
 * WC tested up to: 10.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MHONT_VERSION', '2.0.6' );
define( 'MHONT_FILE', __FILE__ );
define( 'MHONT_PATH', plugin_dir_path( __FILE__ ) );
define( 'MHONT_URL', plugin_dir_url( __FILE__ ) );
define( 'MHONT_TEXT_DOMAIN', 'mailhilfe-order-note-manager' );

add_action(
	'before_woocommerce_init',
	static function () {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

require_once MHONT_PATH . 'includes/class-mhont-plugin.php';
require_once MHONT_PATH . 'includes/class-mhont-history.php';

// Register email-result logging on all request types, including WP-Cron.
MHONT_History::email_hooks();

register_activation_hook( __FILE__, array( 'MHONT_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'MHONT_Plugin', 'deactivate' ) );

add_action(
	'plugins_loaded',
	static function () {
		// The plugin has no frontend output. Load its admin classes only for
		// dashboard and AJAX requests to reduce frontend memory and parse time.
		if ( is_admin() || wp_doing_ajax() ) {
			MHONT_Plugin::instance();
		}
	}
);
