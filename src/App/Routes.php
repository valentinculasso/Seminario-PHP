<?php

use Slim\Psr7\Request;
use Slim\Psr7\Response;

require __DIR__ . "/../Controllers/usuarioController.php";

require __DIR__ . "/../Controllers/juegoController.php";

require __DIR__ . "/../Controllers/calificacionController.php";

// Usuarios

    $app->post('/register', function(Request $request, Response $response){

        $usuarioController = new usuarioController();

        $datos_usuario = $request->getParsedBody();

        // El enunciado me dice que se agrega un nuevo usuario solo con su nombre de usuario y clave

        // Nombre de usuario debe tener por lo menos 6 caracteres y no mas de 20 caracteres y que sean unicamente alfanumericos
        // ademas, se debe verificar que el nombre de usuario no este en uso
        $nombre = $datos_usuario['nombre_usuario'];
        // La clave debe tener por lo menos 8 caracteres y tiene que tener si o si mayusculas, minusculas, numeros y caracteres especiales
        $clave = $datos_usuario['clave'];
        
        $respuesta = $usuarioController-> register($nombre, $clave);
        $response->getBody()->write(json_encode($respuesta['result']));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);
    });

    $app->post('/login', function(Request $request, Response $Response){

        $usuarioController = new usuarioController();

        $datos_usuario = $request->getParsedBody();

        $nombre = $datos_usuario['nombre_usuario'];
        $clave = $datos_usuario['clave'];

        // En usuario controller podria tener una funcion que reciba el nombre y clave y que haga la verificacion (osea que coinciden los datos)
        // y en Models/usuario podria tener la generacion del token
        $respuesta = $usuarioController->login($nombre, $clave);


    });

    $app->get('/usuario/{id}',function(Request $request, Response $response){

        // Instancio la clase
        $usuarioController = new usuarioController();
        // Cargo en una variable user_id el id que me viene en el request
        $user_id = $request -> getAttribute('id');
        // Cargo en respuesta la tabla con los datos del usuario con id = user_id

        // CHEQUEAR SI EL USUARIO ESTA LOGEADO

        ///
        $respuesta = $usuarioController->getUser($user_id);
        // Genero un json con los datos del usuario
        $response->getBody()->write(json_encode($respuesta['result']));
        // Me retorna el status -> 200 OK (me trae el usuario "el cual existe") o 404 en caso de ser null (usuario inexistente)
        return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);


        // DUDAS: En los últimos 3 casos (donde se recibe el id) se debe validar que el
        // usuario se haya logueado.


    });

    $app->post('/usuario', function(Request $request, Response $response){

        $usuarioController = new usuarioController();

        $datos_usuario = $request->getParsedBody();

        $nombre = $datos_usuario['nombre_usuario'];
        $clave = $datos_usuario['clave'];
        $admin = $datos_usuario['es_admin'];

        // insert a la base
        $respuesta = $usuarioController->insertUser($nombre, $clave, $admin);

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

        // delete a la base
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

        // Instancio la clase
        $juegoController = new juegoController();
        // Cargo en una variable user_id el id que me viene en el request
        $user_id = $request -> getAttribute('id');
        // Cargo en respuesta la tabla con los datos del usuario con id = user_id
        $respuesta = $juegoController->getJuego($user_id);
        // Genero un json con los datos del usuario
        $response->getBody()->write(json_encode($respuesta['result']));
        // Me retorna el status -> 200 OK (me trae el usuario "el cual existe") o 404 en caso de ser null (usuario inexistente)
        return $response->withHeader('Content-Type', 'application/json')->withStatus($respuesta['status']);

    });

    // da de alta un nuevo juego. Solo lo puede hacer un usuario logueado y que sea administrador.
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