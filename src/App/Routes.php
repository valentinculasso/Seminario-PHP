<?php

use Slim\Psr7\Request;
use Slim\Psr7\Response;

require __DIR__ . "/../Controllers/usuarioController.php";

// Usuarios

    $app->get('/usuario/{id}',function(Request $request, Response $response){

        $usuarioController = new usuarioController();

        $user_id = $request -> getAttribute('id');
        
        $respuesta = $usuarioController->getUser($user_id);

        $response->getBody()->write(json_encode($respuesta));

        $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);

        return $response;
    });



?>