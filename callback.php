<?php

require_once dirname( __FILE__, 4 ) . '/wp-load.php';
require_once dirname( __FILE__ ) . '/includes/util/render-js-script.php';

$required_params = [ 'hash', 'transactionId', 'codeTransaction', 'payform' ];

foreach ( $required_params as $param ) {
	if ( empty( $_REQUEST[ $param ] ) ) {
		wp_die( 'Missing required parameters.' );
	}
}

// Sanitize input values
$tx_hash   = sanitize_text_field( $_REQUEST['hash'] );
$tx_id     = sanitize_text_field( $_REQUEST['transactionId'] );
$tx_code   = sanitize_text_field( $_REQUEST['codeTransaction'] );
$paymethod = sanitize_text_field( $_REQUEST['payform'] );

[ $order_id ] = explode( '-', $tx_code );
$order_id = intval( $order_id );
$wc_order = wc_get_order( $order_id );

if ( ! $order_id ) {
	wp_die( 'Invalid or missing order.' );
}

if ( ! $wc_order ) {
	wp_die( 'Order not found.' );
}

if ( $tx_hash !== md5( $wc_order->get_order_key() ) ) {
	wp_die( 'Hash verification failed.' );
}

$gateways = WC()->payment_gateways()->payment_gateways();
$gateway  = $gateways[ WC_YOPAGO_ID ] ?? NULL;

if ( ! $gateway ) {
	wp_die( sprintf(
		__( 'Payment gateway %s not found.', WC_YOPAGO_ID ),
		WC_YOPAGO_NAME
	) );
}

if ( isset( $_REQUEST['error'] ) && isset( $_REQUEST['message'] ) ) {
	$err_url = trailingslashit( $gateway->get_option( 'error_url' ) );
	$err_url .= $order_id
	            . '/?error='
	            . sanitize_text_field( $_REQUEST['error'] )
	            . '&message='
	            . urlencode( sanitize_text_field( $_REQUEST['message'] ) );

	yopago_js_redirect_script( $err_url,
		__( 'Redirection to error page failed. Please click the link below:', WC_YOPAGO_ID ),
		__( 'Go to error page', WC_YOPAGO_ID )
	);

	exit;
}

if ( ! $wc_order->is_paid() ) {
	$wc_order->payment_complete( $tx_id );

	$original_amount   = $wc_order->get_total();
	$original_currency = get_woocommerce_currency();
	$conversion        = YoPago_Currency_Converter::convert_to_bob( $original_amount, $original_currency );

	if ( $conversion ) {
		$wc_order->add_order_note( sprintf(
			'Conversion applied: %s %.2f × %.4f = Bs. %.2f. Commission %s: %.2f. Total charged: Bs. %.2f.',
			$original_currency,
			$original_amount,
			$conversion['rate'],
			$conversion['converted'],
			$conversion['fee_type'],
			$conversion['fee_amount'],
			$conversion['total']
		) );
	}

	$wc_order->add_order_note( sprintf(
		__( 'YoPago confirmed payment. Transaction #%s via %s.',
			WC_YOPAGO_ID ),
		$tx_id,
		$paymethod
	) );
}

// Generate thank you page URL
$success_url = trailingslashit( $gateway->get_option( 'success_url' ) );
$success_url .= $order_id;
$success_url .= '/?key=' . $wc_order->get_order_key();

yopago_js_redirect_script( $success_url,
	__( 'Redirection to success page failed. Please click the link below:', WC_YOPAGO_ID ),
	__( 'Go to success page', WC_YOPAGO_ID )
);

exit;
