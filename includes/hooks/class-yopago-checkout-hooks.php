<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YoPago_Checkout_Hooks {

	public static function init(): void {
		add_action( 'woocommerce_review_order_before_payment', [ self::class, 'show_checkout_message' ] );
	}

	public static function show_checkout_message(): void {
		if ( ! is_checkout() ) {
			return;
		}

		$gateways = WC()->payment_gateways()->get_available_payment_gateways();

		if ( isset( $gateways[ WC_YOPAGO_ID ] ) ) {
			$gateway = $gateways[ WC_YOPAGO_ID ];
			if ( $gateway instanceof WC_Gateway_YoPago && ! empty( $gateway->checkout_msg ) ) {
				echo '<div class="woocommerce-info">' . esc_html( $gateway->checkout_msg ) . '</div>';
			}
		}
	}
}
