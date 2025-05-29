<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YoPago_Loader {

	public static function init(): void {
		add_action( 'plugins_loaded', [ self::class, 'init_gateway' ], 0 );
		add_action( 'plugins_loaded', [ self::class, 'load_textdomain' ] );
	}

	public static function init_gateway(): void {
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
			add_action( 'admin_notices',
				function() {
					echo '<div class="error"><p><strong>' . WC_YOPAGO_NAME . ':</strong> ' .
					     __( 'WooCommerce is not active. Activate WooCommerce to use this payment gateway.',
						     WC_YOPAGO_TEXT_DOMAIN ) . '</p></div>';
				} );
			deactivate_plugins( WC_YOPAGO_PLUGIN_BASENAME );

			return;
		}

		require_once WC_YOPAGO_PLUGIN_PATH . 'includes/gateway/class-gateway-yopago.php';

		add_filter( 'woocommerce_payment_gateways',
			function( $methods ) {
				$methods[] = 'WC_Gateway_YoPago';

				return $methods;
			} );
	}

	public static function load_textdomain(): void {
		load_plugin_textdomain(
			WC_YOPAGO_TEXT_DOMAIN,
			FALSE,
			dirname( WC_YOPAGO_PLUGIN_BASENAME ) . '/languages/'
		);
	}
}
