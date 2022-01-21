<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Entrypoints
    |--------------------------------------------------------------------------
    | Entrypoints are scripts or style files that should be included by
    | default. The `entrypoints` settings makes it easy to include a whole
    | directory, or you can chose a specific file if you prefer.
    */
    'entrypoints' => [
        'resources/js',
        'resources/scripts',
    ],
    'ignore_patterns' => [
        '/\\.d\\.ts$/',
        '/\\.json$/',
    ],

    /*
    |--------------------------------------------------------------------------
    | SSR
    |--------------------------------------------------------------------------
    | When building an SSR bundle, you must specify an entrypoint.
    | This setting defines the SSR entrypoint that will be used when using
    | the --ssr flag.
    */
    'ssr_entrypoint' => null,

    /*
    |--------------------------------------------------------------------------
    | Aliases
    |--------------------------------------------------------------------------
    | These aliases will be added to the Vite configuration and used
    | to generate a proper tsconfig.json file.
    */
    'aliases' => [
        '@' => 'resources',
    ],

    /*
    |--------------------------------------------------------------------------
    | Static assets path
    |--------------------------------------------------------------------------
    | This option defines the directory that Vite considers as the
    | public directory. Its content will be copied to the build directory
    | at build-time.
    | https://vitejs.dev/config/#publicdir
    */
    'public_directory' => resource_path('static'),

    /*
    |--------------------------------------------------------------------------
    | Ping timeout
    |--------------------------------------------------------------------------
    | The maximum duration, in seconds, that the ping to `ping_url` or
    | `dev_url` should take while trying to determine whether to use the
    | manifest or the server in a local environment. Using false will disable
    | the feature.
    | https://laravel-vite.innocenzi.dev/guide/configuration.html#ping-timeout
    */
    'ping_timeout' => .01,
    'ping_url' => null,

    /*
    |--------------------------------------------------------------------------
    | Build path
    |--------------------------------------------------------------------------
    | The directory, relative to /public, in which Vite will build
    | the production files. This should match "build.outDir" in the Vite
    | configuration file.
    */
    'build_path' => 'build',

    /*
    |--------------------------------------------------------------------------
    | Development URL
    |--------------------------------------------------------------------------
    | The URL at which the Vite development server runs.
    | This is used to generate the script tags when developing.
    */
    'dev_url' => env('DEV_SERVER_URL', 'http://localhost:3000'),

    /*
    |--------------------------------------------------------------------------
    | Commands
    |--------------------------------------------------------------------------
    | Defines the list of artisan commands that will be executed when
    | the development server or the production build starts.
    */
    'commands' => [
        'vite:aliases',
        // 'typescript:generate'
    ],

    /*
    |--------------------------------------------------------------------------
    | Testing
    |--------------------------------------------------------------------------
    | Specifies settings related to testing.
    | If `use_manifest` is set to false, Laravel Vite will not attempt to
    | use the manifest when running tests.
    */
    'testing' => [
        'use_manifest' => false,
    ],
];
