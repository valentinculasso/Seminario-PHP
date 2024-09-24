<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . "/../App/Functions.php"; // require_once me permite usar la carpeta functions en todas las clases Controller
require __DIR__ . "/../Models/usuario.php"; // Para generar un token voy a necesitar usar la carpeta Models donde se encuentra la clase usuario

class usuarioController {

    public function register($nombreUsuario, $clave){

        if (ctype_alnum($nombreUsuario)){ //Primero chequeo que la cadena sean TODOS alfanumericos
            if(!(strlen($nombreUsuario) > 6)or(!(strlen($nombreUsuario) < 20))){ // Luego chequeo que este en el rango de caracteres
                $respuesta = ['status'=> 401, 'result'=>"El nombre de usuario ingresado no cumple con los requisitos."];
            }
            else{
                // si entra aca el nombre de usuario es valido por lo que tengo que chequear ahora la clave
                if(!(strlen($clave) >8)){
                    $respuesta = ['status'=> 401, 'result'=>"La clave no cumple con los requisitos."];
                }
                else{
                    $respuesta = $this -> agregarUsuario($nombreUsuario, $clave);
                }
            }
        }
        else{
            $respuesta = ['status'=> 401, 'result'=>"El nombre de usuario ingresado no es alfanumerico."];
        }
        return $respuesta;
    }

    public function login($usuario, $clave){

    }

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

    public function agregarUsuario($nombre, $clave){
        try{
            $conn = conectarbd();
            // consulta SQL
            $sql = "SELECT * FROM `usuario` WHERE nombre_usuario = '$nombre'";
            // Envia la consulta a la base de datos
            $response = mysqli_query($conn, $sql);
            if(!mysqli_num_rows($response)){
                // El nombre de usuario no existe
                $this -> insertUser($nombre, $clave, 0);
                $respuesta = ['status'=>200, 'result'=>'Usuario registrado con exito'];
            }
            else{
                // El nombre de usuario existe
                $respuesta = ['status'=>401, 'result'=>'No se ha podido registrar ya que el nombre de usuario existe'];
            }
            $conn = desconectarbd($conn);
        }
        catch(Exception $e){
            // No se pudo conectar a la base de datos
            $respuesta = ['status'=>500, 'result'=> $e->getMessage()];
        }
        return $respuesta;
    }
}

?>