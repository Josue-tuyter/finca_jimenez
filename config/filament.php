<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Broadcasting
    |--------------------------------------------------------------------------
    |
    | By uncommenting the Laravel Echo configuration, you may connect Filament
    | to any Pusher-compatible websockets server.
    |
    | This will allow your users to receive real-time notifications.
    |
    */

    'broadcasting' => [

        // 'echo' => [
        //     'broadcaster' => 'pusher',
        //     'key' => env('VITE_PUSHER_APP_KEY'),
        //     'cluster' => env('VITE_PUSHER_APP_CLUSTER'),
        //     'wsHost' => env('VITE_PUSHER_HOST'),
        //     'wsPort' => env('VITE_PUSHER_PORT'),
        //     'wssPort' => env('VITE_PUSHER_PORT'),
        //     'authEndpoint' => '/broadcasting/auth',
        //     'disableStats' => true,
        //     'encrypted' => true,
        //     'forceTLS' => true,
        // ],


    ],

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | This is the storage disk Filament will use to store files. You may use
    | any of the disks defined in the `config/filesystems.php`.
    |
    */

    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Assets Path
    |--------------------------------------------------------------------------
    |
    | This is the directory where Filament's assets will be published to. It
    | is relative to the `public` directory of your Laravel application.
    |
    | After changing the path, you should run `php artisan filament:assets`.
    |
    */

    'assets_path' => null,



    /*
    |--------------------------------------------------------------------------
    | Carbon Locale(Language)
    |--------------------------------------------------------------------------
    |
    | Option to whether change the language for carbon library or not.
    |
    */
    'carbon' => true,

    /*
    |--------------------------------------------------------------------------
    | Language display name
    |--------------------------------------------------------------------------
    |
    | Option to whether dispaly the language in English or Native.
    |
    */
    'native' => true,

    /*
    |--------------------------------------------------------------------------
    | All Locales (Languages)
    |--------------------------------------------------------------------------
    |
    | Uncomment the languages that your site supports - or add new ones.
    | These are sorted by the native name, which is the order you might show them in a language selector.
    |
    */
    'locales' => [
        //'ar'          => ['name' => 'Arabic',                 'script' => 'Arab', 'native' => 'العربية', 'flag_code' => 'sa'],
        'en'          => ['name' => 'English',                'script' => 'Latn', 'native' => 'English', 'flag_code' => 'us'],
        //'es'          => ['name' => 'Spanish',                 'script' => 'Latn', 'native' => 'Español', 'flag_code' => 'es'],

        // 'ace'         => ['name' => 'Achinese',               'script' => 'Latn', 'native' => 'Aceh', 'flag_code' => '' ],
        //'af'          => ['name' => 'Afrikaans',              'script' => 'Latn', 'native' => 'Afrikaans', 'flag_code' => '' ],
        //'agq'         => ['name' => 'Aghem',                  'script' => 'Latn', 'native' => 'Aghem', 'flag_code' => '' ],
        //'ak'          => ['name' => 'Akan',                   'script' => 'Latn', 'native' => 'Akan', 'flag_code' => '' ],
        //'an'          => ['name' => 'Aragonese',              'script' => 'Latn', 'native' => 'aragonés', 'flag_code' => '' ],
        //'cch'         => ['name' => 'Atsam',                  'script' => 'Latn', 'native' => 'Atsam', 'flag_code' => '' ],
        //'gn'          => ['name' => 'Guaraní',                'script' => 'Latn', 'native' => 'Avañe’ẽ', 'flag_code' => '' ],
        //'ae'          => ['name' => 'Avestan',                'script' => 'Latn', 'native' => 'avesta', 'flag_code' => '' ],
        //'ay'          => ['name' => 'Aymara',                 'script' => 'Latn', 'native' => 'aymar aru', 'flag_code' => '' ],
        //'az'          => ['name' => 'Azerbaijani (Latin)',    'script' => 'Latn', 'native' => 'azərbaycanca', 'flag_code' => '' ],
        
    ],

    'middleware' => [
    'auth',
    'verified', // Asegúrate de que los usuarios hayan verificado su correo
    'filament',
],
















    /*
    |--------------------------------------------------------------------------
    | Cache Path
    |--------------------------------------------------------------------------
    |
    | This is the directory that Filament will use to store cache files that
    | are used to optimize the registration of components.
    |
    | After changing the path, you should run `php artisan filament:cache-components`.
    |
    */

    'cache_path' => base_path('bootstrap/cache/filament'),

    /*
    |--------------------------------------------------------------------------
    | Livewire Loading Delay
    |--------------------------------------------------------------------------
    |
    | This sets the delay before loading indicators appear.
    |
    | Setting this to 'none' makes indicators appear immediately, which can be
    | desirable for high-latency connections. Setting it to 'default' applies
    | Livewire's standard 200ms delay.
    |
    */

    'livewire_loading_delay' => 'default',

    //'locale' => 'es',

];
