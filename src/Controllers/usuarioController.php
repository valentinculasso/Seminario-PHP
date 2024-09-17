<?php //namespace ..\Controllers

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class usuarioController{

    public function getAll($request, $response, $arg){
        $response->getBody()->write("Hola Mundo");

        return $response;
    }

}

?>