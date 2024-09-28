<?php

use Slim\Psr7\Request;
use Slim\Psr7\Response;

require_once __DIR__ . "/../Controllers/usuarioController.php";

require_once __DIR__ . "/../Controllers/juegoController.php";

require_once __DIR__ . "/../Controllers/calificacionController.php";

    $app->post('/register', function(Request $request, Response $response){

        $userController = new usuarioController();

        $datos_usuario = $request->getParsedBody();

        $nombre = $datos_usuario['nombre_usuario'];
        $clave = $datos_usuario['clave'];
        
        $respuesta = $userController-> register($nombre, $clave);
        $response->getBody()->write(json_encode($respuesta['result']));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);
    });

    $app->post('/login', function(Request $request, Response $response){

        $usuarioController = new usuarioController();

        $datos_usuario = $request->getParsedBody();

        $nombre = $datos_usuario['nombre_usuario'];
        $clave = $datos_usuario['clave'];

        $respuesta = $usuarioController->login($nombre, $clave);

        $response->getBody()->write(json_encode($respuesta['result']));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);
    });

    // Usuarios

    $app->get('/usuario/{id}',function(Request $request, Response $response){

        $usuarioController = new usuarioController();
        $user_id = $request -> getAttribute('id');

        $respuesta = $usuarioController->getUser($user_id);

        $response->getBody()->write(json_encode($respuesta['result']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);
    });

    $app->post('/usuario', function(Request $request, Response $response){

        $usuarioController = new usuarioController();

        $datos_usuario = $request->getParsedBody();

        $nombre = $datos_usuario['nombre_usuario'];
        $clave = $datos_usuario['clave'];
        $admin = $datos_usuario['es_admin'];

        $respuesta = $usuarioController->createUser($nombre, $clave, $admin);

        $response->getBody()->write(json_encode($respuesta['result']));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);
    });

    $app->put('/usuario/{id}', function(Request $request, Response $response){

        $user_id = $request -> getAttribute('id');

        // put a la base -> aca tengo una duda, osea yo recibo el ID y a ese usuario lo voy a modificar. Pero en si como se que le tengo que modificar? 
        // ya viene en el request o como es? Porque en el caso del delete recibo id y elimino el usuario con dicho id y listo

    });

    $app->delete('/usuario/{id}', function(Request $request, Response $response){

        $usuarioController = new usuarioController();

        $user_id = $request -> getAttribute('id');

        $respuesta = $usuarioController->deleteUser($user_id);

        $response->getBody()->write(json_encode($respuesta['result']));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);
    });

    // Juegos -----------------------------------------------------------------------------

    //Listar los juegos de la página según los parámetrosde búsqueda incluyendo la puntuación promedio del juego.
    $app->get('/juegos?pagina={pagina}&clasificacion={clasificacion}&texto={texto}&plataforma={plataforma}', function(Request $request, Response $response){

        $datos_usuario = $request->getParsedBody(); 

    });

    $app->get('/juegos/{id}',function(Request $request, Response $response){

        $juegoController = new juegoController();

        $user_id = $request -> getAttribute('id');

        $respuesta = $juegoController->getJuego($user_id);

        $response->getBody()->write(json_encode($respuesta['result']));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);
    });

    // Da de alta un nuevo juego. Solo lo puede hacer un usuario logueado y que sea administrador.
    $app->post('/juego', function(Request $request, Response $response){

        $datos_usuario = $request->getParsedBody();

        $nombre = $datos_usuario['nombre_usuario']; // Bueno aca irian los campos de juego
        $clave = $datos_usuario['clave'];
        $admin = $datos_usuario['es_admin'];

        // insert a la base

    });

    // actualiza los datos de un juego existente. Solo lo puede hacer un usuario logueado y que sea administrador.
    $app->put('/juego/{id}', function(Request $request, Response $response){


    });

    //  borra el juego siempre y cuando no tenga calificaciones. Solo lo puede hacer un usuario logueado y que sea administrador.
    $app->delete('/juego/{id}', function(Request $request, Response $response){

        $user_id = $request -> getAttribute('id');

        // delete a la base

    });

    // Calificaciones ---------------------------------------------------------------------

    //  Crear una nueva calificación. Solo lo puede hacer unusuario logueado.
    $app->post('/calificacion', function(Request $request, Response $response){

        $datos_usuario = $request->getParsedBody();

        // insert a la base

    });

    /* 
    PUT /calificacion/{id}: Editar una calificación existente. Solo lo puede hacer un usuario logueado.
        ○ Éxito: Código 200 OK, se actualiza el valor de puntuación del juego
        solo si el token coincide con el almacenado para ese usuario y el token no está vencido.
        ○ Fallo: Código de estado 401 Unauthorized, con un mensaje de error.
    */
    $app->put('/calificacion/{id}', function(Request $request, Response $response){

        $user_id = $request -> getAttribute('id');

        // put a la base

    });

    // Eliminar una calificación. Solo lo puede hacer un usuario logueado.
    $app->delete('/calificacion/{id}', function(Request $request, Response $response){

        $user_id = $request -> getAttribute('id');

        // delete a la base

    });
?>