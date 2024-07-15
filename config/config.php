<?php


return [
    'database' => [
        "host" => "host.docker.internal",
        "port" => 3306,
        "dbname" => "groupup",
        "charset" => "utf8mb4",
        "username" => "groupup",
        "password" => "secret",
        'driver' => "pdo_mysql",
    ],
    'weather' => [
        "url" => "https://api.open-meteo.com/v1/forecast",
        "query" => [
            "hourly" => "temperature_2m,rain,snowfall",
            "forecast_days" => "16"
        ]
    ]
];