<?php
/**
 * Installation related functions and actions.
 *
 * @class    WPL_Install
 * @version  1.0.0
 * @package  WooCommerce/Classes
 * @category Admin
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WPL_Install Class.
 */
class WPL_Install {

	/**
	 * Install WPL.
	 */
	public static function install() {
		self::create_tables();
	}

	/**
	 * Set up the database tables which the plugin needs to function.
	 *
	 * Tables:
	 * 		woocommerce_api_licenses - Table for storing API product licence keys for purchases.
	 */
	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = "
CREATE TABLE {$wpdb->prefix}woocommerce_api_licenses (
  license_id bigint(20) NOT NULL auto_increment,
  license_key varchar(64) NOT NULL,
  activation_email varchar(200) NOT NULL,
  user_id bigint(20) NULL,
  product_id bigint(20) NOT NULL,
  order_id bigint(20) NOT NULL DEFAULT 0,
  usage_limit bigint(20) NOT NULL DEFAULT 0,
  date_created datetime NOT NULL default '0000-00-00 00:00:00',
  date_expires datetime NULL default null,
  PRIMARY KEY  (license_id),
  UNIQUE KEY license_key (license_key),
  KEY activation_email (activation_email)
) $charset_collate;
		";

		dbDelta( $sql );
	}
}
