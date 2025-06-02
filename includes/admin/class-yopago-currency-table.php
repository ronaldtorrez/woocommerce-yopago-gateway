<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YoPago_Currency_Table {

	private static $instance;

	public static function init(): YoPago_Currency_Table {
		if ( NULL === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function render_table(): void {
		$rates = get_option( 'yopago_currency_rates', [] );
		?>
		<div class="yopago-currency-table-wrapper">
			<table class="widefat" id="yopago-currency-rates-table">
				<thead>
				<tr>
					<th><?php
						esc_html_e( 'Currency', WC_YOPAGO_ID ); ?></th>
					<th><?php
						esc_html_e( 'Rate', WC_YOPAGO_ID ); ?></th>
					<th><?php
						esc_html_e( 'Fee (BOB)', WC_YOPAGO_ID ); ?></th>
					<th><?php
						esc_html_e( 'Fee Type', WC_YOPAGO_ID ); ?></th>
					<th><?php
						esc_html_e( 'Example', WC_YOPAGO_ID ); ?></th>
					<th><?php
						esc_html_e( 'Actions', WC_YOPAGO_ID ); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php
				if ( empty( $rates ) ) :?>
					<tr class="empty-row">
						<td colspan="6">
							<?php
							esc_html_e( 'No currency rates added yet.', WC_YOPAGO_ID ); ?>
						</td>
					</tr>
				<?php
				else : ?>
					<?php
					foreach ( $rates as $index => $rate ) : ?>
						<tr
							data-index="<?php
							echo esc_attr( $index ); ?>"
						>
							<td>
								<select
									class="yopago-currency-select"
									name="currency_rates[<?php
									echo $index; ?>][currency]"
									data-selected="<?php
									echo esc_attr( $rate['currency'] ); ?>"
								>
									<!-- Options se llenarán con JS -->
								</select>
							</td>
							<td><input
									type="number" step="0.0001" name="currency_rates[<?php
								echo $index; ?>][rate]" value="<?php
								echo esc_attr( $rate['rate'] ); ?>" min="0.0001" required
								></td>
							<td><input
									type="number" step="0.01" name="currency_rates[<?php
								echo $index; ?>][fee]" value="<?php
								echo esc_attr( $rate['fee'] ); ?>" min="0" required
								></td>
							<td>
								<select
									name="currency_rates[<?php
									echo $index; ?>][fee_type]"
								>
									<option
										value="fixed" <?php
									selected( $rate['fee_type'], 'fixed' ); ?>><?php
										esc_html_e( 'Fixed', WC_YOPAGO_ID ); ?></option>
									<option
										value="percent" <?php
									selected( $rate['fee_type'], 'percent' ); ?>><?php
										esc_html_e( 'Percent', WC_YOPAGO_ID ); ?></option>
								</select>
							</td>
							<td>
								<button
									type="button" class="button yopago-example-btn" data-index="<?php
								echo $index; ?>"
								>
									<?php
									esc_html_e( 'View', WC_YOPAGO_ID ); ?>
								</button>
							</td>
							<td>
								<button type="button" class="button button-link-delete yopago-remove-rate">
									<?php
									esc_html_e( 'Remove', WC_YOPAGO_ID ); ?>
								</button>
							</td>
						</tr>
					<?php
					endforeach; ?>
				<?php
				endif; ?>
				</tbody>
				<tfoot>
				<tr>
					<td colspan="6">
						<button type="button" id="yopago-add-rate" class="button">
							<?php
							esc_html_e( 'Add Currency', WC_YOPAGO_ID ); ?>
						</button>
					</td>
				</tr>
				</tfoot>
			</table>
		</div>

		<!-- Modal para ejemplos -->
		<div id="yopago-example-modal" style="display:none;">
			<div class="yopago-modal-content">
				<h3><?php
					esc_html_e( 'Conversion Example', WC_YOPAGO_ID ); ?></h3>
				<p id="yopago-example-text"></p>
				<button type="button" class="button" id="yopago-modal-close">
					<?php
					esc_html_e( 'Close', WC_YOPAGO_ID ); ?>
				</button>
			</div>
		</div>
		<?php
	}

	public function save_rates( $settings ) {
		if ( isset( $_POST['currency_rates'] ) ) {
			$rates           = array_values( $_POST['currency_rates'] );
			$sanitized_rates = [];

			foreach ( $rates as $rate ) {
				$sanitized_rates[] = [
					'currency' => sanitize_text_field( $rate['currency'] ),
					'rate'     => floatval( $rate['rate'] ),
					'fee'      => floatval( $rate['fee'] ),
					'fee_type' => in_array( $rate['fee_type'], [ 'fixed', 'percent' ] ) ? $rate['fee_type'] : 'fixed',
				];
			}

			update_option( 'yopago_currency_rates', $sanitized_rates );
		}

		return $settings;
	}
}
