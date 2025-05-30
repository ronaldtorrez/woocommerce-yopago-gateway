// assets/js/admin/currency-table.js
jQuery( document ).ready( function ( $ ) {
    const table = $( '#yopago-currency-rates-table' )
    const modal = $( '#yopago-example-modal' )
    const modalText = $( '#yopago-example-text' )

    // Cargar datos de divisas
    $.getJSON( wc_yopago_params.currency_data_url, function ( currencies ) {
        window.yopagoCurrencies = currencies
        $( '.yopago-currency-select' ).each( function () {
            initCurrencySelect( $( this ) )
        } )
    } ).fail( function ( jqxhr, textStatus, error ) {
        console.error( 'Error al cargar divisas:', textStatus, error )
        console.log( 'Ruta intentada:', wc_yopago_params.currency_data_url )
    } )

    // Añadir nueva fila
    $( '#yopago-add-rate' ).click( function () {
        const index = Date.now()
        const row = `
        <tr data-index='${ index }'>
            <td>
                <select class='yopago-currency-select' name='currency_rates[${ index }][currency]'></select>
            </td>
            <td><input type='number' step='0.0001' name='currency_rates[${ index }][rate]' value='1' min='0.0001' required></td>
            <td><input type='number' step='0.01' name='currency_rates[${ index }][fee]' value='0' min='0' required></td>
            <td>
                <select name='currency_rates[${ index }][fee_type]'>
                    <option value='fixed'>${ wc_yopago_params.fixed }</option>
                    <option value='percent'>${ wc_yopago_params.percent }</option>
                </select>
            </td>
            <td>
                <button type='button' class='button yopago-example-btn' data-index='${ index }'>
                    ${ wc_yopago_params.view }
                </button>
            </td>
            <td>
                <button type='button' class='button button-link-delete yopago-remove-rate'>
                    ${ wc_yopago_params.remove }
                </button>
            </td>
        </tr>`

        table.find( 'tbody .empty-row' ).remove()
        table.find( 'tbody' ).append( row )
        initCurrencySelect( table.find( `tr[data-index="${ index }"] .yopago-currency-select` ) )
    } )

    // Eliminar fila
    table.on( 'click', '.yopago-remove-rate', function () {
        const row = $( this ).closest( 'tr' )
        row.remove()

        if ( table.find( 'tbody tr' ).length === 0 ) {
            table.find( 'tbody' ).append( `
                <tr class='empty-row'>
                    <td colspan='6'>${ wc_yopago_params.no_currencies }</td>
                </tr>
            ` )
        }
    } )

    // Mostrar ejemplo
    table.on( 'click', '.yopago-example-btn', function () {
        const index = $( this ).data( 'index' )
        const row = $( `tr[data-index="${ index }"]` )

        const rate = parseFloat( row.find( 'input[name*="[rate]"]' ).val() ) || 1
        const fee = parseFloat( row.find( 'input[name*="[fee]"]' ).val() ) || 0
        const feeType = row.find( 'select[name*="[fee_type]"]' ).val()
        const currencyCode = row.find( '.yopago-currency-select' ).val()
        const currency = window.yopagoCurrencies.find( c => c.code === currencyCode )

        if ( !currency ) {
            return
        }

        const amount = 100 // Ejemplo: 100 BOB
        let converted = amount * rate

        if ( feeType === 'fixed' ) {
            converted += fee
        } else {
            converted += (
                             converted * fee
                         ) / 100
        }

        const exampleText = wc_yopago_params.example_text
                                            .replace( '{amount}', amount )
                                            .replace( '{currency}', `${ currency.name } (${ currency.symbol })` )
                                            .replace( '{converted}', converted.toFixed( 2 ) )
                                            .replace( '{symbol}', currency.symbol )

        modalText.html( exampleText )
        modal.show()
    } )

    // Cerrar modal
    $( '#yopago-modal-close' ).click( function () {
        modal.hide()
    } )

    // Inicializar select de divisas
    function initCurrencySelect( select ) {
        const selectedCurrency = select.data( 'selected' )

        select.empty()

        window.yopagoCurrencies.forEach( currency => {
            select.append( new Option(
                `${ currency.name } (${ currency.symbol })`,
                currency.code,
                false,
                currency.code === selectedCurrency
            ) )
        } )

        select.select2(
            {
                width: '100%',
                placeholder: wc_yopago_params.select_currency,
                allowClear: false,
                templateResult: formatCurrency,
                templateSelection: formatCurrencySelection
            }
        )

        if ( selectedCurrency ) {
            select.val( selectedCurrency ).trigger( 'change' )
        }
    }

    // Formatear opciones de divisa
    function formatCurrency( currency ) {
        if ( !currency.id ) {
            return currency.text
        }
        const curr = window.yopagoCurrencies.find( c => c.code === currency.id )
        if ( !curr ) {
            return currency.text
        }

        return $( '<span><img src="' + curr.flag + '" class="yopago-flag" /> ' +
                  curr.name + ' (' + curr.symbol + ')</span>' )
    }

    function formatCurrencySelection( currency ) {
        if ( !currency.id ) {
            return currency.text
        }
        const curr = window.yopagoCurrencies.find( c => c.code === currency.id )
        return curr ? curr.code + ' - ' + curr.name : currency.text
    }
} )
