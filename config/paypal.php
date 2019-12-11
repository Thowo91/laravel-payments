<?php
return [
    /**
     * Sandbox und Live credentials
     */
    'credentials' => [
        'sandbox' => [
            'client_id' => env('PAYPAL_SANDBOX_CLIENT_ID', ''),
            'secret' => env('PAYPAL_SANDBOX_SECRET', '')
        ],
    ],
    /**
     * SDK Konfiguration
     */
    'settings' => [
        /**
         * Payment Mode
         *
         * Optionen: 'sandbox' oder 'live'
         */
        'mode' => env('PAYPAL_MODE', 'sandbox'),

        // Angabe in Sekunden
        'http.ConnectionTimeOut' => 30,
        'log.LogEnabled' => true,
        'log.FileName' => storage_path() . '/logs/paypal.log',

        /**
         * Log Level
         *
         * Optionen: 'DEBUG', 'INFO', 'WARN' oder 'ERROR'
         */
        'log.LogLevel' => 'ERROR'
    ],
];
