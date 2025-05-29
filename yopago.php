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
/**
 * Plugin version
 */
define( 'WCG_YOPAGO_NAME', 'YoPago' );

/**
 * Plugin ID
 */
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
 * Initializes the YoPago payment gateway for WooCommerce.
 *
 * - Checks if the WC_Payment_Gateway class exists (WooCommerce active).
 * - Includes the main gateway class if the file exists.
 * - Adds the gateway to the list of WooCommerce payment methods.
 *
 * @return void
 */
function yopago_init_gateway(): void {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		add_action( 'admin_notices',
			function() {
				echo '<div class="error"><p><strong>' . WCG_YOPAGO_NAME . ':</strong> '
				     . __(
					     'WooCommerce is not active. Activate WooCommerce to use this payment gateway.',
					     WCG_YOPAGO_TEXT_DOMAIN
				     )
				     . '</p></div>';
			}
		);

		deactivate_plugins( WCG_YOPAGO_PLUGIN_BASENAME );

		return;
	}

	$class_path = WCG_YOPAGO_PLUGIN_PATH . 'includes/class-gateway-yopago.php';

	if ( file_exists( $class_path ) ) {
		require_once $class_path;
	}

	add_filter( 'woocommerce_payment_gateways', 'yopago_register_gateway' );
}

function yopago_register_gateway( $methods ) {
	$methods[] = 'WC_Gateway_YoPago';

	return $methods;
}

add_action( 'plugins_loaded', 'yopago_init_gateway', 0 );

/**
 * Activation hook
 */

function yopago_on_activate(): void {
	if (
		! class_exists( 'WooCommerce' )
		|| ! class_exists( 'WC_Payment_Gateway' )
		|| ! is_plugin_active( 'woocommerce/woocommerce.php' )
	) {
		return;
	}

	// Check if the gateway is already registered
	if ( ! in_array( 'WC_Gateway_YoPago', WC()->payment_gateways()->get_available_payment_gateways(), TRUE ) ) {
		// Register the gateway
		WC()->payment_gateways()->init();
	}
}

register_activation_hook( __FILE__, 'yopago_on_activate' );

/**
 * Deactivation hook
 */
function yopago_on_deactivate(): void {
	delete_option( 'woocommerce_yopago_settings' );
}

register_deactivation_hook( __FILE__, 'yopago_on_deactivate' );

/**
 * Uninstall hook
 */

function yopago_on_uninstall(): void {
	delete_option( 'woocommerce_yopago_settings' );
}

register_uninstall_hook( __FILE__, 'yopago_on_uninstall' );

/**
 * Load the plugin text domain for translations
 */
function yopago_load_textdomain(): void {
	load_plugin_textdomain(
		WCG_YOPAGO_TEXT_DOMAIN,
		FALSE,
		dirname( WCG_YOPAGO_PLUGIN_BASENAME ) . '/languages/'
	);
}

add_action( 'plugins_loaded', 'yopago_load_textdomain' );
