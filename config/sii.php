<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Configuración del SII Chile
    |--------------------------------------------------------------------------
    |
    | Configuración para la integración con el Servicio de Impuestos Internos
    | de Chile para el envío de documentos tributarios electrónicos.
    | Implementación moderna compatible con PHP 8.2+
    |
    */

    'ambiente' => env('SII_AMBIENTE', 'certificacion'), // 'certificacion' o 'produccion'
    
    'urls' => [
        'certificacion' => [
            'base' => 'https://maullin.sii.cl/DTEWS/',
            'upload' => 'https://palena.sii.cl/cgi_dte/UPL/DTEUpload',
            'token' => 'https://palena.sii.cl/cgi_dte/UPL/GetTokenFromSeed.cgi',
            'seed' => 'https://palena.sii.cl/cgi_dte/UPL/GetSeed.cgi',
            'query_estado' => 'https://maullin.sii.cl/DTEWS/QueryEstDte.jws',
            'query_envio' => 'https://maullin.sii.cl/DTEWS/QueryEstUp.jws',
        ],
        'produccion' => [
            'base' => 'https://sii.cl/DTEWS/',
            'upload' => 'https://sii.cl/cgi_dte/UPL/DTEUpload',
            'token' => 'https://sii.cl/cgi_dte/UPL/GetTokenFromSeed.cgi',
            'seed' => 'https://sii.cl/cgi_dte/UPL/GetSeed.cgi',
            'query_estado' => 'https://sii.cl/DTEWS/QueryEstDte.jws',
            'query_envio' => 'https://sii.cl/DTEWS/QueryEstUp.jws',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Certificados Digitales
    |--------------------------------------------------------------------------
    |
    | Configuración de certificados digitales para la firma electrónica
    |
    */

    'certificado' => [
        'ruta' => env('SII_CERTIFICADO_PATH', storage_path('certificates/certificado.p12')),
        'password' => env('SII_CERTIFICADO_PASSWORD', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Datos del Emisor
    |--------------------------------------------------------------------------
    |
    | Información del emisor de los documentos tributarios
    |
    */

    'emisor' => [
        'rut' => env('SII_EMISOR_RUT', ''),
        'razon_social' => env('SII_EMISOR_RAZON_SOCIAL', ''),
        'giro' => env('SII_EMISOR_GIRO', ''),
        'direccion' => env('SII_EMISOR_DIRECCION', ''),
        'comuna' => env('SII_EMISOR_COMUNA', ''),
        'ciudad' => env('SII_EMISOR_CIUDAD', ''),
        'telefono' => env('SII_EMISOR_TELEFONO', ''),
        'email' => env('SII_EMISOR_EMAIL', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Documentos
    |--------------------------------------------------------------------------
    |
    | Tipos de documentos y sus códigos según SII
    |
    */

    'tipos_documento' => [
        'factura' => [
            'codigo' => 33,
            'nombre' => 'Factura Electrónica',
        ],
        'boleta' => [
            'codigo' => 39,
            'nombre' => 'Boleta Electrónica',
        ],
        'nota_credito' => [
            'codigo' => 61,
            'nombre' => 'Nota de Crédito Electrónica',
        ],
        'nota_debito' => [
            'codigo' => 56,
            'nombre' => 'Nota de Débito Electrónica',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Folios
    |--------------------------------------------------------------------------
    |
    | Configuración para el manejo de folios autorizados por el SII
    |
    */

    'folios' => [
        'ruta_caf' => storage_path('folios/'),
        'backup_enviados' => storage_path('dte_enviados/'),
        'backup_respuestas' => storage_path('sii_respuestas/'),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Configuración de Firma Digital
    |--------------------------------------------------------------------------
    |
    | Configuración para la firma digital de documentos
    |
    */
    
    'firma' => [
        'algoritmo' => 'sha256',
        'validar_certificado' => env('SII_VALIDAR_CERTIFICADO', true),
        'incluir_timestamp' => env('SII_INCLUIR_TIMESTAMP', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Logs
    |--------------------------------------------------------------------------
    |
    | Configuración para el registro de logs de las transacciones con el SII
    |
    */

    'logs' => [
        'habilitado' => env('SII_LOGS_ENABLED', true),
        'nivel' => env('SII_LOG_LEVEL', 'info'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Timeouts y Reintentos
    |--------------------------------------------------------------------------
    |
    | Configuración de timeouts y reintentos para las conexiones al SII
    |
    */

    'timeout' => env('SII_TIMEOUT', 30),
    'max_reintentos' => env('SII_MAX_REINTENTOS', 3),
    'delay_entre_reintentos' => env('SII_DELAY_REINTENTOS', 2), // segundos

];
