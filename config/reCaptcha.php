<?php

return [
    'key'    => env('GOOGLE_RECAPTCHA_KEY'),
    'secret' => env('GOOGLE_RECAPTCHA_SECRET'),
    'attributes' => [
        'data-theme'             => 'light',     // light, dark
        'data-size'              => 'normal',    // normal, compact
        'data-type'              => 'image',     // aduio, image
        'data-tabindex'          => 0,
        'data-callback'          => null,
        'data-expired-callback'  => null,
    ]
];