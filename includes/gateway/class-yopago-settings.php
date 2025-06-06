<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YoPago_Settings {

	public static function get_form_fields(): array {
		$order_received_url =
			wc_get_endpoint_url( 'order-received',
				'',
				trailingslashit( wc_get_page_permalink( 'checkout' ) ) );

		return [
			'enabled'                     => [
				'title'   => __( 'Enable/Disable', WC_YOPAGO_ID ),
				'type'    => 'checkbox',
				'label'   => sprintf(
					__( 'Enable %s gateway', WC_YOPAGO_ID ),
					WC_YOPAGO_NAME
				),
				'default' => 'no',
			],
			'title'                       => [
				'title'       => __( 'Title', WC_YOPAGO_ID ),
				'type'        => 'text',
				'description' => __( 'Enter a title for the payment method.', WC_YOPAGO_ID ),
				'default'     => sprintf(
					__( 'Pay with %s', WC_YOPAGO_ID ),
					WC_YOPAGO_NAME
				),
				'desc_tip'    => TRUE,
			],
			'description'                 => [
				'title'       => __( 'Description', WC_YOPAGO_ID ),
				'type'        => 'textarea',
				'description' => __( 'Enter a description for the payment method.', WC_YOPAGO_ID ),
				'default'     => __( 'Multiple payment methods including credit/debit cards, QR, and Bolivian banks.',
					WC_YOPAGO_ID ),
				'desc_tip'    => TRUE,
			],
			'code'                        => [
				'title'       => __( 'Code', WC_YOPAGO_ID ),
				'type'        => 'text',
				'description' => __( 'Enter the code for the payment method.', WC_YOPAGO_ID ),
				'default'     => '',
				'placeholder' => 'ej. ABC1-DE12-F123-1234',
				'desc_tip'    => TRUE,
			],
			'name_company'                => [
				'title'       => __( 'Company Name', WC_YOPAGO_ID ),
				'type'        => 'text',
				'description' => __( 'Enter the name of your company.', WC_YOPAGO_ID ),
				'default'     => '',
				'desc_tip'    => TRUE,
			],
			'api_url'                     => [
				'title'       => sprintf(
					__( '%s url', WC_YOPAGO_ID ),
					WC_YOPAGO_NAME
				),
				'type'        => 'text',
				'description' => __( 'Enter the URL for payment processing.', WC_YOPAGO_ID ),
				'default'     => 'https://yopago.com.bo/pay/api/generateUrl',
				'desc_tip'    => TRUE,
			],
			'success_url'                 => [
				'title'       => __( 'Thank You URL', WC_YOPAGO_ID ),
				'type'        => 'text',
				'description' => __( 'Enter the URL to redirect after successful payment.', WC_YOPAGO_ID ),
				'default'     => $order_received_url,
				'desc_tip'    => TRUE,
			],
			'error_url'                   => [
				'title'       => __( 'Error URL', WC_YOPAGO_ID ),
				'type'        => 'text',
				'description' => __( 'Enter the URL to redirect in case of payment error.', WC_YOPAGO_ID ),
				'default'     => $order_received_url,
				'desc_tip'    => TRUE,
			],
			'checkout_msg'                => [
				'title'       => __( 'Checkout Message Title', WC_YOPAGO_ID ),
				'type'        => 'text',
				'description' => __( 'Enter a title for the checkout message.', WC_YOPAGO_ID ),
				'default'     => sprintf(
					__( 'Pay with %s', WC_YOPAGO_ID ),
					WC_YOPAGO_NAME
				),
				'desc_tip'    => TRUE,
			],
			'currency_conversion_enabled' => [
				'title'       => __( 'Currency Conversion', WC_YOPAGO_ID ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable currency conversion', WC_YOPAGO_ID ),
				'default'     => 'no',
				'description' => __( 'Allow currency conversion for payments.', WC_YOPAGO_ID ),
			],
			'currency_conversion_table'   => [
				'type'  => 'currency_conversion_table',
				'title' => __( 'Currency Conversion Rates', WC_YOPAGO_ID ),
			],

		];
	}
}
