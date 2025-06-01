import { resolve } from 'path'
import { defineConfig } from 'vite'

export default defineConfig(
    {
        build: {
            rollupOptions: {
                input: {
                    currencyTable: resolve( __dirname, 'assets/js/admin/currency-table/index.js' )
                },
                output: {
                    entryFileNames: 'currency-table.js',
                    dir: 'assets/js/admin',
                    format: 'iife',
                    globals: { jquery: 'jQuery', select2: 'jQuery.fn.select2' }
                },
                external: [ 'jquery', 'select2' ]
            },
            minify: 'terser',
            emptyOutDir: false
        }
    } )
