<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configurations
    |--------------------------------------------------------------------------
    | The following describes a set of configurations that can be used
    | independently. Because Vite does not support generating multiple
    | bundles, using separate configuration files is necessary.
    */
    'configs' => [
        'default' => [
            'entrypoints' => [
                'ssr' => 'resources/scripts/ssr.ts',
                'paths' => [
                    'resources/scripts/main.ts',
                    'resources/js/app.js',
                ],
                'ignore' => '/\\.(d\\.ts|json)$/',
            ],
            'dev_server' => [
                'enabled' => true,
                'url' => env('DEV_SERVER_URL', 'http://localhost:3000'),
                'ping_before_using_manifest' => true,
                'ping_url' => null,
                'ping_timeout' => 1,
                'key' => env('DEV_SERVER_KEY'),
                'cert' => env('DEV_SERVER_CERT'),
            ],
            'build_path' => 'build',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Aliases
    |--------------------------------------------------------------------------
    | You can define aliases to avoid having to make relative imports.
    | Aliases will be written to tsconfig.json automatically so your IDE
    | can know how to resolve them.
    */
    'aliases' => [
        '@' => 'resources',
    ],

    /*
    |--------------------------------------------------------------------------
    | Commands
    |--------------------------------------------------------------------------
    | Before starting the development server or building the assets, you
    | may need to run specific commands. With these options, you can
    | define what to run, automatically.
    */
    'commands' => [
        'artisan' => [
            // 'typescript:generate'
        ],
        'shell' => [
            //
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Testing
    |--------------------------------------------------------------------------
    | While testing, you may need to use the manifest, or not, depending
    | what and how you test. You can tune these options according to
    | your needs.
    */
    'testing' => [
        'use_manifest' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default interfaces
    |--------------------------------------------------------------------------
    | Here you may change how some parts of the package work by replacing
    | their associated logic.
    */
    'server_checker' => Innocenzi\Vite\ServerCheckers\HttpServerChecker::class,
    'tag_generator' => Innocenzi\Vite\TagGenerators\DefaultTagGenerator::class,
    'entrypoints_finder' => Innocenzi\Vite\EntrypointsFinder\DefaultEntrypointsFinder::class,

    /*
    |--------------------------------------------------------------------------
    | Default configuration
    |--------------------------------------------------------------------------
    | Here you may specify which of the configurations above you wish
    | to use as your default one.
    */
    'default' => env('VITE_DEFAULT_CONFIG', 'default'),
];
