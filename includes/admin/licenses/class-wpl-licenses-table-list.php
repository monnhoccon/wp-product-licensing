<?php
/**
 * WooCommerce Licenses Table List
 *
 * @class    WPL_Licenses_Table_List
 * @version  1.0.0
 * @package  WooCommerce/Admin/Licenses
 * @category Admin
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * WPL_Licenses_Table_List Class.
 */
class WPL_Licenses_Table_List extends WP_List_Table {

	/**
	 * Initialize the license table list.
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => __( 'License', 'wp-product-licensing' ),
			'plural'   => __( 'Licenses', 'wp-product-licensing' ),
			'ajax'     => false
		) );
	}

	/**
	 * No items found text.
	 */
	public function no_items() {
		_e( 'No Licenses found.', 'wp-product-licensing' );
	}

	/**
	 * Get list columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'cb'               => '<input type="checkbox" />',
			'license_key'      => __( 'License Key', 'wp-product-licensing' ),
			'activation_email' => __( 'Activation Email', 'wp-product-licensing' ),
			'user'             => __( 'User', 'wp-product-licensing' ),
			'product'          => __( 'Product', 'wp-product-licensing' ),
			'order_id'         => __( 'Order ID', 'wp-product-licensing' ),
			'usage_limit'      => __( 'Usage / Limit', 'wp-product-licensing' ),
			'date_created'     => __( 'Date Created', 'wp-product-licensing' ),
			'date_expires'     => __( 'Date Expires', 'wp-product-licensing' ),
		);
	}

	/**
	 * Get list sortable columns.
	 *
	 * @return array
	 */
	protected function get_sortable_columns() {
		return array(
			'activation_email' => array( 'activation_email', false ),
			'user'             => array( 'user_id', false ),
			'product'          => array( 'product_id', false ),
			'order_id'         => array( 'order_id', false ),
			'date_created'     => array( 'date_created', true ),
			'date_expires'     => array( 'date_expires', false ),
		);
	}

	/**
	 * Column cb.
	 *
	 * @param  object $license
	 * @return string
	 */
	public function column_cb( $license ) {
		return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $license->license_id );
	}

	/**
	 * Return license key column.
	 *
	 * @param  object $license
	 * @return string
	 */
	public function column_license_key( $license ) {
		$url = admin_url( 'admin.php?page=wpl-licenses&edit-license=' . $license->license_id );

		$output = '<a class="row-title" href="' . esc_url( $url ) . '"><code>' . esc_html( $license->license_key ) . '</code></a>';

		// Get actions
		$actions = array(
			'id'    => sprintf( __( 'ID: %d', 'wp-product-licensing' ), $license->license_id ),
			'edit'  => '<a href="' . esc_url( $url ) . '">' . __( 'View/Edit', 'wp-product-licensing' ) . '</a>',
			'trash' => '<a class="submitdelete" title="' . esc_attr__( 'Revoke API License Key', 'wp-product-licensing' ) . '" href="' . esc_url( wp_nonce_url( add_query_arg( array( 'revoke-license' => $license->license_id ), admin_url( 'admin.php?page=wpl-licenses' ) ), 'revoke' ) ) . '">' . __( 'Revoke', 'wp-product-licensing' ) . '</a>'
		);

		$row_actions = array();

		foreach ( $actions as $action => $link ) {
			$row_actions[] = '<span class="' . esc_attr( $action ) . '">' . $link . '</span>';
		}

		$output .= '<div class="row-actions">' . implode(  ' | ', $row_actions ) . '</div>';

		return $output;
	}

	/**
	 * Return activation email column.
	 *
	 * @param  object $license
	 * @return string
	 */
	public function column_activation_email( $license ) {
		return '<span class="email">' . $license->activation_email . '</span>';
	}

	/**
	 * Return user column.
	 *
	 * @param  object $license
	 * @return string
	 */
	public function column_user( $license ) {
		$user = get_user_by( 'id', $license->user_id );

		if ( ! $user ) {
			return '<span class="na">&ndash;</span>';
		}

		$user_name = ! empty( $user->data->display_name ) ? $user->data->display_name : $user->data->user_login;

		if ( current_user_can( 'edit_user' ) ) {
			return '<a href="' . esc_url( add_query_arg( array( 'user_id' => $user->ID ), admin_url( 'user-edit.php' ) ) ) . '">' . esc_html( $user_name ) . '</a>';
		}

		return esc_html( $user_name );
	}

	/**
	 * Return product column.
	 *
	 * @param  object $license
	 * @return string
	 */
	public function column_product( $license ) {
		if ( $product = wpl_get_licence_product( $license->product_id ) ) {
			return '<a href="' . esc_url( add_query_arg( array( 'post' => absint( $product->ID ), 'action' => 'edit' ), admin_url( 'post.php' ) ) ) . '">' . esc_html( $product->post_title ) . '</a>';
		}

		return '<span class="na">&ndash;</span>';
	}

	/**
	 * Return order ID column.
	 *
	 * @param  object $license
	 * @return string
	 */
	public function column_order_id( $license ) {
		if ( $order = wc_get_order( $license->order_id ) ) {
			return '<a href="' . esc_url( add_query_arg( array( 'post' => absint( $order->id ), 'action' => 'edit' ), admin_url( 'post.php' ) ) ) . '">' . esc_html( $order->id ) . '&rarr;</a>';
		}

		return '<span class="na">&ndash;</span>';
	}

	/**
	 * Return usage limit column.
	 *
	 * @param  object $license
	 * @return string
	 */
	public function column_usage_limit( $license ) {
		global $wpdb;

		$usage_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( license_id ) FROM {$wpdb->prefix}woocommerce_api_licenses WHERE license_key=%s;", $license->license_key ) );
		$usage_limit = absint( $license->usage_limit );
		$usage_url   = sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=wpl-activations&license=' . $license->license_id ), absint( $usage_count ) );

		if ( $usage_limit ) {
			return sprintf( __( '%s / %s', 'wp-product-licensing' ), $usage_url, $usage_limit );
		} else {
			return sprintf( __( '%s / &infin;', 'wp-product-licensing' ), $usage_url );
		}
	}

	/**
	 * Return date created column.
	 *
	 * @param  object $license
	 * @return string
	 */
	public function column_date_created( $license ) {
		if ( ! empty( $license->date_created ) ) {
			return date_i18n( wc_date_format(), strtotime( $license->date_created ) );
		}

		return __( 'Unknown', 'wp-product-licensing' );
	}

	/**
	 * Return date expires column.
	 *
	 * @param  object $license
	 * @return string
	 */
	public function column_date_expires( $license ) {
		if ( ! empty( $license->date_expires ) ) {
			return date_i18n( wc_date_format(), strtotime( $license->date_expires ) );
		}

		return __( 'Unknown', 'wp-product-licensing' );
	}

	/**
	 * Get the status label for licenses.
	 *
	 * @param  string   $status_name
	 * @param  stdClass $status
	 *
	 * @return array
	 */
	private function get_status_label( $status_name, $status ) {

	}

	/**
	 * Get bulk actions.
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {
		return array(
			'revoke' => __( 'Revoke', 'wp-product-licensing' )
		);
	}

	/**
	 * Prepare table list items.
	 */
	public function prepare_items() {
		global $wpdb;

		$per_page = $this->get_items_per_page( 'wpl_licenses_per_page' );
		$columns  = $this->get_columns();
		$hidden   = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		// Column headers
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$current_page = $this->get_pagenum();
		if ( 1 < $current_page ) {
			$offset = $per_page * ( $current_page - 1 );
		} else {
			$offset = 0;
		}

		$search   = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';
		$orderby  = isset( $_REQUEST['orderby'] ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'date_created';
		$order    = isset( $_REQUEST['order'] ) && 'DESC' === strtoupper( $_REQUEST['order'] ) ? 'DESC' : 'ASC';

		if ( $search ) {
			$search = "AND ( license_key LIKE '%" . esc_sql( $wpdb->esc_like( $search ) ) . "%' OR activation_email LIKE '%" . esc_sql( $wpdb->esc_like( $search ) ) . "%' ) ";
		}

		// Get the API licenses
		$licenses = $wpdb->get_results(
			"SELECT * FROM {$wpdb->prefix}woocommerce_api_licenses WHERE 1 = 1 {$search}" .
			$wpdb->prepare( "ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d;", $per_page, $offset )
		);

		$count = $wpdb->get_var( "SELECT COUNT(license_id) FROM {$wpdb->prefix}woocommerce_api_licenses WHERE 1 = 1 {$search};" );

		$this->items = $licenses;

		// Set the pagination
		$this->set_pagination_args( array(
			'total_items' => $count,
			'per_page'    => $per_page,
			'total_pages' => ceil( $count / $per_page )
		) );
	}

	/**
	 * Get a list of hidden columns.
	 * @return array
	 */
	protected function get_hidden_columns() {
		return get_hidden_columns( $this->screen );
	}
}
