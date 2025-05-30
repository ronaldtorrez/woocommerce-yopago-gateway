<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YoPago_Currency_Converter {

	public static function convert_to_bob( float $amount, string $currency ): ?array {
		$settings = get_option( 'woocommerce_' . WC_YOPAGO_ID . '_settings', [] );

		if ( empty( $settings['currency_conversion_enabled'] ) || $settings['currency_conversion_enabled'] !== 'yes' ) {
			return NULL;
		}

		if ( strtoupper( $currency ) === 'BOB' ) {
			return [
				'converted'  => $amount,
				'rate'       => 1.0,
				'fee'        => 0.0,
				'fee_type'   => 'fixed',
				'fee_amount' => 0.0,
				'total'      => $amount,
			];
		}

		$conversions = get_option( 'yopago_currency_rates', [] );

		foreach ( $conversions as $row ) {
			if ( strtoupper( $row['currency'] ) === strtoupper( $currency ) ) {
				$rate       = floatval( $row['rate'] );
				$fee        = floatval( $row['fee'] );
				$fee_type   = $row['fee_type'];
				$converted  = $amount * $rate;
				$fee_amount = ( $fee_type === 'percent' )
					? ( $converted * $fee / 100 )
					: $fee;

				$total = round( $converted + $fee_amount, 2 );

				return [
					'converted'  => round( $converted, 2 ),
					'rate'       => $rate,
					'fee'        => $fee,
					'fee_type'   => $fee_type,
					'fee_amount' => round( $fee_amount, 2 ),
					'total'      => $total,
				];
			}
		}

		return NULL;
	}
}
