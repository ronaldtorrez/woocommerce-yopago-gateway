jQuery( document ).ready( function ( $ ) {

    const $table = $( '#yopago-currency-rates-table' )
    const $modal = $( '#yopago-example-modal' )
    const $modalContent = $( '#yopago-example-text' )
    const $addBtn = $( '#yopago-add-rate' )

    initCurrencyRateManager()

    $( document ).on( 'change', '.yopago-currency-select', function () {
        updateCurrencyOptions()
        updateAddButtonState()
        markSettingsAsDirty()
    } )

    function updateCurrencyOptions() {
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
                $( this ).prop(
                    'disabled',
                    code !== myCode && selectedCodes.includes( code )
                )
            } )

            const currentVal = $sel.val()
            $sel.select2( 'destroy' )
            attachSelect2( $sel )
            if ( currentVal ) {
                $sel.val( currentVal ).trigger( 'change.select2' )
            }
        } )
    }

    function updateAddButtonState() {
        const totalCurrencies = window.yopagoCurrencies ? window.yopagoCurrencies.length : 0
        const rowCount = $table.find( 'tbody tr' ).not( '.empty-row' ).length
        $addBtn.prop( 'disabled', rowCount >= totalCurrencies )
    }

    function initCurrencyRateManager() {
        loadCurrencyData()
        bindUIEvents()
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

    function bindUIEvents() {
        $addBtn.on( 'click', handleAddRate )
        $table.on( 'click', '.yopago-remove-rate', handleRemoveRate )
        $table.on( 'click', '.yopago-example-btn', handleShowExample )
        $( '#yopago-modal-close' ).on( 'click', () => $modal.hide() )
    }

    function handleAddRate() {
        const index = Date.now()
        const newRow = buildCurrencyRateRow( index )

        $table.find( 'tbody .empty-row' ).remove()
        $table.find( 'tbody' ).append( newRow )

        const $newSelect = $table.find( `tr[data-index="${ index }"] .yopago-currency-select` )
        initializeCurrencyDropdown( $newSelect )

        updateCurrencyOptions()
        updateAddButtonState()
        markSettingsAsDirty()
    }

    function handleRemoveRate() {
        $( this ).closest( 'tr' ).remove()

        if ( $table.find( 'tbody tr' ).length === 0 ) {
            $table.find( 'tbody' ).append(
                `<tr class='empty-row'><td colspan='6'>${ wc_yopago_params.no_currencies }</td></tr>`
            )
        }

        updateCurrencyOptions()
        updateAddButtonState()
        markSettingsAsDirty()
    }

    function handleShowExample() {
        const index = $( this ).data( 'index' )
        const $row = $( `tr[data-index="${ index }"]` )

        const rate = parseFloat( $row.find( 'input[name*="[rate]"]' ).val() ) || 1
        const fee = parseFloat( $row.find( 'input[name*="[fee]"]' ).val() ) || 0
        const feeType = $row.find( 'select[name*="[fee_type]"]' ).val()
        const currencyCode = $row.find( '.yopago-currency-select' ).val()
        const currency = window.yopagoCurrencies.find( c => c.code === currencyCode )
        if ( !currency ) {
            return
        }

        const exampleHTML = generateExampleHTML( currency, rate, fee, feeType )
        $modalContent.html( exampleHTML )
        $modal.show()
    }

    function buildCurrencyRateRow( index ) {
        return `
            <tr data-index='${ index }'>
                <td>
                    <select class='yopago-currency-select'
                            name='currency_rates[${ index }][currency]'>
                        <option value='' disabled selected hidden></option>
                    </select>
                </td>
                <td>
                    <input type='number' step='0.0001'
                           name='currency_rates[${ index }][rate]' value='1'
                           min='0.0001' required>
                </td>
                <td>
                    <input type='number' step='0.01'
                           name='currency_rates[${ index }][fee]' value='0'
                           min='0' required>
                </td>
                <td>
                    <select name='currency_rates[${ index }][fee_type]'>
                        <option value='fixed'>${ wc_yopago_params.fixed }</option>
                        <option value='percent'>${ wc_yopago_params.percent }</option>
                    </select>
                </td>
                <td>
                    <button type='button' class='button yopago-example-btn'
                            data-index='${ index }'>
                        ${ wc_yopago_params.view }
                    </button>
                </td>
                <td>
                    <button type='button' class='button button-link-delete yopago-remove-rate'>
                        ${ wc_yopago_params.remove }
                    </button>
                </td>
            </tr>
        `
    }

    function initializeCurrencyDropdown( $select ) {
        const selectedValue = $select.data( 'selected' )
        $select.empty().append(
            `<option value='' disabled ${ selectedValue ? '' : 'selected' } hidden></option>`
        )

        window.yopagoCurrencies.forEach( currency => {
            $select.append(
                new Option(
                    `${ currency.name } (${ currency.symbol })`,
                    currency.code,
                    false,
                    currency.code === selectedValue
                )
            )
        } )

        attachSelect2( $select )

        if ( selectedValue ) {
            $select.val( selectedValue ).trigger( 'change.select2' )
        }
    }

    function attachSelect2( $sel ) {
        $sel.select2( {
                          width: '100%',
                          placeholder: wc_yopago_params.select_currency,
                          allowClear: false,
                          templateResult: formatCurrencyOption,
                          templateSelection: formatCurrencySelection
                      } )
    }

    function formatCurrencyOption( option ) {
        if ( !option.id ) {
            return option.text
        }
        const currency = window.yopagoCurrencies.find( c => c.code === option.id )
        return currency
               ? $( `<span><img src='${ currency.flag }' class='yopago-flag' /> ${ currency.name } (${ currency.symbol })</span>` )
               : option.text
    }

    function formatCurrencySelection( option ) {
        if ( !option.id ) {
            return option.text
        }
        const currency = window.yopagoCurrencies.find( c => c.code === option.id )
        return currency ? `${ currency.code } - ${ currency.name }` : option.text
    }

    function markSettingsAsDirty() {
        $( '#mainform :input' ).first().trigger( 'change' )
    }

    function generateExampleHTML( currency, rate, fee, feeType ) {
        const orderAmount = 50
        const subtotal = orderAmount * rate
        const feeAmount = feeType === 'fixed' ? fee : subtotal * (
            fee / 100
        )
        const total = subtotal + feeAmount
        return `
            <h4>${ wc_yopago_params.ex_title.replace( '{from}', currency.code ).replace( '{to}', 'BOB' ) }</h4>
            <p><strong>${ wc_yopago_params.ex_assumptions }</strong></p>
            <ul>
                <li>${ wc_yopago_params.ex_site_currency }: ${ currency.code }</li>
                <li>${ wc_yopago_params.ex_rate }: 1 ${ currency.code } = ${ rate.toFixed( 4 ) } BOB</li>
                <li>${ wc_yopago_params.ex_fee }: ${ feeType === 'fixed'
                                                     ? wc_yopago_params.fixed + ' ' + fee
                                                     : fee + '%' }</li>
                <li>${ wc_yopago_params.ex_original }: ${ currency.symbol }${ orderAmount.toFixed( 2 ) }</li>
            </ul>
            <p><strong>${ wc_yopago_params.ex_calc }</strong></p>
            <table class='widefat'>
                <thead><tr><th>${ wc_yopago_params.ex_concept }</th><th>${ wc_yopago_params.ex_value }</th></tr></thead>
                <tbody>
                    <tr><td>${ wc_yopago_params.ex_c_original.replace(
            '{from}',
            currency.code
        ) }</td><td>${ currency.symbol }${ orderAmount.toFixed( 2 ) }</td></tr>
                    <tr><td>${ wc_yopago_params.ex_c_rate }</td><td>1 ${ currency.code } = ${ rate.toFixed( 4 ) } BOB</td></tr>
                    <tr><td>${ wc_yopago_params.ex_c_subtotal.replace(
            '{to}',
            'BOB'
        ) }</td><td>Bs. ${ subtotal.toFixed( 2 ) }</td></tr>
                    <tr><td>${ wc_yopago_params.ex_c_commission }</td><td>Bs. ${ feeAmount.toFixed( 2 ) }</td></tr>
                    <tr><td><strong>${ wc_yopago_params.ex_c_total.replace(
            '{to}',
            'BOB'
        ) }</strong></td><td><strong>Bs. ${ total.toFixed( 2 ) }</strong></td></tr>
                </tbody>
            </table>
            <p><strong>${ wc_yopago_params.ex_result }</strong></p>
            <p>${ wc_yopago_params.ex_result_text
                                  .replace( '{symbol}', 'Bs. ' )
                                  .replace( '{total}', total.toFixed( 2 ) )
                                  .replace( '{to}', 'BOB' ) }</p>
        `;
    }
} );
