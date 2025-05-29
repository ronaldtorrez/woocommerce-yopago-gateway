<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YoPago_Deactivator {

	public static function deactivate(): void {
		delete_option( WC_YOPAGO_SETTING );
	}
}
