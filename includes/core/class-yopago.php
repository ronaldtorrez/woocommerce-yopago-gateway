<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class YoPago {

	public static function run(): void {
		self::load_dependencies();
		self::init_hooks();
	}

	private static function load_dependencies(): void {
		require_once WC_YOPAGO_PLUGIN_PATH . 'includes/core/class-yopago-loader.php';
		require_once WC_YOPAGO_PLUGIN_PATH . 'includes/core/class-yopago-activator.php';
		require_once WC_YOPAGO_PLUGIN_PATH . 'includes/core/class-yopago-deactivator.php';

		require_once WC_YOPAGO_PLUGIN_PATH . 'includes/util/class-yopago-currency-converter.php';
		require_once WC_YOPAGO_PLUGIN_PATH . 'includes/admin/class-yopago-currency-table.php';

		require_once WC_YOPAGO_PLUGIN_PATH . 'includes/gateway/class-yopago-settings.php';
		require_once WC_YOPAGO_PLUGIN_PATH . 'includes/gateway/class-yopago-api.php';
		require_once WC_YOPAGO_PLUGIN_PATH . 'includes/gateway/class-yopago-render.php';

		require_once WC_YOPAGO_PLUGIN_PATH . 'includes/hooks/class-yopago-checkout-hooks.php';
		require_once WC_YOPAGO_PLUGIN_PATH . 'includes/functions.php';
	}

	private static function init_hooks(): void {
		YoPago_Loader::init();
		YoPago_Checkout_Hooks::init();
		YoPago_Assets::init();
	}
}
