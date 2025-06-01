import $ from 'jquery'
import { generateExampleHTML } from './example-generator'
import { buildCurrencyRateRow } from './row-builder'
import { markSettingsAsDirty } from './utils/global'
import { initializeCurrencyDropdown } from './utils/initialize-currency-dropdown'
import { updateAddButtonState } from './utils/update-add-button-state'
import { updateCurrencyOptions } from './utils/update-currency-options'

export function bindUIEvents( { $table, $addBtn, $modal, $modalContent } ) {

    $addBtn.on( 'click', function () {
        const index = Date.now()
        const newRow = buildCurrencyRateRow( index )
        $table.find( 'tbody .empty-row' ).remove()
        $table.find( 'tbody' ).append( newRow )

        const $newSelect = $table.find(
            `tr[data-index="${ index }"] .yopago-currency-select`
        )
        initializeCurrencyDropdown( $newSelect )
        updateCurrencyOptions()
        updateAddButtonState()
        markSettingsAsDirty()
    } )

    $table.on( 'click', '.yopago-remove-rate', function () {
        $( this ).closest( 'tr' ).remove()
        if ( $table.find( 'tbody tr' ).length === 0 ) {
            $table.find( 'tbody' ).append(
                `<tr class='empty-row'><td colspan='6'>${ wc_yopago_params.no_currencies }</td></tr>`
            )
        }
        updateCurrencyOptions()
        updateAddButtonState()
        markSettingsAsDirty()
    } )

    $table.on( 'click', '.yopago-example-btn', function () {
        const index = $( this ).data( 'index' )
        const $row = $table.find( `tr[data-index="${ index }"]` )
        const rate = parseFloat( $row.find( 'input[name*="[rate]"]' ).val() ) || 1
        const fee = parseFloat( $row.find( 'input[name*="[fee]"]' ).val() ) || 0
        const feeType = $row.find( 'select[name*="[fee_type]"]' ).val()
        const code = $row.find( '.yopago-currency-select' ).val()
        const currency = window.yopagoCurrencies.find( c => c.code === code )
        if ( !currency ) {
            return
        }

        $modalContent.html( generateExampleHTML( currency, rate, fee, feeType ) )
        $modal.show()
    } )

    $( '#yopago-modal-close' ).on( 'click', () => $modal.hide() )
}
