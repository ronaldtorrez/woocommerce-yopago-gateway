import { randomInt } from './utils/global'

export function generateExampleHTML( currency, rate, fee, feeType ) {
    const orderAmount = randomInt( 20, 200 )
    const subtotal = orderAmount * rate
    const feeAmount = feeType === 'fixed' ? fee : subtotal * (
        fee / 100
    )
    const total = subtotal + feeAmount

    return `
    <h4>${ wc_yopago_params.ex_title.replace( '{from}', currency.code ).replace( '{to}', 'BOB' ) }</h4>
    <p><strong>${ wc_yopago_params.ex_assumptions }</strong></p>
    <ul>
      <li>${ wc_yopago_params.ex_site_currency }: ${ currency.code } => ${ currency.symbol }</li>
      <li>${ wc_yopago_params.ex_rate }: 1 ${ currency.code } = ${ rate.toFixed( 4 ) } BOB</li>
      <li>${ feeType === 'fixed' ? wc_yopago_params.ex_fee_fixed : wc_yopago_params.ex_fee_percent }: 
      ${ fee }${ feeType === 'fixed' ? ' BOB' : '%' }
      </li>
      <li>${ wc_yopago_params.ex_original }: ${ orderAmount.toFixed( 2 ) } ${ currency.code }</li>
    </ul>
    <p><strong>${ wc_yopago_params.ex_calc }</strong></p>
    <table class='widefat'>
      <thead>
          <tr>
              <th>${ wc_yopago_params.ex_concept }</th>
              <th>${ wc_yopago_params.ex_value }</th>
          </tr>
      </thead>
      <tbody>
        <tr>
            <td>${ wc_yopago_params.ex_c_original.replace( '{from}', currency.code ) }</td>
            <td>${ orderAmount.toFixed( 2 ) } ${ currency.code }</td>
        </tr>
        <tr>
            <td>${ wc_yopago_params.ex_c_rate }</td>
            <td>1 ${ currency.code } = ${ rate.toFixed( 4 ) } BOB</td>
        </tr>
        <tr>
            <td>${ wc_yopago_params.ex_c_subtotal.replace( '{to}', 'BOB' ) }</td>
            <td>${ subtotal.toFixed( 2 ) } BOB</td>
        </tr>
        <tr>
            <td>${ feeType === 'fixed' ? wc_yopago_params.ex_fee_fixed : wc_yopago_params.ex_fee_percent }</td>
            <td>${ feeAmount.toFixed( 2 ) } BOB</td>
        </tr>
        <tr>
            <td>
                <strong>${ wc_yopago_params.ex_c_total.replace( '{to}', 'BOB' ) }</strong>
            </td>
            <td>
                <strong>${ total.toFixed( 2 ) } BOB</strong>
            </td>
        </tr>
      </tbody>
    </table>
    <p><strong>${ wc_yopago_params.ex_result }</strong></p>
    <p>
    ${ wc_yopago_params.ex_result_text
                       .replace( '{total}', total.toFixed( 2 ) )
                       .replace( '{to}', 'BOB' ) }
    </p>
  `
}
