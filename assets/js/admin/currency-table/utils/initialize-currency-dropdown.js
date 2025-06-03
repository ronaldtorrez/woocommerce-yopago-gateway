import { attachSelect2 } from './attach-select2'

export function initializeCurrencyDropdown( $select ) {
    const selected = $select.data( 'selected' )
    $select.empty().append(
        `<option value='' disabled ${ selected ? '' : 'selected' } hidden></option>`
    )

    window.yopagoCurrencies.forEach( c => {
        $select.append( new Option(
                            `${ c.name } (${ c.code } - ${ c.symbol })`,
                            c.code,
                            false,
                            c.code === selected
                        )
        )
    } )

    attachSelect2( $select )

    if ( selected ) {
        $select.val( selected ).trigger( 'change.select2' )
    }

    if ( selected ) {
        $select.val( selected ).trigger( 'change.select2' )
        const currency = window.yopagoCurrencies.find( c => c.code === selected )
        const rate = (
                         currency && currency.rateToBOB != null
                     )
                     ? currency.rateToBOB
                     : 1
        $select.closest( 'tr' ).find( 'input[name*="[rate]"]' ).val( rate.toFixed( 4 ) )
    }

    $select.closest( 'tr' ).find( '.yopago-example-btn' ).prop( 'disabled', !$select.val() )
}
