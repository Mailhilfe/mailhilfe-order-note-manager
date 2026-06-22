<?php
/**
 * Capability handling.
 *
 * @package Mailhilfe_Order_Note_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Role and capability helper.
 */
final class MHONT_Capabilities {

	const MANAGE_TEMPLATES = 'manage_mh_order_note_templates';
	const USE_TEMPLATES    = 'use_mh_order_note_templates';

	/**
	 * Adds capabilities when missing.
	 *
	 * @return void
	 */
	public static function maybe_add_caps() {
		if ( 'yes' !== get_option( 'mhont_caps_installed' ) ) {
			self::add_caps();
		}
	}

	/**
	 * Adds plugin capabilities to administrator and shop manager.
	 *
	 * @return void
	 */
	public static function add_caps() {
		$roles = array( 'administrator', 'shop_manager' );

		foreach ( $roles as $role_name ) {
			$role = get_role( $role_name );
			if ( $role ) {
				$role->add_cap( self::MANAGE_TEMPLATES );
				$role->add_cap( self::USE_TEMPLATES );
			}
		}

		update_option( 'mhont_caps_installed', 'yes', false );
	}
}
