<?php
/**
 * WP Product Licensing Uninstall.
 *
 * Uninstalls the plugin and associated data.
 *
 * @author   AxisThemes
 * @category Core
 * @package  WooCommerce/Uninstaller
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;

$status_options = get_option( 'woocommerce_status_options', array() );

if ( ! empty( $status_options['uninstall_data'] ) ) {

	// Tables.
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}woocommerce_api_licenses" );
}
