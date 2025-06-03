// generate-currencies-bob.js
// This script builds a full list of world currencies with:
// - ISO code, name, symbol, flag icon URL
// - Exchange rate to Bolivian Boliviano (BOB)
// Output: currencies-to-bob.json (saved next to this script)

import fs from 'fs/promises'
import fetch from 'node-fetch'
import path from 'path'
import { fileURLToPath } from 'url'

/* --------------------------------------------------------------------------
 0. Determine output location (same directory as this script)
 This ensures the JSON is written where the script resides, regardless
 of where it's executed from.
 --------------------------------------------------------------------------- */
const __filename = fileURLToPath( import.meta.url )
const __dirname = path.dirname( __filename )
const OUTPUT = path.join( __dirname, 'currencies.json' )

/* --------------------------------------------------------------------------
 1. Base currency metadata
 Initial list of currencies, including USD, EUR, and Latin American
 examples. Each item includes ISO code, name, symbol, and flag code.
 --------------------------------------------------------------------------- */
const metadata = [
    { code: 'USD', name: 'US Dollar', symbol: '$', flag: 'us' },
    { code: 'EUR', name: 'Euro', symbol: '€', flag: 'eu' },
    { code: 'BRL', name: 'Brazilian Real', symbol: 'R$', flag: 'br' },
    { code: 'ARS', name: 'Argentine Peso', symbol: '$', flag: 'ar' },
    { code: 'CLP', name: 'Chilean Peso', symbol: '$', flag: 'cl' }
]

/* --------------------------------------------------------------------------
 2. Load ISO 4217 currency list from GitHub
 Adds any missing currencies (code + name) not already in metadata.
 Default symbol is the code itself. Flags use the first 2 letters.
 --------------------------------------------------------------------------- */
const iso4217 = await fetch(
    'https://raw.githubusercontent.com/umpirsky/currency-list/master/data/en_US/currency.json'
).then( res => res.json() )

for ( const [ code, name ] of Object.entries( iso4217 ) ) {
    if ( !metadata.find( m => m.code === code ) ) {
        metadata.push( {
                           code,
                           name,
                           symbol: code,
                           flag: code.slice( 0, 2 ).toLowerCase()
                       } )
    }
}

/* --------------------------------------------------------------------------
 3. Get exchange rates from BOB to all currencies
 Source: @fawazahmed0/currency-api (CDN version, no API key required)
 Converts "1 BOB → other" to "1 OTHER → BOB" via inversion.
 --------------------------------------------------------------------------- */
const ratesData = await fetch(
    'https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/bob.json'
).then( res => res.json() )

const bobToOthers = ratesData.bob

const otherToBob = Object.fromEntries(
    Object.entries( bobToOthers ).map( ( [ code, rate ] ) => [
        code.toUpperCase(),
        1 / rate
    ] )
)

/* --------------------------------------------------------------------------
 4. Merge metadata with exchange rates and flag URLs
 Builds final array: includes symbol, full name, ISO code, flag icon,
 and current rate to BOB. Sorted alphabetically by code.
 --------------------------------------------------------------------------- */
const allCurrencies = metadata.map( entry => (
    {
        ...entry,
        flag: `https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/flags/4x3/${ entry.flag }.svg`,
        rateToBOB: +(
            otherToBob[ entry.code ]?.toFixed( 6 ) || NaN
        )
    }
) ).sort( ( a, b ) => a.code.localeCompare( b.code ) )

/* --------------------------------------------------------------------------
 5. Save output JSON to the same directory as this script
 This ensures the file is written next to generate-currencies-bob.js,
 regardless of the working directory used to run the script.
 --------------------------------------------------------------------------- */
await fs.writeFile( OUTPUT, JSON.stringify( allCurrencies, null, 2 ) )

console.log( `✅  ${ allCurrencies.length } currencies saved to ${ OUTPUT }` )
