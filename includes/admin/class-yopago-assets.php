<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YoPago_Assets {

	public static function init(): void {
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin_assets' ] );
	}

	public static function enqueue_admin_assets( $hook ): void {
		if ( 'woocommerce_page_wc-settings' !== $hook ) {
			return;
		}

		// Verifica si estamos en la página de configuración de nuestro gateway
		if ( ! isset( $_GET['section'] ) || $_GET['section'] !== 'yopago' ) {
			return;
		}

		wp_enqueue_style(
			'yopago-admin',
			WC_YOPAGO_PLUGIN_URL . 'assets/css/admin-currency-table.css',
			[],
			filemtime( WC_YOPAGO_PLUGIN_PATH . 'assets/css/admin-currency-table.css' )
		);

		wp_enqueue_script( 'select2' );

		wp_enqueue_script(
			'yopago-currency-table',
			WC_YOPAGO_PLUGIN_URL . 'assets/js/admin/currency-table.min.js',
			[ 'jquery', 'select2' ],
			filemtime( WC_YOPAGO_PLUGIN_PATH . 'assets/js/admin/currency-table.min.js' ),
			TRUE
		);

		wp_localize_script(
			'yopago-currency-table',
			'wc_yopago_params',
			[
				'currency_data_url' => WC_YOPAGO_CURRENCY_DATA_URL,
				'select_currency'   => __( 'Select a currency', WC_YOPAGO_ID ),
				'fixed'             => __( 'Fixed', WC_YOPAGO_ID ),
				'percent'           => __( 'Percent', WC_YOPAGO_ID ),
				'view'              => __( 'View', WC_YOPAGO_ID ),
				'remove'            => __( 'Remove', WC_YOPAGO_ID ),
				'no_currencies'     => __( 'No currency rates added yet.', WC_YOPAGO_ID ),

				'ex_title'         => __( 'Conversion example: {from} → {to}', WC_YOPAGO_ID ),
				'ex_assumptions'   => __( 'Assumptions', WC_YOPAGO_ID ),
				'ex_site_currency' => __( 'Site currency', WC_YOPAGO_ID ),
				'ex_rate'          => __( 'Exchange rate', WC_YOPAGO_ID ),
				'ex_fee'           => __( 'Commission', WC_YOPAGO_ID ),
				'ex_original'      => __( 'Original order amount', WC_YOPAGO_ID ),
				'ex_calc'          => __( 'Calculation', WC_YOPAGO_ID ),
				'ex_concept'       => __( 'Concept', WC_YOPAGO_ID ),
				'ex_value'         => __( 'Value', WC_YOPAGO_ID ),
				'ex_c_original'    => __( 'Original amount ({from})', WC_YOPAGO_ID ),
				'ex_c_rate'        => __( 'Exchange rate', WC_YOPAGO_ID ),
				'ex_c_subtotal'    => __( 'Subtotal converted ({to})', WC_YOPAGO_ID ),
				'ex_c_commission'  => __( 'Commission', WC_YOPAGO_ID ),
				'ex_c_total'       => __( 'Total to charge ({to})', WC_YOPAGO_ID ),
				'ex_result'        => __( 'Final result', WC_YOPAGO_ID ),
				'ex_result_text'   => __( 'The customer will pay {symbol}{total} {to} thanks to automatic conversion.',
					WC_YOPAGO_ID ),
			]
		);
	}
}
