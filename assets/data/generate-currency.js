// generate-currencies-bob.js
// -----------------------------------------------
// Builds a complete currency list that contains
//   • ISO code, English name, symbol, flag URL
//   • Exchange-rate *to* Bolivian Boliviano (BOB)
// Output: currencies.json (saved next to script)
// -----------------------------------------------

import fs from 'fs/promises'
import fetch from 'node-fetch'
import path from 'path'
import { fileURLToPath } from 'url'

/* ───────────────────────────────────────────────
 0. Figure out the output location (same folder)
 ------------------------------------------------*/
const __filename = fileURLToPath( import.meta.url )
const __dirname = path.dirname( __filename )
const OUTPUT = path.join( __dirname, 'currencies.json' )

/* ───────────────────────────────────────────────
 1. Seed list – the ones you care about most
 (can be empty; they’ll be updated in step 2)
 ------------------------------------------------*/
const metadata = []

/* ───────────────────────────────────────────────
 2. Pull an extended list with real symbols
 Source: “Common-Currency-Codes” JSON Gist
 https://gist.githubusercontent.com/ksafranski/2973986/raw
 ------------------------------------------------*/
const extended = await fetch(
    'https://gist.githubusercontent.com/ksafranski/2973986/raw'
).then( res => res.json() )   // key = ISO code, value = { name, symbol, … }

for ( const [ code, data ] of Object.entries( extended ) ) {
    const entry = {
        code,
        name: data.name,
        symbol: data.symbol || data.symbol_native || code,
        flag: code.slice( 0, 2 ).toLowerCase()                // fallback for flags
    }

    // Update if already present, otherwise append
    const existing = metadata.find( m => m.code === code )
    if ( existing ) {
        Object.assign( existing, entry )
    } else {
        metadata.push( entry )
    }
}

/* ───────────────────────────────────────────────
 3. Get BOB → other rates and invert them
 API: @fawazahmed0/currency-api (no key)
 ------------------------------------------------*/
const ratesData = await fetch(
    'https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/bob.json'
).then( res => res.json() )

const bobToOthers = ratesData.bob

const otherToBob = Object.fromEntries(
    Object.entries( bobToOthers ).map( ( [ code, rate ] ) => [
        code.toUpperCase(),
        1 / rate                                              // 1 OTHER → BOB
    ] )
)

/* ───────────────────────────────────────────────
 4. Merge everything, attach flag URL & rate
 ------------------------------------------------*/
const allCurrencies = metadata
    .map( entry => (
        {
            ...entry,
            flag: `https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/flags/4x3/${ entry.flag }.svg`,
            rateToBOB: +(
                otherToBob[ entry.code ]?.toFixed( 6 ) || NaN
            )
        }
    ) )
    .filter( entry => !isNaN( entry.rateToBOB ) )               // drop obsolete codes
    .sort( ( a, b ) => a.code.localeCompare( b.code ) )

/* ───────────────────────────────────────────────
 5. Write the JSON next to this script
 ------------------------------------------------*/
await fs.writeFile( OUTPUT, JSON.stringify( allCurrencies, null, 2 ) )

console.log( `✅  Saved ${ allCurrencies.length } active currencies to ${ OUTPUT }` )
