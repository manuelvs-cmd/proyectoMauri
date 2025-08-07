<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Optimizaciones de Caché
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'clientes_ttl' => env('CACHE_CLIENTES_TTL', 3600), // 1 hora
        'dashboard_ttl' => env('CACHE_DASHBOARD_TTL', 300), // 5 minutos
        'estadisticas_ttl' => env('CACHE_ESTADISTICAS_TTL', 1800), // 30 minutos
    ],

    /*
    |--------------------------------------------------------------------------
    | Paginación
    |--------------------------------------------------------------------------
    */
    'pagination' => [
        'clientes_per_page' => env('CLIENTES_PER_PAGE', 15),
        'pedidos_per_page' => env('PEDIDOS_PER_PAGE', 20),
        'facturas_per_page' => env('FACTURAS_PER_PAGE', 25),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de archivos
    |--------------------------------------------------------------------------
    */
    'files' => [
        'max_upload_size' => env('MAX_UPLOAD_SIZE', 5120), // 5MB en KB
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de notificaciones
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'email_enabled' => env('NOTIFICATIONS_EMAIL_ENABLED', true),
        'sms_enabled' => env('NOTIFICATIONS_SMS_ENABLED', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de reportes
    |--------------------------------------------------------------------------
    */
    'reports' => [
        'default_format' => env('REPORTS_DEFAULT_FORMAT', 'pdf'),
        'cache_reports' => env('CACHE_REPORTS', true),
        'reports_ttl' => env('REPORTS_TTL', 7200), // 2 horas
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limiting' => [
        'api_requests_per_minute' => env('API_RATE_LIMIT', 60),
        'login_attempts_per_minute' => env('LOGIN_RATE_LIMIT', 5),
    ],

];
