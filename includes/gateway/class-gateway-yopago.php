<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/class-yopago-settings.php';
require_once __DIR__ . '/class-yopago-render.php';
require_once __DIR__ . '/class-yopago-api.php';

class WC_Gateway_YoPago extends WC_Payment_Gateway {

	public $title;
	public $description;
	public string $code;
	public string $name_company;
	public string $api_url;
	public string $success_url;
	public string $error_url;
	public string $checkout_msg;

	public function __construct() {
		$this->id                 = WC_YOPAGO_ID;
		$this->icon               = apply_filters( 'woocommerce_' . WC_YOPAGO_ID . '_icon',
			WC_YOPAGO_PLUGIN_URL . 'assets/images/yopago.png' );
		$this->has_fields         = FALSE;
		$this->method_title       = WC_YOPAGO_NAME;
		$this->method_description =
			__( 'Multiple payment methods including credit/debit cards, QR, and Bolivian banks.',
				WC_YOPAGO_ID );

		$this->init_form_fields();
		$this->init_settings();

		$this->title        = $this->get_option( 'title' );
		$this->description  = $this->get_option( 'description' );
		$this->code         = $this->get_option( 'code' );
		$this->name_company = $this->get_option( 'name_company' );
		$this->api_url      = $this->get_option( 'api_url' );
		$this->success_url  = $this->get_option( 'success_url' );
		$this->error_url    = $this->get_option( 'error_url' );
		$this->checkout_msg = $this->get_option( 'checkout_msg' );

		add_action( 'woocommerce_receipt_' . $this->id, [ $this, 'render_iframe' ] );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
	}

	public function init_form_fields(): void {
		$this->form_fields = YoPago_Settings::get_form_fields();
	}

	public function process_payment( $order_id ): array {
		$order = wc_get_order( $order_id );
		$order->update_status(
			'pending',
			sprintf(
				__( 'Awaiting %s payment', WC_YOPAGO_ID ),
				WC_YOPAGO_NAME
			) );

		return [
			'result'   => 'success',
			'redirect' => $order->get_checkout_payment_url( TRUE ),
		];
	}

	public function render_iframe( $order_id ): void {
		YoPago_Render::render_iframe( $order_id, $this );
	}
}
