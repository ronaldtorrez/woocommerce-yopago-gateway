import { formatCurrency } from './format-currency.js'

export function attachSelect2( $sel ) {
    $sel.select2(
        {
            width: '100%',
            placeholder: wc_yopago_params.select_currency,
            allowClear: false,
            templateResult: option => formatCurrency( option, true ),
            templateSelection: option => formatCurrency( option, false )
        }
    )
}
