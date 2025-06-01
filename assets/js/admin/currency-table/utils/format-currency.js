import $ from 'jquery'

export function formatCurrency( option, isResult ) {
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
