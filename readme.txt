=== WooCommerce YoPago Gateway ===
Contributors: ronaldtorrez
Donate link: https://yopago.com.bo
Tags: woocommerce, payment gateway, bolivia, BOB, card payments, qr, bank transfer, currency conversion
Requires at least: 5.6
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

WooCommerce YoPago Gateway lets you charge customers in Bolivia through YoPago — credit/debit cards, QR codes and local bank transfers.  It ships with an advanced currency-conversion module so you can quote prices in any WooCommerce currency and seamlessly bill the customer in Bolivianos (BOB) using your own exchange rates and fees.

== Description (EN) ==
* Accept YoPago cards, QR & bank payments.
* Automatically convert store currency to BOB (Bolivianos) with fixed or percentage fees.
* Live “conversion example” modal inside the gateway settings.
* Secure callback that validates the hash and marks the order as paid.
* Custom checkout message.
* Translation-ready.

== Descripción (ES) ==
WooCommerce YoPago Gateway permite aceptar pagos bolivianos mediante YoPago — tarjetas de crédito/débito, código QR y transferencias bancarias locales— e incluye un potente módulo de conversión de divisas para transformar el total del pedido a Bolivianos (BOB) con sus propias tarifas y comisiones configurables.

* Acepta múltiples métodos de pago YoPago: tarjetas, QR, bancos bolivianos.
* Conversión automática de la moneda de la tienda a BOB con tipos de cambio y comisión fijos o porcentuales.
* Modal de ejemplo en vivo para que el administrador verifique cada regla de conversión.
* Callback seguro que confirma el pago y actualiza el estado del pedido en WooCommerce.
* Mensaje personalizable que se muestra en la pantalla de pago.
* Totalmente traducible y preparado para i18n.

== Features ==
1. **Multiple payment methods** – Card, QR and bank transfer in a single gateway.
2. **Custom exchange rates & fees** – Define an unlimited list of currencies with individual rate, fee amount and fee type (fixed or percent).
3. **Interactive admin UI** – Elegant Select2 dropdowns with flags, dynamic add/remove rows, and real-time validation.
4. **Automatic order notes** – Logs the exact conversion formula and YoPago transaction ID.
5. **Developer-friendly** – Modern ES6 modules, namespaced PHP classes, WordPress coding standards, and full WP-CLI compatibility.

== Installation ==
1. Upload the plugin ZIP via *Plugins ▸ Add New ▸ Upload Plugin* or clone the repository inside `/wp-content/plugins/`.
2. Activate **WooCommerce YoPago Gateway**.
3. Go to *WooCommerce ▸ Settings ▸ Payments ▸ YoPago* and click **Manage**.
4. Fill in:
   * **Code** – Your YoPago company code.
   * **Company Name** – Shown on the checkout form and YoPago receipt.
   * **API URL** – Usually `https://yopago.com.bo/pay/api/generateUrl` unless YoPago provides a sandbox.
   * **Success/Error URL** – Where customers are redirected after payment.
5. (Optional) Tick **Enable currency conversion** and configure your desired exchange rates in the table that appears.
6. Save changes – you’re ready to accept YoPago!

== Frequently Asked Questions ==
= Does the plugin work outside Bolivia? =
Yes, but YoPago settles funds in Bolivianos to Bolivian bank accounts. Your store currency can be anything; the built-in converter will translate it to BOB at checkout.

= Where do I get the “company code”? =
Sign in to your YoPago merchant portal and look under *Configuración ▸ Integraciones*. Contact soporte@yopago.com.bo if you can’t find it.

= How are fees applied? =
Choose **Fixed** to add a constant amount in BOB on every conversion, or **Percent** to charge a percentage of the converted subtotal.

= Can I leave the exchange-rate table empty? =
Absolutely. If no rule matches the store currency, the plugin charges the WooCommerce total as-is (no conversion).

== Changelog ==
= 1.0 – 2025-06-02 =
* Initial public release.
* Payment iframe generation with secure hash validation.
* Custom currency-conversion engine and interactive admin UI.
* Spanish (es_ES) and English (en_US) translations included.

== Upgrade Notice ==
= 1.0 =
First stable release of WooCommerce YoPago Gateway.  No special upgrade steps are required.

== Localization ==
The plugin is fully internationalized.  Use [Poedit](https://poedit.net/) or any gettext editor to translate the `.pot` file inside `/languages/`.
