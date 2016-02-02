<?php
/**
 * Functions for license specific things.
 *
 * @author   AxisThemes
 * @category Category
 * @package  WooCommerce/Functions
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the WC product for a licence.
 *
 * If the licence was purchased for a variation, this will return the parent.
 *
 * @param  int $product_or_variation_id
 * @return post
 */
function wpl_get_licence_product( $product_or_variation_id ) {
	if ( 'product_variation' === get_post_type( $product_or_variation_id ) ) {
		$variation  = get_post( $product_or_variation_id );
		$product_id = $variation->post_parent;
	} else {
		$product_id = $product_or_variation_id;
	}
	return get_post( $product_id );
}
