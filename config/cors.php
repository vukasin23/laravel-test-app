<?php

return [

    'paths' => ['api/*', 'build/*'], // build/* je bitan zbog Vite fajlova

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'], // ili npr. ['https://tvoj-domen.com']

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
