<?php

use Slim\Psr7\Request;
use Slim\Psr7\Response;

require_once __DIR__ . "/../Controllers/usuarioController.php";

require_once __DIR__ . "/../Controllers/juegoController.php";

require_once __DIR__ . "/../Controllers/calificacionController.php";

    $authMiddleware = function($request, $handler){
        $response = new Response(); 
        $authHeader = $request->getHeader('Authorization');
        if(!$authHeader){
            $response->getBody()->write(json_encode(['error'=>'token no proporcionado']));
            return $response->withStatus(401);
        }
        // Extraer el token en base64
        $tokenBase64 = str_replace('Bearer ', '', $authHeader[0]);
        // Decodificar token
        $tokenDecoded = base64_decode($tokenBase64);
        // verifico si la decodificacion fue exitosa
        if(!$tokenDecoded){
            $response->getBody()->write(json_encode(['error'=>'token invalido']));
            return $response->withStatus(401);
        }

        // echo $tokenDecoded;
        $token = json_decode($tokenDecoded);

        // aca si imprimo, no imprime nada imprime vacio por lo que la decodificacion falla
        if(!$token || !isset($token->id) || !isset($token->date)){
            $response->getBody()->write(json_encode(['error'=>'Formato del token invalido']));
            return $response->withStatus(401);
        }
        // verifico si el token expiro
        try{
            $tokenDate = new DateTime($token->date);
            $currentDate = new DateTime();
            $tokenDate->modify('+1 hour');
            // verifico si expiro
            if($currentDate > $tokenDate){
                $response->getBody()->write(json_encode(['error'=>'token expirado']));
                return $response->withStatus(401);
            }
            // si el token es valido, se agrega el ID del usuario al request para que este disponible en los endpoints
            $request = $request->withAttribute('es_admin', $token->admin) // en el endpoint accedo al admin con: $admin = $request->getAttribute('es_admin');
                               ->withAttribute('usuario_id', $token->id); // en el endpoint accedo al usuario id con: $user_id = $request->getAttribute('usuario_id');
            return $handler->handle($request);
        }
        catch(Exception $e){
            $response->getBody()->write(json_encode(['error'=>'Error al validar el token']));
            return $response->withStatus(500);
        }
    };

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

    $app->post('/usuario', function(Request $request, Response $response){

        $usuarioController = new usuarioController();

        $admin = $request->getAttribute('es_admin');
        
        $datos_usuario = $request->getParsedBody();

        $nombre = $datos_usuario['nombre_usuario'];
        $clave = $datos_usuario['clave'];
        $admin = $datos_usuario['es_admin'];

        $respuesta = $usuarioController->createUser($nombre, $clave, $admin);

        $response->getBody()->write(json_encode($respuesta['result']));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);
    });

    $app->get('/usuario/{id}',function(Request $request, Response $response){

        $usuarioController = new usuarioController();
        $user_id = $request -> getAttribute('id');

        $respuesta = $usuarioController->getUser($user_id);

        $response->getBody()->write(json_encode($respuesta['result']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);
    })->add($authMiddleware);

    $app->put('/usuario/{id}', function(Request $request, Response $response){

        $usuarioController = new usuarioController();

        $user_id_logeado = $request -> getAttribute('usuario_id');
        $user_id = $request -> getAttribute('id');

        $datos_usuario = $request->getParsedBody();
        $nombre = $datos_usuario['nombre_usuario'];
        $clave = $datos_usuario['clave'];
        $admin = $datos_usuario['es_admin'];

        if($user_id_logeado == $user_id){
            $respuesta = $usuarioController->editUser($user_id, $nombre, $clave, $admin);

            $response->getBody()->write(json_encode($respuesta['result']));

            return $response->withHeader('Content-type', 'application/json')->withStatus($respuesta['status']);
        }
        else{
            $response->getBody()->write(json_encode(['error'=>'No puede editar a otro usuario!']));
            return $response->withStatus(401);
        }

    })->add($authMiddleware);

    $app->delete('/usuario/{id}', function(Request $request, Response $response){

        $usuarioController = new usuarioController();

        $user_id_logeado = $request ->getAttribute('usuario_id');

        $user_id = $request -> getAttribute('id');

        // CONSULTAR SI ESTA BIEN HACER EL IF, Y CONSULTAR SI ESTA BIEN LA RESPUESTA
        if($user_id_logeado == $user_id){
            $respuesta = $usuarioController->deleteUser($user_id);
        
            $response->getBody()->write(json_encode($respuesta['result']));

            return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);
        }
        else{
            $response->getBody()->write(json_encode(['error'=>'No puede eliminarse al usuario porque el id del usuario a eliminar no coincide con el id del usuario logeado']));
            return $response->withStatus(409);
        }

    })->add($authMiddleware);

    // Juegos -----------------------------------------------------------------------------

    //Listar los juegos de la página según los parámetrosde búsqueda incluyendo la puntuación promedio del juego.
    $app->get('/juegos', function(Request $request, Response $response){

        $juegoController = new juegoController();

        $datos = $request->getQueryParams();

        $parametros = [
            'pagina' => null,
            'clasificacion' => null,
            'texto' => null,
            'plataforma' => null
        ];

        foreach ($datos as $clave => $value) {
            if (array_key_exists($clave, $parametros)) {
                $parametros[$clave] = $value;
            }
        }

        $respuesta = $juegoController->getPagina(
            $parametros['pagina'],
            $parametros['clasificacion'],
            $parametros['texto'],
            $parametros['plataforma']
        );

        $response->getBody()->write(json_encode($respuesta['result']));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);

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

        $admin = $request->getAttribute('es_admin');
        if($admin){
            $datos_juego = $request->getParsedBody();

            $nombre = $datos_juego['nombre'];
            $descripcion = $datos_juego['descripcion'];
            $imagen = $datos_juego['imagen'];
            $clasificacion_edad = $datos_juego['clasificacion_edad'];

            $respuesta = $juegoController->agregarJuego($nombre, $descripcion, $imagen, $clasificacion_edad);

            $response->getBody()->write(json_encode($respuesta['result']));

            return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);
        }
        else{
            $response->getBody()->write(json_encode(['error'=>'token expirado']));
            return $response->withStatus(401);
        }

    })->add($authMiddleware);

    // actualiza los datos de un juego existente. Solo lo puede hacer un usuario logueado y que sea administrador.
    $app->put('/juego/{id}', function(Request $request, Response $response){

        $juegoController = new juegoController();

        $admin = $request->getAttribute('es_admin');
        if($admin){
            $datos_juego = $request->getParsedBody();

            $juego_id = $request -> getAttribute('id');
            $nombre = $datos_juego['nombre'];
            $descripcion = $datos_juego['descripcion'];
            $imagen = $datos_juego['imagen'];
            $clasificacion_edad = $datos_juego['clasificacion_edad'];

            $respuesta = $juegoController->editarJuego($juego_id, $nombre, $descripcion, $imagen, $clasificacion_edad);

            $response->getBody()->write(json_encode($respuesta['result']));

            return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);
        }
        else{
            $response->getBody()->write(json_encode(['error'=>'token expirado']));
            return $response->withStatus(401);
        }
    })->add($authMiddleware);

    //  borra el juego siempre y cuando no tenga calificaciones. Solo lo puede hacer un usuario logueado y que sea administrador.
    $app->delete('/juego/{id}', function(Request $request, Response $response){

        $juegoController = new juegoController();

        $admin = $request->getAttribute('es_admin');
        if($admin){
            $user_id = $request -> getAttribute('id');

            $respuesta = $juegoController->eliminarJuego($user_id);

            $response->getBody()->write(json_encode($respuesta['result']));

            return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);
        }
        else{
            $response->getBody()->write(json_encode(['error'=>'token expirado']));
            return $response->withStatus(401);
        }

    })->add($authMiddleware);

    // Calificaciones ---------------------------------------------------------------------

    //  Crear una nueva calificación. Solo lo puede hacer unusuario logueado.
    $app->post('/calificacion', function(Request $request, Response $response){

        $calificacionController = new calificacionController();

        $user_id = $request->getAttribute('usuario_id'); // ID del usuario logeado

        $datos_calificacion = $request->getParsedBody();

        $estrellas = $datos_calificacion['estrellas'];
        $id_juego = $datos_calificacion['juego_id'];

        $respuesta = $calificacionController->createCalification($estrellas, $user_id, $id_juego);

        $response->getBody()->write(json_encode($respuesta['result']));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);

    })->add($authMiddleware);

    // Editar una calificación existente. Solo lo puede hacer un usuario logueado.
    $app->put('/calificacion/{id}', function(Request $request, Response $response){

        $calificacionController = new calificacionController();
        
        $user_id = $request->getAttribute('usuario_id'); // ID del usuario logeado

        $calificacion_id = $request -> getAttribute('id');

        $datos_calificacion = $request->getParsedBody();

        $estrellas = $datos_calificacion['estrellas'];
        $id_juego = $datos_calificacion['juego_id'];

        $respuesta = $calificacionController->editCalificacion($calificacion_id, $estrellas, $id_juego, $user_id);

        $response->getBody()->write(json_encode($respuesta['result']));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);

    })->add($authMiddleware);

    // Eliminar una calificación. Solo lo puede hacer un usuario logueado.
    $app->delete('/calificacion/{id}', function(Request $request, Response $response){

        $calificacionController = new calificacionController();

        $user_id = $request->getAttribute('usuario_id');
        
        $calificacion_id = $request -> getAttribute('id');

        // delete a la base

        $respuesta = $calificacionController->deleteCalification($calificacion_id, $user_id);

        $response->getBody()->write(json_encode($respuesta['result']));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);

    })->add($authMiddleware);
?>