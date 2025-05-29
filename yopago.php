<?php
/*
Plugin Name: WooCommerce YoPago Gateway
Plugin URI: https://yopago.com.bo/
Description: Formas de pago por tarjeta, QR, y transferencias bancarias para Woocommerce en Bolivia
Version: 1.0
Author: Ronald Torrez
Author URI: https://ronaldtorrez.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Constantes
define( 'WC_YOPAGO_NAME', 'YoPago' );
define( 'WC_YOPAGO_ID', 'yopago' );
define( 'WC_YOPAGO_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'WC_YOPAGO_PLUGIN_FILE', __FILE__ );
define( 'WC_YOPAGO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WC_YOPAGO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'WC_YOPAGO_TEXT_DOMAIN', 'wc-gateway-' . WC_YOPAGO_ID );
define( 'WC_YOPAGO_SETTING', 'woocommerce_' . WC_YOPAGO_ID . '_settings' );

require_once WC_YOPAGO_PLUGIN_PATH . 'includes/class-yopago-init.php';
require_once WC_YOPAGO_PLUGIN_PATH . 'includes/yopago-functions.php';
require_once WC_YOPAGO_PLUGIN_PATH . 'includes/hooks/class-yopago-checkout-hooks.php';

add_action( 'plugins_loaded', 'yopago_load_textdomain' );
add_action( 'woocommerce_init', [ 'YoPago_Checkout_Hooks', 'init' ] );

function yopago_load_textdomain(): void {
	load_plugin_textdomain( WC_YOPAGO_TEXT_DOMAIN,
		FALSE,
		dirname( WC_YOPAGO_PLUGIN_BASENAME ) . '/languages/' );
}
