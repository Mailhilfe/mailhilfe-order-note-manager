<?php
/**
 * Admin links and menu helpers.
 *
 * @package Mailhilfe_Order_Note_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles admin navigation helpers.
 */
final class MHONT_Admin {

	/**
	 * Registers hooks.
	 *
	 * @return void
	 */
	public static function hooks() {
		add_filter( 'plugin_action_links_' . plugin_basename( MHONT_FILE ), array( __CLASS__, 'plugin_action_links' ) );
	}

	/**
	 * Adds a settings link on the WordPress plugins page.
	 *
	 * @param array $links Existing plugin action links.
	 * @return array
	 */
	public static function plugin_action_links( $links ) {
		if ( ! current_user_can( MHONT_Capabilities::MANAGE_TEMPLATES ) ) {
			return $links;
		}

		$settings_link = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( self::settings_url() ),
			esc_html__( 'Settings', 'mailhilfe-order-note-manager' )
		);
		$help_link     = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( self::help_url() ),
			esc_html( self::localized_help_label() )
		);
		$faq_link      = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( self::faq_url() ),
			esc_html( self::localized_faq_label() )
		);

		array_unshift( $links, $faq_link );
		array_unshift( $links, $help_link );
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * Returns the template overview URL.
	 *
	 * @return string
	 */
	public static function templates_url() {
		return admin_url( 'edit.php?post_type=' . MHONT_Post_Types::POST_TYPE );
	}

	/**
	 * Returns the settings page URL.
	 *
	 * @return string
	 */
	public static function settings_url() {
		return admin_url( 'edit.php?post_type=' . MHONT_Post_Types::POST_TYPE . '&page=mhont-settings' );
	}

	/**
	 * Returns the help page URL.
	 *
	 * @return string
	 */
	public static function help_url() {
		return admin_url( 'edit.php?post_type=' . MHONT_Post_Types::POST_TYPE . '&page=mhont-help' );
	}

	/**
	 * Returns the FAQ page URL.
	 *
	 * @return string
	 */
	public static function faq_url() {
		return admin_url( 'edit.php?post_type=' . MHONT_Post_Types::POST_TYPE . '&page=mhont-faq' );
	}

	/**
	 * Returns localized help label for the plugin action link.
	 *
	 * @return string
	 */
	private static function localized_help_label() {
		return __( 'Help', 'mailhilfe-order-note-manager' );
	}
	/**
	 * Returns localized FAQ label for the plugin action link.
	 *
	 * @return string
	 */
	private static function localized_faq_label() {
		return __( 'FAQ', 'mailhilfe-order-note-manager' );
	}

}
