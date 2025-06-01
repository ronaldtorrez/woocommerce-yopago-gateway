import $ from 'jquery'

export function updateAddButtonState() {
    const total = window.yopagoCurrencies ? window.yopagoCurrencies.length : 0
    const rows = $( '#yopago-currency-rates-table tbody tr' ).not( '.empty-row' ).length
    $( '#yopago-add-rate' ).prop( 'disabled', rows >= total )
}
