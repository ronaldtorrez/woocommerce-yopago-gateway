export function buildCurrencyRateRow( index ) {
    return `
  <tr data-index='${ index }'>
    <td>
      <select class='yopago-currency-select' name='currency_rates[${ index }][currency]'>
        <option value='' disabled selected hidden></option>
      </select>
    </td>
    <td><input  type='number' step='0.0001' name='currency_rates[${ index }][rate]' value='1' min='0.0001' required></td>
    <td><input  type='number' step='0.01'  name='currency_rates[${ index }][fee]'  value='0' min='0'     required></td>
    <td>
      <select name='currency_rates[${ index }][fee_type]'>
        <option value='fixed'>${ wc_yopago_params.fixed }</option>
        <option value='percent'>${ wc_yopago_params.percent }</option>
      </select>
    </td>
    <td>
      <button type='button' class='button yopago-example-btn' data-index='${ index }' disabled>
        ${ wc_yopago_params.view }
      </button>
    </td>
    <td>
      <button type='button' class='button button-link-delete yopago-remove-rate'>
        ${ wc_yopago_params.remove }
      </button>
    </td>
  </tr>`
}
