<?php

require_once dirname( __FILE__, 4 ) . '/wp-load.php';

$required_params = [ 'hash', 'transactionId', 'codeTransaction', 'payform' ];

foreach ( $required_params as $param ) {
	if ( empty( $_REQUEST[ $param ] ) ) {
		wp_die( 'Missing required parameters.' );
	}
}

// Sanitize input values
$hash             = sanitize_text_field( $_REQUEST['hash'] );
$transaction_id   = sanitize_text_field( $_REQUEST['transactionId'] );
$code_transaction = sanitize_text_field( $_REQUEST['codeTransaction'] );
$method           = sanitize_text_field( $_REQUEST['payform'] );

// Extract order ID from transaction code
$order_parts = explode( '-', $code_transaction );
$order_id    = intval( $order_parts[0] );

if ( ! $order_id ) {
	wp_die( 'Invalid order ID.' );
}

// Load the order
$order = wc_get_order( $order_id );

if ( ! $order ) {
	wp_die( 'Order not found.' );
}

// Verify the hash
$order_key  = $order->get_order_key();
$hash_check = md5( $order_key );

if ( $hash !== $hash_check ) {
	wp_die( 'Hash verification failed.' );
}

// Mark order as paid if it's not already completed
if ( $order->get_status() !== 'completed' ) {
	$order->payment_complete( $transaction_id );
	$order->add_order_note(
		sprintf(
			'YoPago confirmed payment. Transaction #%1$s by %2$s.',
			$transaction_id,
			$method
		)
	);

	WC()->cart->empty_cart();
}

// Generate thank you page URL
$gateway     = new WC_Gateway_YoPago();
$success_url = trailingslashit( $gateway->get_option( 'url_thank_you' ) );
$success_url .= $order_id;
$success_url .= '/?key=' . $order_key;

?>
	<script type="text/javascript">
        window.top.location.href = "<?php echo esc_url( $success_url ); ?>"
	</script>
<?php

// Handle error case if provided
if ( isset( $_REQUEST['error'] ) && isset( $_REQUEST['message'] ) ) {
	$gateway  = new WC_Gateway_YoPago();
	$fail_url = trailingslashit( $gateway->get_option( 'url_error' ) );
	$fail_url .= $order_id;
	$fail_url .= '/?error=' . sanitize_text_field( $_REQUEST['error'] );
	$fail_url .= '&message=' . urlencode( sanitize_text_field( $_REQUEST['message'] ) );

	?>

	<script type="text/javascript">
        window.top.location.href = "<?php echo esc_url( $fail_url ); ?>"
	</script>
	<?php

	exit;
}
