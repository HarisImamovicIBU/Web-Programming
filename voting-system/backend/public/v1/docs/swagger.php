<?php

require __DIR__ . '/../../../vendor/autoload.php';

if($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1'){
    define('BASE_URL', 'http://localhost:80/Web-Programming/voting-system/backend/');
} else {
    define('BASE_URL', 'https://lionfish-app-s3jmy.ondigitalocean.app/');
}

$openapi = \OpenApi\Generator::scan([
    __DIR__ . '/doc_setup.php',
    __DIR__ . '/../../../rest/routes'
]);
header('Content-Type: application/json');
echo $openapi->toJson();
?>