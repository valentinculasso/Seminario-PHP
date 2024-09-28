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
    /* En el alta y modificación de un juego se debe verificar que el nombre del juego no
    tenga más de 45 caracteres y que la clasificación por edad sea una de las tres
    válidas ('ATP', '+13', '+18'). Además, se debe convertir la imagen a base 64 para
    almacenarla en la base de datos.
    */
    public function agregarJuego($nombre_juego, $descripcion, $imagen, $clasificacion_edad){
        try{
            $conn = conectarbd();
            // Aca debo agregar el chequeo de si el usuario esta logeado
            // Luego debo chequear si es administrador
            if(strlen($nombre_juego) < 45){
                if($clasificacion_edad = "ATP" || $clasificacion_edad = "+13" || $clasificacion_edad = "+18"){
                    $sql = "INSERT INTO `juego` (`nombre`, `descripcion`, `imagen`, `clasificacion_edad`) VALUES ('$nombre_juego', '$descripcion', '$imagen', '$clasificacion_edad')";
                    $response = mysqli_query($conn, $sql);
                    if(!$response){
                        $respuesta = ['status'=> 404, 'result'=>"El juego no se ha añadido"];
                    }
                    else{
                        $respuesta = ['status'=> 200, 'result'=>"El juego se ha agregado con exito"];
                    }
                }
            }
            $conn = desconectarbd($conn);
        }
        catch(Exception $e){
            $respuesta = ['status'=>500, 'result'=> $e->getMessage()];
        }
        return $respuesta;
    }

    // editarJuego($id): actualiza los datos de un juego existente. Recibe como parametros el id del juego
    public function editarJuego($id, $nombre, $descripcion, $imagen, $clasificacion_edad){
        try{
            $conn = conectarbd();
            $sql = "SELECT * FROM `usuario` WHERE id = $id"; // EL PROBLEMA ACA ES QUE ESTOY BUSCANDO EN LA TABLA USUARIOS CON EL ID DEL JUEGO OSEA CAIDO
            $response = mysqli_query($conn, $sql);
            $user = $response->fetch_assoc();
            // $admin = $user['es_admin'];
            $admin = 1; // Le asigno true para probar (pd: funciona)
            if($admin){
                // Chequeo que el usuario se encuentre logeado
                // $log = verificarLogin($id);
                $log = 1; // Le asigno true para probar (pd: funciona)
                if($log){
                    $sql = "UPDATE `juego` SET nombre = '$nombre', descripcion = '$descripcion', imagen = '$imagen', clasificacion_edad = '$clasificacion_edad' WHERE id = '$id'";
                    $response = mysqli_query($conn, $sql);
                    if(!$response){
                        $respuesta =  ['status'=> 404, 'result'=>"El juego no existe"];
                    }
                    else{
                        $respuesta = ['status'=>200, 'result'=>"Se ha editado el juego correctamente"];
                    }
                }
                else{
                    $respuesta = ['status'=> 401, 'result'=>"El usuario no se encuentra logeado"];
                }
            }
            else{
                $respuesta = ['status'=> 401, 'result'=>"El usuario no es administrador"];
            }
            $conn = desconectarbd($conn);
        }
        catch(Exception $e){
            $respuesta = ['status'=>500, 'result'=> $e->getMessage()];
        }
        return $respuesta;
    }

    // eliminarJuego($id): elimina un juego de la base de datos. Recibe como parametro el id del juego
    // PREGUNTAS: 
    //              ¿Como se que usuario esta logeado?
    public function eliminarJuego($id){
        try{
            $conn = conectarbd();
            $sql = "SELECT * FROM `usuario` WHERE id = $id"; // EL PROBLEMA ACA ES QUE ESTOY BUSCANDO EN LA TABLA USUARIOS CON EL ID DEL JUEGO OSEA CAIDO
            $response = mysqli_query($conn, $sql);
            $user = $response->fetch_assoc();
            // $admin = $user['es_admin'];
            $admin = 1; // Le asigno true para probar (pd: funciona)
            if($admin){
                // Chequeo que el usuario se encuentre logeado
                // $log = verificarLogin($id);
                $log = 1; // Le asigno true para probar (pd: funciona)
                if($log){
                    $sql = "DELETE FROM `juego` WHERE id = $id";
                    $response = mysqli_query($conn, $sql);
                    if(!$response){
                        $respuesta =  ['status'=> 409, 'result'=>"No se ha eliminado el juego"];
                    }
                    else{
                        $respuesta = ['status'=>200, 'result'=>"Se ha eliminado el juego correctamente"];
                    }
                }
                else{
                    $respuesta = ['status'=> 401, 'result'=>"El usuario no se encuentra logeado"];
                }
            }
            else{
                $respuesta = ['status'=> 401, 'result'=>"El usuario no es administrador"];
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