<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Synchronisation automatique
    |--------------------------------------------------------------------------
    |
    | Synchroniser automatiquement le stock quand une variante est modifiée
    |
    */
    'auto_sync' => true,

    /*
    |--------------------------------------------------------------------------
    | Gestion des backorders
    |--------------------------------------------------------------------------
    |
    | Comment gérer les backorders dans le stock partagé
    |
    */
    'shared_backorder' => true,

    /*
    |--------------------------------------------------------------------------
    | Événements personnalisés
    |--------------------------------------------------------------------------
    |
    | Activer l'émission d'événements personnalisés pour le stock partagé
    |
    */
    'events' => [
        'stock_updated' => true,
    ],
];
