import { attachSelect2 } from './attach-select2'

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
