<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Entrypoints
    |--------------------------------------------------------------------------
    | The files in the configured directories will be considered
    | entry points and will not be required in the configuration file.
    | To disable the feature, set to false.
    */
    'entrypoints' => [
        'resources/js',
        'resources/scripts',
    ],
    'ignore_patterns' => ['/\\.d\\.ts$/'],

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
    | The maximum duration, in seconds, that the ping to the development
    | server should take while trying to determine whether to use the
    | manifest or the server in a local environment. Using false will disable
    | the feature.
    | https://laravel-vite.innocenzi.dev/guide/configuration.html#ping-timeout
    */
    'ping_timeout' => .1,

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
    'dev_url' => 'http://localhost:3000',

    /*
    |--------------------------------------------------------------------------
    | Inject asset-fixing plugin
    |--------------------------------------------------------------------------
    | Currently, Vite does not support loading assets from an URL other than
    | the development server's URL. If this option is enabled, a plugin fixing
    | this issue will be injected.
    | See: https://github.com/innocenzi/laravel-vite/issues/31
    */
    'asset_plugin' => [
        'find_regex' => '/\/resources\/(.*)\.(svg|jp?g|png|webp)/',
        'replace_with' => '/resources/$1.$2',
    ],

    /*
    |--------------------------------------------------------------------------
    | Commands
    |--------------------------------------------------------------------------
    | Defines the list of artisan commands that will be executed when
    | the development server starts.
    */
    'commands' => [
        'vite:aliases',
        // 'typescript:generate'
    ],
];
