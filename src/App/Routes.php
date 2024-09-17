<?php 

    use Slim\Routing\RouteCollectorProxy; // Permite usar para definir nuestras rutas en grupos

    $app->group('/api',function(RouteCollectorProxy $group){
        $group->get('/usuarios','App\Controllers\usuarioController:getAll');
    });



?>