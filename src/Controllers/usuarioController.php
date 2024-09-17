<?php //namespace

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . "/../App/Functions.php";

class usuarioController {

    public function getUser($id){
    
            $conn = conectarbd();
        
            $sql = "SELECT * FROM `juego` WHERE id = $id";
        
            $result = mysqli_query($conn, $sql);
        
            $variable = mysqli_fetch_array($result);
        
            $response = $variable["nombre"];
        
            // $response->getBody()->write($variable["nombre"]);

            $conn = desconectarbd($conn);

            return $response;
    }

}

?>