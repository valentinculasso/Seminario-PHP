<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . "/../App/Functions.php";

class usuarioController {

    public function getUser($id){
        try{
            // Me conecto a la base de datos
            $conn = conectarbd();
            // En $sql genero la consulta SQL
            $sql = "SELECT * FROM `usuario` WHERE id = $id";
            // Envia la consulta a la base de datos
            $result = mysqli_query($conn, $sql);
            // Si no me equivoco me convierte el $result en un array con los datos del usuario
            $response = mysqli_fetch_array($result);
            // Si $response es null entonces envio un status 404
            if(!$response){
                $respuesta = ['status'=> 404, 'result'=>"ID del usuario inexistente"];
            }
            // Si no $respuesta es valida y envio un status 200 OK y en result el array con los datos del usuario ($response)
            else{
                $respuesta = ['status'=>200, 'result'=>$response];
            }
            // Una vez terminado me desconecto de la base de datos
            $conn = desconectarbd($conn);
        }
        catch(Exception $e){
            // Si por ejemplo no me pude conectar a la base de datos envio un status 500
            $respuesta = ['status'=>500, 'result'=> $e->getMessage()];
        }
        // retorno $respuesta -> la cual puede ser un status 200 OK , 404 not found o 500
        return $respuesta;
    }

    public function insertUser($nombre, $clave, $admin){
        try{
            $conn = conectarbd();

            $sql = "INSERT INTO `usuario`(`nombre_usuario`, `clave`, `es_admin`) VALUES ('$nombre', '$clave', '$admin')";

            $response = mysqli_query($conn, $sql);

            if(!$response){
                $respuesta =  ['status'=> 401, 'result'=>"No se ha creado un nuevo usuario"];
            }
            else{
                $respuesta = ['status'=>200, 'result'=>"Se ha creado un nuevo usario"];
            }

            $conn = desconectarbd($conn);
        }
        catch(Exception $e){
            // Si por ejemplo no me pude conectar a la base de datos envio un status 500
            $respuesta = ['status'=>500, 'result'=> $e->getMessage()];
        }

        return $respuesta;
    }

    public function deleteUser($id){
        try{
            $conn = conectarbd();
            // consulta SQL
            $sql = "DELETE FROM `usuario` WHERE id = $id";
            // Envia la consulta a la base de datos
            $response = mysqli_query($conn, $sql);

            if(!$response){
                // error 400 seria por valores invalidos (id), etc
                $respuesta =  ['status'=> 400, 'result'=>"No se ha eliminado el usuario"];
            }
            else{
                $respuesta = ['status'=>200, 'result'=>"Se ha eliminado correctamente el usario"];
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