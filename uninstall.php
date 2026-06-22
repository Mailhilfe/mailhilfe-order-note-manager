<?php
/**
 * Uninstall handler.
 *
 * @package Mailhilfe_Order_Note_Manager
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Remove all stored templates, including drafts and items in the trash.
$template_ids = get_posts(
	array(
		'post_type'      => 'mhont_template',
		// The special "any" value excludes Trash and some internal statuses.
		// Query every registered status so uninstall also removes trashed drafts.
		'post_status'    => array_keys( get_post_stati() ),
		'fields'         => 'ids',
		'posts_per_page'  => -1,
		'no_found_rows'   => true,
		'suppress_filters' => true,
	)
);

foreach ( $template_ids as $template_id ) {
	wp_delete_post( absint( $template_id ), true );
}

// Register the private taxonomy temporarily so its terms can be removed via
// the WordPress term API even when the plugin itself is no longer loaded.
if ( ! taxonomy_exists( 'mhont_category' ) ) {
	register_taxonomy( 'mhont_category', array( 'mhont_template' ), array( 'public' => false ) );
}

$term_ids = get_terms(
	array(
		'taxonomy'   => 'mhont_category',
		'hide_empty' => false,
		'fields'     => 'ids',
	)
);

if ( ! is_wp_error( $term_ids ) ) {
	foreach ( $term_ids as $term_id ) {
		wp_delete_term( absint( $term_id ), 'mhont_category' );
	}
}

// Remove plugin capabilities from every role to avoid stale permissions.
global $wp_roles;
if ( ! isset( $wp_roles ) || ! is_object( $wp_roles ) ) {
	$wp_roles = wp_roles(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Required during uninstall bootstrap.
}

foreach ( array_keys( $wp_roles->roles ) as $role_name ) {
	$role = get_role( $role_name );
	if ( $role ) {
		$role->remove_cap( 'manage_mh_order_note_templates' );
		$role->remove_cap( 'use_mh_order_note_templates' );
	}
}

delete_option( 'mhont_caps_installed' );
delete_option( 'mhont_settings' );
delete_option( 'mhont_demo_template_ids' );
delete_transient( 'mhont_published_template_ids' );
wp_cache_delete( 'published_template_ids', 'mailhilfe_order_note_manager' );


// Remove the central history table and its schema version.
global $wpdb;
$history_table = $wpdb->prefix . 'mhont_history';
$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $history_table ) );
delete_option( 'mhont_history_db_version' );

// Remove the personal favorites and recent-template data belonging to this site.
$user_meta_suffix = is_multisite() ? '_' . get_current_blog_id() : '';
$wpdb->delete( $wpdb->usermeta, array( 'meta_key' => '_mhont_personal_favorites' . $user_meta_suffix ), array( '%s' ) );
$wpdb->delete( $wpdb->usermeta, array( 'meta_key' => '_mhont_recent_templates' . $user_meta_suffix ), array( '%s' ) );
