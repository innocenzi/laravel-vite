# Migration to Official Laravel Vite Plugin

This guide is a rough migration guide to move from Innocenzi's vite integration to the new Laravel one. If you notice any issues or omissions, feel free to pr :)

LP = Legacy Plugin, aka `innocenzi/laravel-vite`

1. Remove `innocenzi/laravel-vite` from your `composer.json` and run `composer update`
2. Run `yarn remove laravel-vite` and then `yarn add laravel-vite-plugin` to swap to the official plugin
3. Under the plugins section in your `vite.config.ts`, create a new laravel plugin instance and port your LP settings over.
   - Swap the old import to the new one `import laravel from 'laravel-vite-plugin'`
   - Head over to your LP `vite.php` config file, and migrate the entry points over to the `input` key in the new config. Note that the laravel plugin wants specific files, and not just a directory like the LP.
   - If you are using valet with the LP, swap from the old config option to the `valetTls` key, specifying your domain.
   - Migrate your aliases from `vite.php` to the `resolve->alias` option in the vite config, in particular the `@` alias.

   **Example config after migration**
    ```js
    defineConfig({
      optimizeDeps: {
        include: [
          'ziggy', // Include ziggy in build
        ],
      },
      resolve: {
        alias: {
          'ziggy': 'vendor/tightenco/ziggy/dist/vue.m.js', // ziggy alias
          '@': 'resources', // ported alias from vite.php
        }
      },
      plugins: [
        vue(), // Other plugins
        laravel({
          input: [ // Entrypoints
            'resources/js/main.ts',
            'resources/js/ziggy.js',
          ],
          valetTls: 'domain.test', // Valet domain
        })
      ]
    })
     ```
4. Update the `@vite` import in your base blade template to contain the specific entrypoints, instead of the generic entry before, ie:
   ```diff
   -@vite
   +@vite('resources/js/main.ts')
   ```

