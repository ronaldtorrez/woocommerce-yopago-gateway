<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yopago_on_activate(): void {
	if (
		! class_exists( 'WooCommerce' )
		|| ! class_exists( 'WC_Payment_Gateway' )
		|| ! is_plugin_active( 'woocommerce/woocommerce.php' )
	) {
		return;
	}

	if ( ! in_array( 'WC_Gateway_YoPago',
		WC()->payment_gateways()->get_available_payment_gateways(),
		TRUE ) ) {
		WC()->payment_gateways()->init();
	}
}

function yopago_on_deactivate(): void {
	delete_option( WC_YOPAGO_SETTING );
}

register_activation_hook( WC_YOPAGO_PLUGIN_FILE, 'yopago_on_activate' );
register_deactivation_hook( WC_YOPAGO_PLUGIN_FILE, 'yopago_on_deactivate' );
