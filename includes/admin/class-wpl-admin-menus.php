<?php
/**
 * Setup menus in WP admin.
 *
 * @class    WPL_Admin_Menus
 * @version  1.0.0
 * @package  WooCommerce/Admin
 * @category Admin
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WPL_Admin_Menus Class.
 */
class WPL_Admin_Menus {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		// Add menus
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
		add_action( 'admin_menu', array( $this, 'activations_menu' ), 20 );
		add_action( 'admin_menu', array( $this, 'add_license_menu' ), 50 );
		add_filter( 'menu_order', array( $this, 'menu_order' ) );
		add_filter( 'woocommerce_screen_ids', array( $this, 'wc_screen_ids' ) );
	}

	/**
	 * Add menu items.
	 */
	public function admin_menu() {
		add_menu_page( __( 'Licenses', 'wp-product-licensing' ), __( 'Licenses', 'wp-product-licensing' ), 'manage_options', 'wpl-licenses' , array( $this, 'licenses_page' ), 'dashicons-lock', '55.8' );
	}

	/**
	 * Add menu items.
	 */
	public function activations_menu() {
		add_submenu_page( 'wpl-licenses', __( 'Activations', 'wp-product-licensing' ),  __( 'Activations', 'wp-product-licensing' ) , 'manage_options', 'wpl-activations', array( $this, 'activations_page' ) );
	}

	/**
	 * Add menu items.
	 */
	public function add_license_menu() {
		add_submenu_page( 'wpl-licenses', __( 'Add License', 'wp-product-licensing' ),  __( 'Add License', 'wp-product-licensing' ) , 'manage_options', 'wpl-add-license', array( $this, 'add_licence_page' ) );
	}

	/**
	 * Reorder the menu items in admin.
	 *
	 * @param  mixed $menu_order
	 * @return array
	 */
	public function menu_order( $menu_order ) {
		// Initialize our custom order array
		$wpl_menu_order = array();

		// Get index of API product menu
		$api_products = array_search( 'edit.php?post_type=api_product', $menu_order );

		// Loop through menu order and do some rearranging
		foreach ( $menu_order as $index => $item ) {

			if ( ( ( 'wpl-licenses' ) == $item ) ) {
				$wpl_menu_order[] = 'edit.php?post_type=api_product';
				$wpl_menu_order[] = $item;
				unset( $menu_order[ $api_products ] );
			} else {
				$wpl_menu_order[] = $item;
			}
		}

		return $wpl_menu_order;
	}

	/**
	 * WooCommerce screen ids.
	 *
	 * @param  array $screen_ids
	 * @return array
	 */
	public function wc_screen_ids( $screen_ids ) {
		$wc_screen_id = sanitize_title( __( 'License', 'wp-product-licensing' ) );
		$screen_ids[] = $wc_screen_id . '_page_wpl-add-license';
		return $screen_ids;
	}

	/**
	 * Init the license page.
	 */
	public function licenses_page() {

	}

	/**
	 * Init the activation page.
	 */
	public function activations_page() {

	}

	/**
	 * Init the add license page.
	 */
	public function add_licence_page() {

	}
}

return new WPL_Admin_Menus();