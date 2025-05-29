<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yopago_init_gateway(): void {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		add_action( 'admin_notices',
			function() {
				echo '<div class="error"><p><strong>'
				     . WC_YOPAGO_NAME
				     . ':</strong> '
				     .
				     __( 'WooCommerce is not active. Activate WooCommerce to use this payment gateway.',
					     WC_YOPAGO_TEXT_DOMAIN )
				     .
				     '</p></div>';
			} );

		deactivate_plugins( WC_YOPAGO_PLUGIN_BASENAME );

		return;
	}

	require_once WC_YOPAGO_PLUGIN_PATH
	             . 'includes/gateway/class-gateway-yopago.php';

	add_filter( 'woocommerce_payment_gateways', 'yopago_register_gateway' );
}

function yopago_register_gateway( $methods ) {
	$methods[] = 'WC_Gateway_YoPago';

	return $methods;
}

add_action( 'plugins_loaded', 'yopago_init_gateway', 0 );
