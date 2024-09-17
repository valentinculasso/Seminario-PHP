<?php //namespace

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class usuarioController{

    public function getUser($id){
        return ['status'=>200, 'result'=>'pablo'];
    }

}

?>