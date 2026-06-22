<?php
/**
 * Lightweight template cache.
 *
 * @package Mailhilfe_Order_Note_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Caches the published template ID list and invalidates it on changes.
 */
final class MHONT_Template_Cache {

	const CACHE_GROUP   = 'mailhilfe_order_note_manager';
	const OBJECT_KEY    = 'published_template_ids';
	const TRANSIENT_KEY = 'mhont_published_template_ids';

	/** @var array<int>|null */
	private static $request_ids = null;

	/**
	 * Registers invalidation hooks.
	 *
	 * @return void
	 */
	public static function hooks() {
		add_action( 'save_post_' . MHONT_Post_Types::POST_TYPE, array( __CLASS__, 'clear' ) );
		add_action( 'before_delete_post', array( __CLASS__, 'maybe_clear' ) );
		add_action( 'trashed_post', array( __CLASS__, 'maybe_clear' ) );
		add_action( 'untrashed_post', array( __CLASS__, 'maybe_clear' ) );
	}

	/**
	 * Returns published template IDs in display order.
	 *
	 * @return array<int>
	 */
	public static function get_published_ids() {
		if ( null !== self::$request_ids ) {
			return self::$request_ids;
		}

		$ids = wp_cache_get( self::OBJECT_KEY, self::CACHE_GROUP );
		if ( false === $ids || ! is_array( $ids ) ) {
			$ids = get_transient( self::TRANSIENT_KEY );
		}

		if ( false === $ids || ! is_array( $ids ) ) {
			$ids = get_posts(
				array(
					'post_type'              => MHONT_Post_Types::POST_TYPE,
					'post_status'            => 'publish',
					'posts_per_page'         => -1,
					'fields'                 => 'ids',
					'orderby'                => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
					'order'                  => 'ASC',
					'no_found_rows'          => true,
					'ignore_sticky_posts'    => true,
					'cache_results'          => false,
					'update_post_meta_cache' => false,
					'update_post_term_cache' => false,
					'suppress_filters'       => true,
				)
			);
			$ids = array_values( array_filter( array_map( 'absint', $ids ) ) );
			set_transient( self::TRANSIENT_KEY, $ids, DAY_IN_SECONDS );
		}

		self::$request_ids = array_values( array_filter( array_map( 'absint', $ids ) ) );
		wp_cache_set( self::OBJECT_KEY, self::$request_ids, self::CACHE_GROUP, DAY_IN_SECONDS );
		return self::$request_ids;
	}

	/**
	 * Clears all cache layers.
	 *
	 * @return void
	 */
	public static function clear() {
		self::$request_ids = null;
		wp_cache_delete( self::OBJECT_KEY, self::CACHE_GROUP );
		delete_transient( self::TRANSIENT_KEY );
	}

	/**
	 * Clears the cache when the changed post is a template.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public static function maybe_clear( $post_id ) {
		if ( MHONT_Post_Types::POST_TYPE === get_post_type( $post_id ) ) {
			self::clear();
		}
	}
}
