<?php

return [

    'facebook' => [
        'client_id' => '1069289513084347',
        'client_secret' => '2ab6a7f87b0b94adc01ee77a46a86c02',
        'redirect' => 'http://gemini-web.bunduc.ro/profiles/callback?source=facebook',
        'default_graph_version' => 'v2.5'
    ],
    'twitter' => [
        'client_id' => 'e1KZBWrZZWbW90Im51M719wkG',
        'client_secret' => 'DStLye5Labol4kfg6Mdfm1RLqHF1YXuKIeMq8pIKYxfyFgQbEa',
        'redirect' => 'http://gemini-web.bunduc.ro/profiles/callback?source=twitter',
    ],
    'google' => [
        'client_id' => '557224582093-lg3e54jmaqa1m6ndkobn3m91799rpvjv.apps.googleusercontent.com',
        'client_secret' => 'XuOYXJrPm9dnQsrnQ71YjD9t',
        'redirect' => 'http://gemini-web.bunduc.ro/profiles/callback?source=google',
    ],
    'linkedin' => [
        'client_id' => '77rdsw995q58wd',
        'client_secret' => 'OZE1BbLbJUdJ5oBx',
        'redirect' => 'http://gemini-web.bunduc.ro/profiles/callback?source=linkedin',
    ],


    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'mandrill' => [
        'secret' => env('MANDRILL_SECRET'),
    ],

    'ses' => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'stripe' => [
        'model'  => App\User::class,
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

];
