<?php
/**
 * Plugin Name: WP Product Licensing
 * Plugin URI: http://www.axisthemes.com/wp-product-licensing/
 * Description: WordPress Premium Products Licensing for WooCommerce.
 * Version: 1.0.0
 * Author: AxisThemes
 * Author URI: http://axisthemes.com
 * License: GPLv3 or later
 * Text Domain: wp-product-licensing
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WP_Product_Licensing' ) ) :

/**
 * WP_Product_Licensing main Class.
 */
class WP_Product_Licensing {

	/**
	 * Plugin version.
	 * @var string
	 */
	const VERSION = '1.0.0';

	/**
	 * Instance of this class.
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin.
	 */
	private function __construct() {
		// Load plugin text domain.
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Checks with WooCommerce is installed.
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.3', '>=' ) ) {
			$this->includes();
		} else {
			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
		}
	}

	/**
	 * Return an instance of this class.
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Locales found in:
	 *      - WP_LANG_DIR/wp-product-licensing/wp-product-licensing-LOCALE.mo
	 *      - WP_LANG_DIR/plugins/wp-product-licensing-LOCALE.mo
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-product-licensing' );

		load_textdomain( 'wp-product-licensing', WP_LANG_DIR . '/wp-product-licensing/wp-product-licensing-' . $locale . '.mo' );
		load_plugin_textdomain( 'wp-product-licensing', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Includes.
	 */
	private function includes() {
		include_once( 'includes/class-wpl-post-types.php' );

		if ( is_admin() ) {
			include_once( 'includes/admin/class-wpl-admin-menus.php' );
			include_once( 'includes/admin/class-wpl-admin-post-types.php' );
		}
	}

	/**
	 * WooCommerce fallback notice.
	 * @return string
	 */
	public function woocommerce_missing_notice() {
		echo '<div class="error notice is-dismissible"><p>' . sprintf( __( 'WP Product License depends on the last version of %s or later to work!', 'wp-product-licensing' ), '<a href="http://www.woothemes.com/woocommerce/" target="_blank">' . __( 'WooCommerce 2.3', 'wp-product-licensing' ) . '</a>' ) . '</p></div>';
	}
}

add_action( 'plugins_loaded', array( 'WP_Product_Licensing', 'get_instance' ), 0 );

endif;
