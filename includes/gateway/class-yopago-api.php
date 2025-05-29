<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YoPago_API {

	public static function build_payment_url(
		WC_Order $order,
		$gateway
	): ?string {
		$body = [
			'companyCode'     => sanitize_text_field( $gateway->code ),
			'codeTransaction' => $order->get_id() . '-' . random_int( 100, 999 ),
			'urlSuccess'      => WC_YOPAGO_PLUGIN_URL . 'callback.php',
			'urlFailed'       => WC_YOPAGO_PLUGIN_URL . 'callback.php',
			'billName'        => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
			'billNit'         => '000000',
			'email'           => $order->get_billing_email(),
			'generateBill'    => '1',
			'concept'         => 'Pago por servicios ' . $gateway->name_company,
			'currency'        => 'BOB',
			'amount'          => $order->get_total(),
			'messagePayment'  => __( 'Thank you for use our service', WC_YOPAGO_TEXT_DOMAIN ),
			'codeExternal'    => md5( $order->get_order_key() ),
		];

		$response = wp_remote_post( $gateway->api_url,
			[
				'method'  => 'POST',
				'headers' => [ 'Content-Type' => 'application/json' ],
				'body'    => json_encode( $body ),
				'timeout' => 60,
			] );

		if ( is_wp_error( $response ) ) {
			return NULL;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), TRUE );

		return $data['url'] ?? NULL;
	}
}
