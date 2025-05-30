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

define( 'WC_YOPAGO_NAME', 'YoPago' );
define( 'WC_YOPAGO_ID', 'yopago' );
define( 'WC_YOPAGO_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'WC_YOPAGO_PLUGIN_FILE', __FILE__ );
define( 'WC_YOPAGO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WC_YOPAGO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'WC_YOPAGO_SETTING', 'woocommerce_' . WC_YOPAGO_ID . '_settings' );

require_once WC_YOPAGO_PLUGIN_PATH . 'includes/core/class-yopago.php';

register_activation_hook( __FILE__, [ 'YoPago_Activator', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'YoPago_Deactivator', 'deactivate' ] );

YoPago::run();
