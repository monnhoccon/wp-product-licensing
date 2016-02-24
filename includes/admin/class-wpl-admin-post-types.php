<?php
/**
 * Post Types Admin
 *
 * @class    WPL_Admin_Post_Types
 * @version  1.0.0
 * @package  WooCommerce/Admin
 * @category Admin
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WPL_Admin_Post_Types Class
 *
 * Handles the edit posts views and some functionality on the edit post screen for post types.
 */
class WPL_Admin_Post_Types {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_post_updated_messages' ), 10, 2 );

		// Edit post screens
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );
		// add_action( 'edit_form_after_title', array( $this, 'edit_form_after_title' ) );

		// Meta-Box controls
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 20 );

		// Disable post type view mode options
		add_filter( 'view_mode_post_types', array( $this, 'disable_view_mode_options' ) );
	}

	/**
	 * Change messages when a post type is updated.
	 * @param  array $messages
	 * @return array
	 */
	public function post_updated_messages( $messages ) {
		global $post, $post_ID;

		$messages['api_product'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __( 'API Product updated.', 'wp-product-licensing' ),
			2 => __( 'Custom field updated.', 'wp-product-licensing' ),
			3 => __( 'Custom field deleted.', 'wp-product-licensing' ),
			4 => __( 'API Product updated.', 'wp-product-licensing' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'API Product restored to revision from %s', 'wp-product-licensing' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => __( 'API Product updated.', 'wp-product-licensing' ),
			7 => __( 'API Product saved.', 'wp-product-licensing' ),
			8 => __( 'API Product submitted.', 'wp-product-licensing' ),
			9 => sprintf( __( 'API Product scheduled for: <strong>%1$s</strong>.', 'wp-product-licensing' ),
			  date_i18n( __( 'M j, Y @ G:i', 'wp-product-licensing' ), strtotime( $post->post_date ) ) ),
			10 => __( 'API Product draft updated.', 'wp-product-licensing' )
		);

		return $messages;
	}

	/**
	 * Specify custom bulk actions messages for different post types.
	 * @param  array $bulk_messages
	 * @param  array $bulk_counts
	 * @return array
	 */
	public function bulk_post_updated_messages( $bulk_messages, $bulk_counts ) {

		$bulk_messages['api_product'] = array(
			'updated'   => _n( '%s product updated.', '%s products updated.', $bulk_counts['updated'], 'wp-product-licensing' ),
			'locked'    => _n( '%s product not updated, somebody is editing it.', '%s products not updated, somebody is editing them.', $bulk_counts['locked'], 'wp-product-licensing' ),
			'deleted'   => _n( '%s product permanently deleted.', '%s products permanently deleted.', $bulk_counts['deleted'], 'wp-product-licensing' ),
			'trashed'   => _n( '%s product moved to the Trash.', '%s products moved to the Trash.', $bulk_counts['trashed'], 'wp-product-licensing' ),
			'untrashed' => _n( '%s product restored from the Trash.', '%s products restored from the Trash.', $bulk_counts['untrashed'], 'wp-product-licensing' ),
		);

		return $bulk_messages;
	}

	/**
	 * Change title boxes in admin.
	 * @param  string $text
	 * @param  object $post
	 * @return string
	 */
	public function enter_title_here( $text, $post ) {
		switch ( $post->post_type ) {
			case 'api_product' :
				$text = __( 'API Product name', 'wp-product-licensing' );
			break;
		}

		return $text;
	}

	/**
	 * Print API Product description textarea field.
	 * @param WP_Post $post
	 */
	public function edit_form_after_title( $post ) {
		if ( 'api_product' === $post->post_type ) {
			?>
			<textarea id="woocommerce-api-product-description" name="excerpt" cols="5" rows="2" placeholder="<?php esc_attr_e( 'Description (optional)', 'wp-product-licensing' ); ?>"><?php echo $post->post_excerpt; ?></textarea>
			<?php
		}
	}

	/**
	 * Remove bloat.
	 */
	public function remove_meta_boxes() {
		remove_meta_box( 'slugdiv', 'api_product', 'normal' );
	}

	/**
	 * Add Meta boxes.
	 */
	public function add_meta_boxes() {
		add_meta_box( 'woocommerce-api-product-data', __( 'API Product Data', 'wp-product-licensing' ), array( $this, 'api_product_data' ), 'api_product', 'normal', 'high' );
	}

	/**
	 * Output API Product Data.
	 */
	public function api_product_data( $post ) {
		wp_nonce_field( 'woocommerce_save_data', 'woocommerce_meta_nonce' );
		?>
		<style type="text/css">
			#edit-slug-box, #minor-publishing-actions { display:none }
		</style>
		<div id="api_product_options" class="panel-wrap api_product_data">

			<ul class="api_product_data_tabs wc-tabs" style="display:none;">
				<?php
					$api_product_data_tabs = apply_filters( 'woocommerce_api_product_data_tabs', array(
						'general' => array(
							'label'  => __( 'General', 'wp-product-licensing' ),
							'target' => 'general_api_product_data',
							'class'  => 'general_api_product_data',
						)
					) );

					foreach ( $api_product_data_tabs as $key => $tab ) {
						?><li class="<?php echo $key; ?>_options <?php echo $key; ?>_tab <?php echo implode( ' ' , (array) $tab['class'] ); ?>">
							<a href="#<?php echo $tab['target']; ?>"><?php echo esc_html( $tab['label'] ); ?></a>
						</li><?php
					}
				?>
			</ul>
			<div id="general_api_product_data" class="panel woocommerce_options_panel"><?php

			?></div>
		</div>
		<?php
	}

	/**
	 * Removes api_product from the list of post types that support "View Mode" switching.
	 * View mode is seen on posts where you can switch between list or excerpt. Our post types don't support
	 * it, so we want to hide the useless UI from the screen options tab.
	 *
	 * @param  array $post_types Array of post types supporting view mode
	 * @return array             Array of post types supporting view mode, without api_product
	 */
	public function disable_view_mode_options( $post_types ) {
		unset( $post_types['api_product'] );
		return $post_types;
	}
}

new WPL_Admin_Post_Types();
