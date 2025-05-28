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
define( 'WCG_YOPAGO_NAME', 'YoPago' );
define( 'WCG_YOPAGO_ID', 'yopago' );

/**
 * Plugin path
 */
define( 'WCG_YOPAGO_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Plugin URL
 */
define( 'WCG_YOPAGO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin file
 */
define( 'WCG_YOPAGO_PLUGIN_FILE', __FILE__ );

/**
 * Plugin basename
 */
define( 'WCG_YOPAGO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Plugin text domain
 */
define( 'WCG_YOPAGO_TEXT_DOMAIN', 'wc-gateway-' . WCG_YOPAGO_ID );

/**
 * Load the gateway class
 */
function wc_gateway_yopago_init(): void {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	require_once WCG_YOPAGO_PLUGIN_PATH . 'includes/class-wc-gateway-yopago.php';

	add_filter( 'woocommerce_payment_gateways', 'wc_gateway_yopago_add_gateway' );
}

function wc_gateway_yopago_add_gateway( $methods ) {
	$methods[] = 'WC_Gateway_YoPago';

	return $methods;
}

add_action( 'plugins_loaded', 'wc_gateway_yopago_init', 0 );

/**
 * Activation hook
 */

function wc_gateway_yopago_activate(): void {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	// Check if the gateway is already registered
	if ( ! in_array( 'WC_Gateway_YoPago', WC()->payment_gateways()->get_available_payment_gateways(), TRUE ) ) {
		// Register the gateway
		WC()->payment_gateways()->init();
	}

	// Optionally, you can add default settings or perform other activation tasks here
	// For example, you can set default options for the gateway
	// update_option( 'woocommerce_yopago_settings',
	// 	[
	// 		'enabled'                => 'yes',
	// 		'title'                  => __( 'Paga con Yo Pago', WCG_YOPAGO_TEXT_DOMAIN ),
	// 		'description'            => __( 'Paga con Yo Pago', WCG_YOPAGO_TEXT_DOMAIN ),
	// 		'codigo'                 => '',
	// 		'name_company'           => '',
	// 		'url_yopago'             => '',
	// 		'url_thank_you'          => '',
	// 		'url_error'              => '',
	// 		'mensaje_chekcout_title' => __( 'Gracias por su compra', WCG_YOPAGO_TEXT_DOMAIN ),
	// 	] );
}

register_activation_hook( __FILE__, 'wc_gateway_yopago_activate' );

/**
 * Deactivation hook
 */
function wc_gateway_yopago_deactivate(): void {
	// Optionally, you can perform cleanup tasks here
	// For example, you can remove the gateway settings
	delete_option( 'woocommerce_yopago_settings' );
}

register_deactivation_hook( __FILE__, 'wc_gateway_yopago_deactivate' );

/**
 * Uninstall hook
 */

function wc_gateway_yopago_uninstall(): void {
	// Optionally, you can perform cleanup tasks here
	// For example, you can remove the gateway settings
	delete_option( 'woocommerce_yopago_settings' );
}

register_uninstall_hook( __FILE__, 'wc_gateway_yopago_uninstall' );

/**
 * Load the gateway class
 */
function wc_gateway_yopago_load_gateway_class(): void {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	require_once WCG_YOPAGO_PLUGIN_PATH . 'includes/class-wc-gateway-yopago.php';
}

add_action( 'plugins_loaded', 'wc_gateway_yopago_load_gateway_class', 0 );

/**
 * Load the plugin text domain for translations
 */
function wc_gateway_yopago_load_textdomain(): void {
	load_plugin_textdomain( WCG_YOPAGO_TEXT_DOMAIN,
		FALSE,
		dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'wc_gateway_yopago_load_textdomain' );
