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

        $juegoController = new juegoController();

        $datos_juego = $request->getParsedBody();

        $nombre = $datos_juego['nombre'];
        $descripcion = $datos_juego['descripcion'];
        $imagen = $datos_juego['imagen'];
        $clasificacion_edad = $datos_juego['clasificacion_edad'];

        $respuesta = $juegoController->agregarJuego($nombre, $descripcion, $imagen, $clasificacion_edad);

        $response->getBody()->write(json_encode($respuesta['result']));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);

    });

    // actualiza los datos de un juego existente. Solo lo puede hacer un usuario logueado y que sea administrador.
    $app->put('/juego/{id}', function(Request $request, Response $response){

        $juegoController = new juegoController();

        $juego_id = $request -> getAttribute('id');
        $datos_juego = $request->getParsedBody();

        $nombre = $datos_juego['nombre'];
        $descripcion = $datos_juego['descripcion'];
        $imagen = $datos_juego['imagen'];
        $clasificacion_edad = $datos_juego['clasificacion_edad'];

        $respuesta = $juegoController->editarJuego($juego_id, $nombre, $descripcion, $imagen, $clasificacion_edad);

        $response->getBody()->write(json_encode($respuesta['result']));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);
    });

    //  borra el juego siempre y cuando no tenga calificaciones. Solo lo puede hacer un usuario logueado y que sea administrador.
    $app->delete('/juego/{id}', function(Request $request, Response $response){

        $juegoController = new juegoController();

        $user_id = $request -> getAttribute('id');

        $respuesta = $juegoController->eliminarJuego($user_id);

        $response->getBody()->write(json_encode($respuesta['result']));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);

    });

    // Calificaciones ---------------------------------------------------------------------

    //  Crear una nueva calificación. Solo lo puede hacer unusuario logueado.
    $app->post('/calificacion', function(Request $request, Response $response){

        $calificacionController = new calificacionController();

        $datos_calificacion = $request->getParsedBody();

        $estrellas = $datos_calificacion['estrellas'];
        $id_usuario = $datos_calificacion['usuario_id'];
        $id_juego = $datos_calificacion['juego_id'];

        $respuesta = $calificacionController->createCalification($estrellas, $id_usuario, $id_juego);

        $response->getBody()->write(json_encode($respuesta['result']));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);

    });

    // Editar una calificación existente. Solo lo puede hacer un usuario logueado.
    $app->put('/calificacion/{id}', function(Request $request, Response $response){

        $calificacionController = new calificacionController();
        
        $calificacion_id = $request -> getAttribute('id');
        $datos_calificacion = $request->getParsedBody(5);

        $estrellas = $datos_calificacion['estrellas'];
        $id_usuario = $datos_calificacion['usuario_id'];
        $id_juego = $datos_calificacion['juego_id'];
        
        // put a la base

        $respuesta = $calificacionController->editCalificacion($calificacion_id, $estrellas, $id_usuario, $id_juego);

        $response->getBody()->write(json_encode($respuesta['result']));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);

    });

    // Eliminar una calificación. Solo lo puede hacer un usuario logueado.
    $app->delete('/calificacion/{id}', function(Request $request, Response $response){

        $calificacionController = new calificacionController();
        
        $calificacion_id = $request -> getAttribute('id');

        // delete a la base

        $respuesta = $calificacionController->deleteCalification($calificacion_id);

        $response->getBody()->write(json_encode($respuesta['result']));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);

    });
?>