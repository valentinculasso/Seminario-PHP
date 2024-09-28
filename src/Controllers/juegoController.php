<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . "/../App/Functions.php";

class juegoController {

    // getJuego($id): Obtiene la informacion de un juego especifico y su listado de calificaciones. Recibo como parametro el ID del juego.
    public function getJuego($id){
        try{
            $connection = conectarbd();
            $sql = "SELECT * FROM `juego` WHERE id = $id";
            $result = mysqli_query($connection, $sql);
            $response = mysqli_fetch_array($result);
            if(!$response){
                $respuesta = ['status'=> 404, 'result'=>"ID del usuario inexistente"];
            }
            else{
                $respuesta = ['status'=>200, 'result'=>$response];
            }
            $connection = desconectarbd($connection);
        }
        catch(Exception $e){
            $respuesta = ['status'=>500, 'result'=> $e->getMessage()];
        }
        return $respuesta;
    }

    // agregarJuego(): agrega un nuevo juego a la base de datos. Recibe como parametros los campos del juego.
    public function agregarJuego(){

    }

    // editarJuego($id): actualiza los datos de un juego existente. Recibe como parametros el id del juego
    public function editarJuego($id){

    }

    // eliminarJuego($id): elimina un juego de la base de datos. Recibe como parametro el id del juego
    public function eliminarJuego($id){
        
    }
}
?>