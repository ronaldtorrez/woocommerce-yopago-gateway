<?php

/*
Plugin Name: WooCommerce YoPago Gateway
Plugin URI: https://woothemes.com/woocommerce
Description: Extends WooCommerce with an <enter name> gateway.
Version: 1.0
Author: Ronald Torrez
Author URI: https://ronaldtorrez.com/
	Copyright: © 2009-2011 WooThemes.
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Plugin version
 */
define( 'WC_GATEWAY_NAME_VERSION', '1.0' );
/**
 * Plugin path
 */
define( 'WC_GATEWAY_NAME_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
/**
 * Plugin URL
 */
define( 'WC_GATEWAY_NAME_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
/**
 * Plugin file
 */
define( 'WC_GATEWAY_NAME_PLUGIN_FILE', __FILE__ );
/**
 * Plugin basename
 */
define( 'WC_GATEWAY_NAME_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
/**
 * Plugin text domain
 */
define( 'WC_GATEWAY_NAME_TEXT_DOMAIN', 'wc-gateway-yopago' );
/**
 * Load the gateway class
 */
function wc_gateway_yopago_init() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	require_once WC_GATEWAY_NAME_PLUGIN_PATH . 'includes/class-wc-gateway-yopago.php';

	add_filter( 'woocommerce_payment_gateways', 'wc_gateway_yopago_add_gateway' );
}
