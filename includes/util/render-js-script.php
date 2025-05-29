<?php

/**
 * Outputs a JavaScript redirect to the specified URL.
 *
 * @param  string  $url  The URL to redirect to.
 * @param  string  $message  Optional message to display if JavaScript is disabled.
 * @param  string  $link_text  Optional text for the link if JavaScript is disabled.
 */
function yopago_js_redirect_script(
	string $url,
	string $message = '',
	string $link_text = ''
): void {
	$url       = esc_url( $url );
	$message   = $message ?: esc_html__( 'Redirection failed. Please click the link below:', WC_YOPAGO_TEXT_DOMAIN );
	$link_text = $link_text ?: esc_html__( 'Go to page', WC_YOPAGO_TEXT_DOMAIN );
	$title     = esc_html__( 'Redirecting...', WC_YOPAGO_TEXT_DOMAIN );

	echo '<!DOCTYPE html>';
	echo '<html lang="' . esc_attr( get_bloginfo( 'language' ) ) . '">';
	echo '<head>';
	echo '<meta charset="utf-8">';
	echo '<title>' . $title . '</title>';
	echo '</head>';
	echo '<body>';
	echo '<script type="text/javascript">';
	echo 'window.top.location.href = "' . $url . '";';
	echo '</script>';
	echo '<noscript>';
	echo '<p>' . $message . '</p>';
	echo '<a href="' . $url . '" data-tooltip="' . $url . '">' . $link_text . '</a>';
	echo '</noscript>';
	echo '</body>';
	echo '</html>';
}
