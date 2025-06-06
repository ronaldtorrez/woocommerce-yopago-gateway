import { resolve } from 'path'
import { terser } from 'rollup-plugin-terser'
import { defineConfig } from 'vite'

export default defineConfig(
    {
        build: {
            minify: false,
            emptyOutDir: false,
            rollupOptions: {
                input: {
                    currencyTable: resolve(
                        __dirname,
                        'assets/js/admin/currency-table/index.js'
                    )
                },
                external: [ 'jquery', 'select2' ],
                output: [
                    {
                        entryFileNames: 'currency-table.min.js',
                        dir: 'assets/js/admin',
                        format: 'iife',
                        globals: { jquery: 'jQuery', select2: 'jQuery.fn.select2' },
                        plugins: [ terser() ]
                    }
                ]
            }
        }
    }
)
