import jQuery from 'jquery'
import 'select2/dist/js/select2.full'
import { initializeCurrencyDropdown, updateAddButtonState, updateCurrencyOptions } from './currency-utils'
import { bindUIEvents } from './ui-events'

window.jQuery = window.$ = jQuery

jQuery( document ).ready( function ( $ ) {
    const $table = $( '#yopago-currency-rates-table' )
    const $modal = $( '#yopago-example-modal' )
    const $modalContent = $( '#yopago-example-text' )
    const $addBtn = $( '#yopago-add-rate' )

    initCurrencyRateManager()

    // ========== listeners “sueltos” ==========
    $( document ).on( 'change', '.yopago-currency-select', function () {
        updateCurrencyOptions()
        updateAddButtonState()
        $( this ).closest( 'tr' ).find( '.yopago-example-btn' )
                 .prop( 'disabled', !$( this ).val() )
        $( '#mainform :input' ).first().trigger( 'change' )
    } )

    $( '#mainform' ).on( 'submit', function () {
        $table.find( 'tbody tr' ).each( function () {
            if ( !$( this ).find( '.yopago-currency-select' ).val() ) {
                $( this ).remove()
            }
        } )
    } )

    // ========== core ==========
    function initCurrencyRateManager() {
        loadCurrencyData()
        bindUIEvents( { $table, $addBtn, $modal, $modalContent } )
    }

    function loadCurrencyData() {
        $.getJSON( wc_yopago_params.currency_data_url )
         .done( function ( currencies ) {
             window.yopagoCurrencies = currencies
             $( '.yopago-currency-select' ).each( function () {
                 initializeCurrencyDropdown( $( this ) )
             } )
             updateCurrencyOptions()
             updateAddButtonState()
         } )
         .fail( function ( jqxhr, status, error ) {
             console.error( 'Failed to load currencies:', status, error )
             console.log( 'Attempted path:', wc_yopago_params.currency_data_url )
         } )
    }
} )
