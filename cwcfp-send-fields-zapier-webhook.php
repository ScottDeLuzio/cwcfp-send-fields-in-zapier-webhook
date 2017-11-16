<?php
	/*
  	Plugin Name: CWCFP Send Fields In Zapier Webhook
  	Plugin URI: https://conditionalcheckoutfields.com/
  	Description: Add conditional fields as line items after checkout
   	Version: 1.0.0
  	Author: Scott DeLuzio
  	Author URI: https://scottdeluzio.com
	  */
/* Copyright 2017 Scott DeLuzio */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_action( 'woocommerce_checkout_update_order_meta', 'add_conditional_fields_as_line_items' );

function add_conditional_fields_as_line_items( $order_id ){
	$field_count 	= get_option('pro_conditional_fields_qty');
	$conditional_a 	= 1;
	$data			= false;

	for ( $conditional_a; $conditional_a <= $field_count; $conditional_a++ ) {
		$quantity = cwcfp_get_field_quantity( $conditional_a );
		for( $i = 1; $i <= $quantity; $i++ ){
			if ( isset( $_POST[ 'conditional_field_'. $conditional_a . '-' . $i ] ) && '' != trim( $_POST[ 'conditional_field_' . $conditional_a . '-' . $i ] ) ) {

				$field_title		= get_option( 'pro_conditional_fields_title_' . $conditional_a );

				$data[$field_title]	= sanitize_text_field( $_POST[ 'conditional_field_' . $conditional_a . '-' . $i ] );
			}
		}
	}

	if ( $data ){
		$data['id']	= $order_id;
		$jsondata	= json_encode( $data );

		$headers	= array();

		$send_data	= array(
			'sslverify'	=>	false,
			'ssl'		=>	true,
			'body'		=>	$jsondata,
			'headers'	=>	$headers,
		);

		// Replace with your Zapier webhook URL
		$response	= wp_remote_post( 'https://hooks.zapier.com/hooks/catch/12345/abcdef/', $send_data );
	}
}