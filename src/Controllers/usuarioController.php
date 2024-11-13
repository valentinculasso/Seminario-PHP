<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . "/../App/Functions.php";

class usuarioController {

    // login($nombreUsuario, $clave): login a un usuario, se le generan sus respectivos tokens. Se recibe como parametro el nombre de usuario y su clave
    public function login($nombreUsuario, $clave){
        try{
            $conn = conectarbd();
            // validar parametros de entrada
            $sql = "SELECT * FROM `usuario` WHERE nombre_usuario = '$nombreUsuario' AND clave = '$clave'";
            $response = mysqli_query($conn, $sql);
            if(mysqli_num_rows($response) > 0){
                // Si entra aca el nombre de usuario y clave existen y son validas
                $user = mysqli_fetch_assoc($response);
                // Crear token
                $fechaVencimientoToken = new DateTime();
                $fechaVencimientoToken->modify('+1 hour');
                $fechaVencimientoToken->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
                $FechaVencimiento = $fechaVencimientoToken->format('Y-m-d H:i:s');
                $token = '{
                    "id":'.  $user['id'] .',
                    "date":"'. $FechaVencimiento .'",
                    "admin": '. $user['es_admin'] .'
                }';
                $token_encode = base64_encode($token); // le aplico base64 al token
                // Fin crear token

                $id = $user['id'];

                $sql = "UPDATE `usuario` SET token = '$token_encode' , vencimiento_token = '$FechaVencimiento' WHERE id = '$id'";
                $response = mysqli_query($conn, $sql);
                if($response){
                    $respuesta = ['status'=>200, 'result'=>$token_encode];
                }
                else{
                    $respuesta = ['status'=>401, 'result'=>'El nombre de usuario no existe o credenciales incorrectas'];
                }
            }
            else{
                // El nombre de usuario no existe
                $respuesta = ['status'=>401, 'result'=>'El nombre de usuario no existe o credenciales incorrectas'];
            }
            $conn = desconectarbd($conn);
        }
        catch(Exception $e){
            // No se pudo conectar a la base de datos
            $respuesta = ['status'=>500, 'result'=> $e->getMessage()];
        }
        return $respuesta;
    }

    // De aqui para abajo son los metodos utilizados para el POST, PUT, DELETE Y GET en ese orden.

    // createUser($nombre, $clave, $admin): Crea un nuevo usuario. Recibe como parametros el nombre de usuario, su clave y si es o no admin.
    public function createUser($nombre, $clave, $admin){
        try{
            $conn = conectarbd();
            $sql = "SELECT * FROM `usuario` WHERE nombre_usuario = '$nombre'";
            $response = mysqli_query($conn, $sql);
            if(!mysqli_num_rows($response)){
                        if (!ctype_alnum($nombre) || (!(strlen($nombre) > 6)) || (!(strlen($nombre) < 20))){
                                $respuesta = ['status'=> 401, 'result'=>"El nombre de usuario ingresado no cumple con los requisitos."];
                        }
                        else{
                            if((!(strlen($clave) > 8)) || (!preg_match('/[A-Z]/', $clave) || !preg_match('/[a-z]/', $clave) || !preg_match('/[0-9]/', $clave) || !preg_match('/[\W_]/', $clave))){
                                $respuesta = ['status'=> 401, 'result'=>"La clave no cumple con los requisitos."];
                            }
                            else{
                                // Al llegar aca, tanto el nombre de usuario como la contraseña son validos
                                $sql = "INSERT INTO `usuario`(`nombre_usuario`, `clave`, `es_admin`) VALUES ('$nombre', '$clave', '$admin')";
                                $response = mysqli_query($conn, $sql);
                                if(!$response){
                                    $respuesta =  ['status'=> 401, 'result'=>"No se ha podido crear el usuario"];
                                }
                                else{
                                    $respuesta = ['status'=>200, 'result'=>"Se ha creado un nuevo usario"];
                                }
                            }
                        }
            }
            else{
                $respuesta = ['status'=>401, 'result'=>'No se ha podido registrar ya que el nombre de usuario existe'];
            }
            $conn = desconectarbd($conn);
        }
        catch(Exception $e){
            // Si por ejemplo no me pude conectar a la base de datos envio un status 500
            $respuesta = ['status'=>500, 'result'=> $e->getMessage()];
        }
        return $respuesta;
    }

    // editUser($id): Edita un usuario existente. Recibe como parametro el ID del usuario.
    public function editUser($id, $nombre, $clave, $admin){
        try{
            $conn = conectarbd();
            if (!ctype_alnum($nombre) || (!(strlen($nombre) > 6)) || (!(strlen($nombre) < 20))){
                $respuesta = ['status'=> 401, 'result'=>"El nombre de usuario ingresado no cumple con los requisitos."];
            }
            else{
                if((!(strlen($clave) > 8)) || (!preg_match('/[A-Z]/', $clave) || !preg_match('/[a-z]/', $clave) || !preg_match('/[0-9]/', $clave) || !preg_match('/[\W_]/', $clave))){
                    $respuesta = ['status'=> 401, 'result'=>"La clave no cumple con los requisitos."];
                }
                else{
                    $sql = "UPDATE  `usuario` SET nombre_usuario = '$nombre', clave = '$clave', es_admin = '$admin' WHERE id = $id";
                    mysqli_query($conn, $sql);
                    if(mysqli_affected_rows($conn) === 0){
                        $respuesta = ['status'=> 404, 'result'=>"ID del usuario inexistente"];
                    }
                    else{
                        $respuesta = ['status'=>200, 'result'=>"Se han actualizado los datos del usuario"];
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

    // deleteUser($id): Elimina un usuario. Recibe como parametro el ID del usuario a eliminar.
    public function deleteUser($id){
        try{
            $conn = conectarbd();
            $consultaSoporte = "SELECT * FROM `calificacion` WHERE usuario_id = $id";
            $resultS = mysqli_query($conn, $consultaSoporte);
            if(mysqli_num_rows($resultS) === 0){
                $sql = "DELETE FROM `usuario` WHERE id = $id";
                mysqli_query($conn, $sql);
                if (mysqli_affected_rows($conn) === 0) {
                    $respuesta = ['status' => 409, 'result' => "El usuario no ha sido eliminado porque id del usuario no existe"];
                } else {
                    $respuesta = ['status' => 200, 'result' => "Se ha eliminado correctamente el usuario"];
                }
            }
            else{
                $respuesta = ['status' => 409, 'result' => "El usuario no ha sido eliminado porque el usuario tiene calificaciones"];
            }
            $conn = desconectarbd($conn);
        }
        catch(Exception $e){
            $respuesta = ['status'=>500, 'result'=> $e->getMessage()];
        }
        return $respuesta;
    }

    // getUser($id): Obtiene la informacion de un usuario especifico. Recibe como parametro el ID del usuario.
    public function getUser($id){
        try{
            $conn = conectarbd();
            $sql = "SELECT * FROM `usuario` WHERE id = $id";
            $result = mysqli_query($conn, $sql);
            $response = mysqli_fetch_assoc($result);
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