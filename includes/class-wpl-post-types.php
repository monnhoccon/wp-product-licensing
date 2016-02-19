<?php
/**
 * Post Types
 *
 * Registers post types.
 *
 * @class     WPL_Post_Types
 * @version   1.0.0
 * @package   WooCommerce/Classes/API Products
 * @category  Class
 * @author    AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WPL_Post_Types Class.
 */
class WPL_Post_Types {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
	}

	/**
	 * Register core post types.
	 */
	public static function register_post_types() {
		if ( post_type_exists( 'api_product' ) ) {
			return;
		}

		register_post_type( 'api_product',
			apply_filters( 'register_post_type_api_product',
				array(
					'labels'              => array(
							'name'                  => __( 'API Products', 'wp-product-licensing' ),
							'singular_name'         => __( 'API Product', 'wp-product-licensing' ),
							'menu_name'             => _x( 'API Products', 'Admin menu name', 'wp-product-licensing' ),
							'all_items'             => __( 'All API Products', 'wp-product-licensing' ),
							'add_new'               => __( 'Add New', 'wp-product-licensing' ),
							'add_new_item'          => __( 'Add API Product', 'wp-product-licensing' ),
							'edit'                  => __( 'Edit', 'wp-product-licensing' ),
							'edit_item'             => __( 'Edit API Product', 'wp-product-licensing' ),
							'new_item'              => __( 'New API Product', 'wp-product-licensing' ),
							'view'                  => __( 'View API Product', 'wp-product-licensing' ),
							'view_item'             => __( 'View API Product', 'wp-product-licensing' ),
							'search_items'          => __( 'Search API Products', 'wp-product-licensing' ),
							'not_found'             => __( 'No API Products found', 'wp-product-licensing' ),
							'not_found_in_trash'    => __( 'No API Products found in trash', 'wp-product-licensing' ),
							'parent'                => __( 'Parent API Product', 'wp-product-licensing' ),
						),
					'description'         => __( 'This is where you can create and manage api products.', 'wp-product-licensing' ),
					'public'              => false,
					'show_ui'             => true,
					'capability_type'     => 'post',
					'publicly_queryable'  => false,
					'exclude_from_search' => true,
					'hierarchical'        => false,
					'rewrite'             => false,
					'query_var'           => false,
					'menu_icon'           => 'dashicons-products',
					'supports'            => array( 'title' ),
					'has_archive'         => false,
					'show_in_nav_menus'   => false
				)
			)
		);
	}
}

WPL_Post_Types::init();
