export function generateExampleHTML( currency, rate, fee, feeType ) {
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
      <li>${ wc_yopago_params.ex_fee }: ${ feeType === 'fixed' ? wc_yopago_params.fixed + ' ' + fee : fee + '%' }</li>
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
        <tr><td>${ wc_yopago_params.ex_c_subtotal.replace( '{to}', 'BOB' ) }</td><td>Bs. ${ subtotal.toFixed( 2 ) }</td></tr>
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
  `
}
