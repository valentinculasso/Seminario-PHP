<?php

use Slim\Psr7\Request;
use Slim\Psr7\Response;

require __DIR__ . "/../Controllers/usuarioController.php";

// Usuarios

    $app->get('/usuario/{id}',function(Request $request, Response $response){

        // Instancio la clase
        $usuarioController = new usuarioController();
        // Cargo en una variable user_id el id que me viene en el request
        $user_id = $request -> getAttribute('id');
        // Cargo en respuesta la tabla con los datos del usuario con id = user_id
        $respuesta = $usuarioController->getUser($user_id);
        // Genero un json con los datos del usuario
        $response->getBody()->write(json_encode($respuesta['result']));
        // Me retorna el status -> 200 OK (me trae el usuario "el cual existe") o 404 en caso de ser null (usuario inexistente)
        return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);

    });

    $app->post('/usuario', function(Request $request, Response $response){

        $datos_usuario = $request->getParsedBody();

        $nombre = $datos_usuario['nombre_usuario'];
        $clave = $datos_usuario['clave'];
        $admin = $datos_usuario['es_admin'];

        // insert a la base

    });

?>