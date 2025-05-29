<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YoPago_Activator {

	public static function activate(): void {
		if (
			! class_exists( 'WooCommerce' )
			||
			! class_exists( 'WC_Payment_Gateway' )
			||
			! is_plugin_active( 'woocommerce/woocommerce.php' )
		) {
			return;
		}

		if (
			! in_array(
				'WC_Gateway_YoPago',
				WC()->payment_gateways()->get_available_payment_gateways(),
				TRUE
			)
		) {
			WC()->payment_gateways()->init();
		}
	}
}
