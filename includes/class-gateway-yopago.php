<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
		$this->id                 = WCG_YOPAGO_ID;
		$this->icon               =
			apply_filters(
				'woocommerce_' . WCG_YOPAGO_ID . '_icon',
				WCG_YOPAGO_PLUGIN_URL . 'assets/images/yopago.png'
			);
		$this->has_fields         = FALSE;
		$this->method_title       = __( WCG_YOPAGO_NAME, WCG_YOPAGO_TEXT_DOMAIN );
		$this->method_description = __(
			'Multiple payment methods including credit/debit cards, QR, and Bolivian banks.',
			WCG_YOPAGO_TEXT_DOMAIN
		);

		$this->init_form_fields();
		$this->init_settings();

		// Load settings
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
		$order_received_url = wc_get_endpoint_url(
			'order-received',
			'',
			trailingslashit( wc_get_page_permalink( 'checkout' ) )
		);

		$this->form_fields = [
			'enabled'      => [
				'title'   => __( 'Enable/Disable', WCG_YOPAGO_TEXT_DOMAIN ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable ' . WCG_YOPAGO_NAME . ' Gateway', WCG_YOPAGO_TEXT_DOMAIN ),
				'default' => 'no',
			],
			'title'        => [
				'title'       => __( 'Title', WCG_YOPAGO_TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Enter a title for the payment method.', WCG_YOPAGO_TEXT_DOMAIN ),
				'default'     => _x( 'Pay with ' . WCG_YOPAGO_NAME . '.',
					'Pay with ' . WCG_YOPAGO_NAME . '.',
					WCG_YOPAGO_TEXT_DOMAIN ),
				'desc_tip'    => TRUE,
			],
			'description'  => [
				'title'       => __( 'Description', WCG_YOPAGO_TEXT_DOMAIN ),
				'type'        => 'textarea',
				'description' => __( 'Enter a description for the payment method.', WCG_YOPAGO_TEXT_DOMAIN ),
				'default'     => __( 'Multiple payment methods including credit/debit cards, QR, and Bolivian banks.',
					WCG_YOPAGO_TEXT_DOMAIN ),
				'desc_tip'    => TRUE,
			],
			'code'         => [
				'title'       => __( 'Code', WCG_YOPAGO_TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Enter the code for the payment method.', WCG_YOPAGO_TEXT_DOMAIN ),
				'default'     => 'EZ2J-AA34-BA26-T87B',
				'placeholder' => 'ej. ZPOG-P1V2-23gK-H34G',
				'desc_tip'    => TRUE,
			],
			'name_company' => [
				'title'       => __( 'Company Name', WCG_YOPAGO_TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Enter the name of your company.', WCG_YOPAGO_TEXT_DOMAIN ),
				'default'     => '',
				'placeholder' => 'ej. My Business Name',
				'desc_tip'    => TRUE,
			],
			'api_url'      => [
				'title'       => __( WCG_YOPAGO_NAME . ' URL', WCG_YOPAGO_TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Enter the ' . WCG_YOPAGO_NAME . ' URL for payment processing.',
					WCG_YOPAGO_TEXT_DOMAIN ),
				// TODO: Delete this default URL to Production
				'default'     => 'https://yopago.com.bo/pay/api/generateUrl',
				'placeholder' => 'ej. https://yopago.com.bo/pay/api/generateUrl',
				'desc_tip'    => TRUE,
			],
			'success_url'  => [
				'title'       => __( 'Thank You URL', WCG_YOPAGO_TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Enter the URL to redirect after successful payment.', WCG_YOPAGO_TEXT_DOMAIN ),
				'default'     => $order_received_url,
				'placeholder' => 'ej. ' . $order_received_url,
				'desc_tip'    => TRUE,
			],
			'error_url'    => [
				'title'       => __( 'Error URL', WCG_YOPAGO_TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Enter the URL to redirect in case of payment error.', WCG_YOPAGO_TEXT_DOMAIN ),
				'default'     => $order_received_url,
				'placeholder' => 'ej. ' . $order_received_url,
				'desc_tip'    => TRUE,
			],
			'checkout_msg' => [
				'title'       => __( 'Checkout Message Title', WCG_YOPAGO_TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Enter a title for the checkout message.', WCG_YOPAGO_TEXT_DOMAIN ),
				'default'     => __( 'Pay with ' . WCG_YOPAGO_NAME, WCG_YOPAGO_TEXT_DOMAIN ),
				'desc_tip'    => TRUE,
			],
		];
	}

	public function process_payment( $order_id ): array {
		$order = wc_get_order( $order_id );

		// Mark the order as processing or completed
		$order->update_status( 'pending', __( 'Awaiting ' . WCG_YOPAGO_NAME . ' payment', WCG_YOPAGO_TEXT_DOMAIN ) );

		// Return the redirect URL
		return [
			'result'   => 'success',
			// 'redirect' => $this->get_yopago_redirect_url( $order ),
			'redirect' => $order->get_checkout_payment_url( TRUE )
		];
	}

	public function render_iframe( $order_id ): void {
		$order       = wc_get_order( $order_id );
		$payment_url = $this->build_payment_url( $order );

		if ( $payment_url ) {
			echo '<iframe src="'
			     . esc_url( $payment_url )
			     . '" style="border:none; height:700px; width:100%;" scrolling="yes"></iframe>';
		} else {
			echo '<p>' . __( 'There was a problem connecting to YoPago.', WCG_YOPAGO_TEXT_DOMAIN ) . '</p>';
		}
	}

	private function build_payment_url( WC_Order $order ): ?string {
		$body = [
			'companyCode'     => sanitize_text_field( $this->code ),
			'codeTransaction' => $order->get_id() . '-' . random_int( 100, 999 ),
			'urlSuccess'      => WCG_YOPAGO_PLUGIN_URL . 'callback.php',
			'urlFailed'       => WCG_YOPAGO_PLUGIN_URL . 'callback.php',
			'billName'        => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
			'billNit'         => '000000',
			'email'           => $order->get_billing_email(),
			'generateBill'    => '1',
			'concept'         => 'Pago por servicios ' . $this->name_company,
			'currency'        => 'BOB',
			'amount'          => $order->get_total(),
			'messagePayment'  => __( 'Thank you for use our service', WCG_YOPAGO_TEXT_DOMAIN ),
			'codeExternal'    => md5( $order->get_order_key() ),
		];

		$response = wp_remote_post(
			$this->api_url,
			[
				'method'  => 'POST',
				'headers' => [
					'Content-Type' => 'application/json',
				],
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
