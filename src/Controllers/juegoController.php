<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . "/../App/Functions.php";

class juegoController {

    public function getJuego($id){
        try{
            // Me conecto a la base de datos
            $connection = conectarbd();
            // En $sql genero la consulta SQL
            $sql = "SELECT * FROM `juego` WHERE id = $id";
            // Envia la consulta a la base de datos
            $result = mysqli_query($connection, $sql);
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
            $connection = desconectarbd($connection);
        }
        catch(Exception $e){
            // Si por ejemplo no me pude conectar a la base de datos envio un status 500
            $respuesta = ['status'=>500, 'result'=> $e->getMessage()];
        }
        // retorno $respuesta -> la cual puede ser un status 200 OK , 404 not found o 500
        return $respuesta;
    }
}
?>