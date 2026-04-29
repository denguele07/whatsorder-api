<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour Sanctum SPA + Vue 3 front.
    |
    */

    // Routes concernées par CORS
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login',
        'logout',
    ],

    // Méthodes HTTP autorisées
    'allowed_methods' => ['*'],

    // Domaines autorisés à appeler l'API
    'allowed_origins' => [
    'https://whatsorder-web-two.vercel.app',
    'http://127.0.0.1:5173',
    'http://localhost:5173',
    env('FRONTEND_URL', 'http://localhost:5173'),
],

    // Patterns d'origines (regex). Vide pour nous.
    'allowed_origins_patterns' => [],

    // Headers autorisés en entrée
    'allowed_headers' => ['*'],

    // Headers exposés au front
    'exposed_headers' => [],

    // Durée du cache du preflight (en secondes)
    'max_age' => 0,

    // CRITIQUE pour Sanctum SPA : autoriser l'envoi des cookies
    'supports_credentials' => false,
];
