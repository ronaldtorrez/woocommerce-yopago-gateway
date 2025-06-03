<?php
/*
Plugin Name: WooCommerce YoPago Gateway
Plugin URI: https://github.com/larafriend/woocommerce-yopago-gateway
Description: Payment methods by card, QR code, and bank transfer for WooCommerce in Bolivia.
Version: 1.0
Author: Ronald Torrez
Author URI: https://github.com/larafriend/
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: yopago
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WC_YOPAGO_NAME', 'YoPago' );
define( 'WC_YOPAGO_ID', 'yopago' );
define( 'WC_YOPAGO_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'WC_YOPAGO_PLUGIN_FILE', __FILE__ );
define( 'WC_YOPAGO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WC_YOPAGO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'WC_YOPAGO_SETTING', 'woocommerce_' . WC_YOPAGO_ID . '_settings' );
define( 'WC_YOPAGO_CURRENCY_DATA_URL', WC_YOPAGO_PLUGIN_URL . 'assets/data/currencies.json' );

require_once WC_YOPAGO_PLUGIN_PATH . 'includes/core/class-yopago.php';
require_once WC_YOPAGO_PLUGIN_PATH . 'includes/admin/class-yopago-assets.php';
require_once WC_YOPAGO_PLUGIN_PATH . 'includes/admin/class-yopago-currency-table.php';

register_activation_hook( __FILE__, [ 'YoPago_Activator', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'YoPago_Deactivator', 'deactivate' ] );

YoPago::run();
