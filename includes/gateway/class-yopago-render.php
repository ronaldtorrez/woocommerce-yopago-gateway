<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YoPago_Render {

	public static function render_iframe( $order_id, $gateway ): void {
		$order = wc_get_order( $order_id );
		$url   = YoPago_API::build_payment_url( $order, $gateway );

		if ( $url ) {
			echo '<iframe src="'
			     . esc_url( $url )
			     . '" style="border:none; height:700px; width:100%;" scrolling="yes"></iframe>';
		} else {
			echo '<p>' . __( 'There was a problem connecting to YoPago.', WC_YOPAGO_TEXT_DOMAIN ) . '</p>';
		}
	}
}
