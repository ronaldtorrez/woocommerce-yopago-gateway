import $ from 'jquery'
import { attachSelect2 } from './attach-select2'

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
