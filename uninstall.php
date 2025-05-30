<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( WC_YOPAGO_SETTING );
delete_option( 'yopago_currency_rates' );
