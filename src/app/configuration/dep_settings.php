<?php
return [
    'settings' => [
        'displayErrorDetails' => (($_SERVER['MODE']=="development") ? true : false), // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/hotelmap/views/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        'db' => [
            "development" => [
                "host" => "mysql-primarydb.prod.hotelmap.com",
                "user" => "worker_dev",
                "pass" => "AA4sHBJhQDYF73V",
                "dbname" => "HotelMapWorldWide"
            ],
            "production" => [
                "host" => "mysql-primarydb.prod.hotelmap.com",
                "user" => "worker",
                "pass" => "8lMGMyf0sFeeoIuJvQetEsaVomFk3xbC",
                "dbname" => "HotelMapWorldWide"
            ]
        ]
    ],
];
