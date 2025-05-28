<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Gateway_YoPago extends WC_Payment_Gateway {

	public $title;
	public $description;
	public string $code;
	public string $name_company;
	public string $url_yopago;
	public string $url_thank_you;
	public string $url_error;
	public string $mensaje_checkout_title;

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
		$this->title                  = $this->get_option( 'title' );
		$this->description            = $this->get_option( 'description' );
		$this->code                   = $this->get_option( 'code' );
		$this->name_company           = $this->get_option( 'name_company' );
		$this->url_yopago             = $this->get_option( 'url_yopago' );
		$this->url_thank_you          = $this->get_option( 'url_thank_you' );
		$this->url_error              = $this->get_option( 'url_error' );
		$this->mensaje_checkout_title = $this->get_option( 'mensaje_checkout_title' );

		add_action( 'woocommerce_receipt_' . $this->id, [ $this, 'receipt_page' ] );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
	}

	public function init_form_fields(): void {
		$this->form_fields = [
			'enabled'                => [
				'title'   => __( 'Enable/Disable', WCG_YOPAGO_TEXT_DOMAIN ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable ' . WCG_YOPAGO_NAME . ' Gateway', WCG_YOPAGO_TEXT_DOMAIN ),
				'default' => 'no',
			],
			'title'                  => [
				'title'       => __( 'Title', WCG_YOPAGO_TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Enter a title for the payment method.', WCG_YOPAGO_TEXT_DOMAIN ),
				'default'     => _x( 'Pay with ' . WCG_YOPAGO_NAME . '.',
					'Pay with ' . WCG_YOPAGO_NAME . '.',
					WCG_YOPAGO_TEXT_DOMAIN ),
				'desc_tip'    => TRUE,
			],
			'description'            => [
				'title'       => __( 'Description', WCG_YOPAGO_TEXT_DOMAIN ),
				'type'        => 'textarea',
				'description' => __( 'Enter a description for the payment method.', WCG_YOPAGO_TEXT_DOMAIN ),
				'default'     => __( 'Multiple payment methods including credit/debit cards, QR, and Bolivian banks.',
					WCG_YOPAGO_TEXT_DOMAIN ),
				'desc_tip'    => TRUE,
			],
			'code'                   => [
				'title'       => __( 'Code', WCG_YOPAGO_TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Enter the code for the payment method.', WCG_YOPAGO_TEXT_DOMAIN ),
				'default'     => 'EZ2J-AA34-BA26-T87B',
				'placeholder' => 'ej. ZPOG-P1V2-23gK-H34G',
				'desc_tip'    => TRUE,
			],
			'name_company'           => [
				'title'       => __( 'Company Name', WCG_YOPAGO_TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Enter the name of your company.', WCG_YOPAGO_TEXT_DOMAIN ),
				'default'     => '',
				'placeholder' => 'ej. My Business Name',
				'desc_tip'    => TRUE,
			],
			'url_yopago'             => [
				'title'       => __( WCG_YOPAGO_NAME . ' URL', WCG_YOPAGO_TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Enter the ' . WCG_YOPAGO_NAME . ' URL for payment processing.',
					WCG_YOPAGO_TEXT_DOMAIN ),
				// TODO: Delete this default URL to Production
				'default'     => 'https://yopago.com.bo/pay/api/generateUrl',
				'placeholder' => 'ej. https://yopago.com.bo/pay/api/generateUrl',
				'desc_tip'    => TRUE,
			],
			'url_thank_you'          => [
				'title'       => __( 'Thank You URL', WCG_YOPAGO_TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Enter the URL to redirect after successful payment.', WCG_YOPAGO_TEXT_DOMAIN ),
				'default'     => get_site_url() . '/sample-page',
				'placeholder' => 'ej. https://yourwebsite.com/thank-you',
				'desc_tip'    => TRUE,
			],
			'url_error'              => [
				'title'       => __( 'Error URL', WCG_YOPAGO_TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Enter the URL to redirect in case of payment error.', WCG_YOPAGO_TEXT_DOMAIN ),
				'default'     => get_site_url() . '/error-page',
				'placeholder' => 'ej. https://yourwebsite.com/error',
				'desc_tip'    => TRUE,
			],
			'mensaje_checkout_title' => [
				'title'       => __( 'Checkout Message Title', WCG_YOPAGO_TEXT_DOMAIN ),
				'type'        => 'text',
				'description' => __( 'Enter a title for the checkout message.', WCG_YOPAGO_TEXT_DOMAIN ),
				'default'     => __( 'Pay with ' . WCG_YOPAGO_NAME, WCG_YOPAGO_TEXT_DOMAIN ),
				'desc_tip'    => TRUE,
			],
		];
	}

	public function payment_fields(): void {
		// Optional: Add custom HTML fields here
		if ( $this->description ) {
			echo wpautop( wp_kses_post( $this->description ) );
		}
	}

	public function process_payment( $order_id ): array {
		$order = wc_get_order( $order_id );

		// Mark the order as processing or completed
		$order->update_status( 'on-hold', __( 'Awaiting ' . WCG_YOPAGO_NAME . ' payment', WCG_YOPAGO_TEXT_DOMAIN ) );

		// Return the redirect URL
		return [
			'result'   => 'success',
			'redirect' => $this->get_yopago_redirect_url( $order ),
		];
	}

	public function get_yopago_redirect_url( $order ): string {
		$redirect_url = $this->url_yopago . '?';
		// TODO: The code parameter should can error - change to "codigo"
		$redirect_url .= 'code=' . urlencode( $this->code );
		$redirect_url .= '&name_company=' . urlencode( $this->name_company );
		$redirect_url .= '&amount=' . urlencode( $order->get_total() );
		$redirect_url .= '&currency=' . urlencode( get_woocommerce_currency() );
		$redirect_url .= '&return_url=' . urlencode( $this->url_thank_you );
		$redirect_url .= '&cancel_url=' . urlencode( $this->url_error );

		return $redirect_url;
	}

	public function receipt_page( $order ): void {
		echo '<p>' . __( 'Thank you for your order, please click the button below to pay with ' . WCG_YOPAGO_NAME . '.',
				WCG_YOPAGO_TEXT_DOMAIN ) . '</p>';
		echo '<a class="button" href="'
		     . esc_url( $this->get_yopago_redirect_url( $order ) )
		     . '">'
		     . __( 'Pay with ' . WCG_YOPAGO_NAME, WCG_YOPAGO_TEXT_DOMAIN )
		     . '</a>';
	}

}
