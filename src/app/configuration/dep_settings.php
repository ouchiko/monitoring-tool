<?php
return [
    'settings' => [
        'displayErrorDetails' => (($_SERVER['MODE']=="development") ? true : false), // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../trains/views/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        'db' => [
            "default" => [
                "host" => "train-mysql",
                "user" => "trains",
                "pass" => "trains",
                "dbname" => "trains"
            ]
        ]
    ],
];
