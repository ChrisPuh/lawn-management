<?php

declare(strict_types=1);

return [

    'enabled' => env('COOKIE_CONSENT_ENABLED', true),

    'cookie_name' => 'lawn_management_cookie_consent',

    'cookie_lifetime' => 365 * 24 * 60,

    'texts' => [
        'message' => 'Diese Website verwendet Cookies für essentielle Funktionen und zur Verbesserung Ihrer Nutzererfahrung.',
        'agree' => 'Akzeptieren',
        'deny' => 'Ablehnen',
        'learn_more' => 'Details ansehen',
    ],
];
