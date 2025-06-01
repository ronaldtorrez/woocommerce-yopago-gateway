import $ from 'jquery'

export function updateCurrencyOptions() {
    const selectedCodes = $( '.yopago-currency-select' )
        .map( function () {
            return $( this ).val()
        } )
        .get()
        .filter( Boolean )

    $( '.yopago-currency-select' ).each( function () {
        const $sel = $( this )
        const myCode = $sel.val()

        $sel.find( 'option' ).prop( 'disabled', false )
        $sel.find( 'option' ).each( function () {
            const code = this.value
            if ( !code ) {
                return
            }
            $( this ).prop( 'disabled', code !== myCode && selectedCodes.includes( code ) )
        } )

        const currentVal = $sel.val()
        $sel.select2( 'destroy' )
        attachSelect2( $sel )
        if ( currentVal ) {
            $sel.val( currentVal ).trigger( 'change.select2' )
        }
    } )
}

export function updateAddButtonState() {
    const total = window.yopagoCurrencies ? window.yopagoCurrencies.length : 0
    const rows = $( '#yopago-currency-rates-table tbody tr' ).not( '.empty-row' ).length
    $( '#yopago-add-rate' ).prop( 'disabled', rows >= total )
}

export function initializeCurrencyDropdown( $select ) {
    const selected = $select.data( 'selected' )
    $select.empty().append(
        `<option value='' disabled ${ selected ? '' : 'selected' } hidden></option>`
    )

    window.yopagoCurrencies.forEach( c => {
        $select.append(
            new Option( `${ c.name } (${ c.symbol })`, c.code, false, c.code === selected )
        )
    } )
    attachSelect2( $select )
    if ( selected ) {
        $select.val( selected ).trigger( 'change.select2' )
    }
    $select.closest( 'tr' ).find( '.yopago-example-btn' ).prop( 'disabled', !$select.val() )
}

export function attachSelect2( $sel ) {
    $sel.select2( {
                      width: '100%',
                      placeholder: wc_yopago_params.select_currency,
                      allowClear: false,
                      templateResult: option => formatCurrency( option, true ),
                      templateSelection: option => formatCurrency( option, false )
                  } )
}

function formatCurrency( option, isResult ) {
    if ( !option.id ) {
        return option.text
    }
    const c = window.yopagoCurrencies.find( x => x.code === option.id )
    if ( !c ) {
        return option.text
    }
    return isResult
           ? $( `<span><img src='${ c.flag }' class='yopago-flag' /> ${ c.name } (${ c.symbol })</span>` )
           : `${ c.code } - ${ c.name }`
}

export const markSettingsAsDirty = () =>
    $( '#mainform :input' ).first().trigger( 'change' )
