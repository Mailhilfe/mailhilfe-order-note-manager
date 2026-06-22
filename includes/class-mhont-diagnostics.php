<?php
/** Diagnostics page. @package Mailhilfe_Order_Note_Manager */
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class MHONT_Diagnostics {
	public static function render_page() {
		if ( ! current_user_can( MHONT_Capabilities::MANAGE_TEMPLATES ) ) { wp_die( esc_html__( 'You are not allowed to view diagnostics.', 'mailhilfe-order-note-manager' ), '', array( 'response' => 403 ) ); }
		$hpos = false;
		if ( class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' ) ) { $hpos = \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled(); }
		$email_enabled = 'unknown';
		if ( function_exists( 'WC' ) && WC() && WC()->mailer() ) { $emails = WC()->mailer()->get_emails(); if ( isset( $emails['WC_Email_Customer_Note'] ) ) { $email_enabled = $emails['WC_Email_Customer_Note']->is_enabled() ? 'yes' : 'no'; } }
		$post_counts    = wp_count_posts( MHONT_Post_Types::POST_TYPE );
		$template_count = is_object( $post_counts ) && isset( $post_counts->publish ) ? absint( $post_counts->publish ) : 0;
		$rows = array(
			'Plugin version' => MHONT_VERSION,
			'WordPress version' => get_bloginfo( 'version' ),
			'PHP version' => PHP_VERSION,
			'WooCommerce version' => defined( 'WC_VERSION' ) ? WC_VERSION : 'not active',
			'HPOS active' => $hpos ? 'yes' : 'no',
			'Customer note email enabled' => $email_enabled,
			'Template count' => $template_count,
			'Object cache' => wp_using_ext_object_cache() ? 'yes' : 'no',
			'Locale' => determine_locale(),
			'WP_DEBUG' => ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? 'yes' : 'no',
		);
		$filtered_rows = apply_filters( 'mailhilfe_order_note_diagnostics', $rows );
		if ( is_array( $filtered_rows ) ) {
			$rows = $filtered_rows;
		}
		?><div class="wrap"><h1><?php esc_html_e( 'Diagnostics', 'mailhilfe-order-note-manager' ); ?></h1><table class="widefat striped"><tbody><?php foreach ( $rows as $label => $value ) : ?><tr><th><?php echo esc_html( $label ); ?></th><td><code><?php echo esc_html( is_scalar( $value ) ? (string) $value : wp_json_encode( $value ) ); ?></code></td></tr><?php endforeach; ?></tbody></table></div><?php
	}
}
