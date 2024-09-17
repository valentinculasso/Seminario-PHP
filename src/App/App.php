<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/../../vendor/autoload.php';

$app = AppFactory::create();

// PRACTICA 1 - ACLARACIONES AGREGAR: 

//    $app->addBodyParsingMiddleware();

//    $data = $request->getParsedBody();

// -----

require __DIR__ . "/Routes.php"; // No me toma el archivo Routes.php por lo que me tira error

$app->run();


?>