<?php

require_once dirname( __FILE__, 4 ) . '/wp-load.php';

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

$gateway = new WC_Gateway_YoPago();

if ( isset( $_REQUEST['error'] ) && isset( $_REQUEST['message'] ) ) {
	$err_url = trailingslashit( $gateway->get_option( 'url_error' ) );
	$err_url .= $order_id . '/?error=' . sanitize_text_field( $_REQUEST['error'] );
	$err_url .= '&message=' . urlencode( sanitize_text_field( $_REQUEST['message'] ) );

	// wp_safe_redirect( $err_url );

	?>
	<!-- TODO: Try to use wp_safe_redirect or similar -->
	<script type="text/javascript">
        window.top.location.href = "<?php echo esc_url( $err_url ); ?>"
	</script>
	<?php

	exit;
}

if ( ! $wc_order->is_paid() ) {
	$wc_order->payment_complete( $tx_id );
	$wc_order->add_order_note( sprintf(
		__( 'YoPago confirmed payment. Transaction #%s via %s.', WCG_YOPAGO_TEXT_DOMAIN ),
		$tx_id,
		$paymethod
	) );
}

// Generate thank you page URL
$success_url = trailingslashit( $gateway->get_option( 'url_thank_you' ) );
$success_url .= $order_id;
$success_url .= '/?key=' . $wc_order->get_order_key();

?>
	<!-- TODO: Try to use wp_safe_redirect or similar -->
	<script type="text/javascript">
        window.top.location.href = "<?php echo esc_url( $success_url ); ?>"
	</script>
<?php

exit;
