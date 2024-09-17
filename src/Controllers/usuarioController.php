<?php //namespace

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . "/../App/Functions.php";

class usuarioController {

    public function getUser($id){
        try{

            $conn = conectarbd();
        
            $sql = "SELECT * FROM `juego` WHERE id = $id";
        
            $result = mysqli_query($conn, $sql);
        
            $response = mysqli_fetch_array($result);

            if(!$response){

                $respuesta = ['status'=> 404, 'result'=>"ID del usuario inexistente"];

            }
            else{

                $respuesta = ['status'=>200, 'result'=>$response];

            }
            $conn = desconectarbd($conn);
        }
        catch(Exception $e){

            $respuesta = ['status'=>500, 'result'=> $e->getMessage()];

        }


        return $respuesta;

    }

}

?>