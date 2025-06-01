import $ from 'jquery'

export const markSettingsAsDirty = () => $( '#mainform :input' ).first().trigger( 'change' )

export const randomInt = ( min, max ) => {
    return Math.floor( Math.random() * (
        max - min + 1
    ) ) + min
}
