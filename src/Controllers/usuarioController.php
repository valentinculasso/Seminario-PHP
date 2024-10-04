<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . "/../App/Functions.php"; // require_once me permite usar la carpeta functions en todas las clases Controller

class usuarioController {

    // register($nombreUsuario, $clave): registra un nuevo usuario. Recibe como parametros el nombre de usuario y la clave
    public function register($nombreUsuario, $clave){
        if (ctype_alnum($nombreUsuario)){ //Primero chequeo que la cadena sean TODOS alfanumericos
            if(!(strlen($nombreUsuario) > 6)or(!(strlen($nombreUsuario) < 20))){ // Luego chequeo que este en el rango de caracteres
                $respuesta = ['status'=> 401, 'result'=>"El nombre de usuario ingresado no cumple con los requisitos."];
            }
            else{
                // si entra aca el nombre de usuario es valido por lo que tengo que chequear ahora la clave
                if(!(strlen($clave) > 8)){
                    $respuesta = ['status'=> 401, 'result'=>"La clave no cumple con los requisitos."];
                }
                else{
                    if (!preg_match('/[A-Z]/', $clave) || !preg_match('/[a-z]/', $clave) || !preg_match('/[0-9]/', $clave) || !preg_match('/[\W_]/', $clave)) {
                        $respuesta = ['status'=> 401, 'result'=>"La clave no cumple con los requisitos."];
                    }
                    else{
                        $respuesta = $this -> agregarUsuario($nombreUsuario, $clave);
                    }
                }
            }
        }
        else{
            $respuesta = ['status'=> 401, 'result'=>"El nombre de usuario ingresado no es alfanumerico."];
        }
        return $respuesta;
    }

    // agregarUsuario($nombre, $clave): Si el usuario no existe agrega un nuevo usuario a la base de datos. Recibe como parametros el nombre de usuario y su contraseña
    public function agregarUsuario($nombre, $clave){
        try{
            $conn = conectarbd();
            $sql = "SELECT * FROM `usuario` WHERE nombre_usuario = '$nombre'";
            $response = mysqli_query($conn, $sql);
            if(!mysqli_num_rows($response)){
                // Si entra aca el nombre de usuario no existe
                $sql = "INSERT INTO `usuario`(`nombre_usuario`, `clave`, `es_admin`) VALUES ('$nombre', '$clave', '0')";
                $response = mysqli_query($conn, $sql);
                if(!$response){
                    $respuesta =  ['status'=> 401, 'result'=>"No se ha creado un nuevo usuario"];
                }
                else{
                    $respuesta = ['status'=>200, 'result'=>"Se ha registrado con exito"];
                }
            }
            else{
                // Si entra aca el nombre de usuario existe
                $respuesta = ['status'=>401, 'result'=>'No se ha podido registrar ya que el nombre de usuario existe'];
            }
            $conn = desconectarbd($conn);
        }
        catch(Exception $e){
            $respuesta = ['status'=>500, 'result'=> $e->getMessage()];
        }
        return $respuesta;
    }

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
                $fecha = new DateTime(); // Creo variable fecha instanciando DateTime
                $token = '{
                    "id":'.  $user['id'] .',
                    "date":"'. $fecha->format('y-m-d H:i') .'",
                    "admin": '. $user['es_admin'] .'
                }';
                $token_encode = base64_encode($token); // le aplico base64 al token
                // Fin crear token

                $id = $user['id']; // Creo variable $id y le asigno el id del usuario que se esta logeando

                // Crear vencimiento_token (es un DateTime en mi base de datos)
                $fechaVencimiento = new DateTime();
                $fechaVencimiento->modify('+1 hour'); // Sumar 1 hora a la fecha actual
                $vencimientoTokenDate = $fechaVencimiento->format('Y-m-d H:i:s');
                // Fin crear vencimiento_token

                $sql = "UPDATE `usuario` SET token = '$token_encode' , vencimiento_token = '$vencimientoTokenDate' WHERE id = '$id'";
                $response = mysqli_query($conn, $sql);
                // Deberia agregar un chequeo para saber si se ejecuto la consulta correctamente?
                $respuesta = ['status'=>200, 'result'=>$token_encode];
            }
            else{
                // El nombre de usuario no existe
                $respuesta = ['status'=>401, 'result'=>'Credenciales incorrectas'];
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

    // editUser($id): Edita un usuario existente. Recibe como parametro el ID del usuario.
    public function editUser($id, $nombre, $clave, $admin){
        try{
            $conn = conectarbd();
            $sql = "UPDATE  `usuario` SET nombre_usuario = '$nombre', clave = '$clave', es_admin = '$admin' WHERE id = $id";
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

    // deleteUser($id): Elimina un usuario. Recibe como parametro el ID del usuario a eliminar.
    public function deleteUser($id){
        try{
            $conn = conectarbd();
            $sql = "DELETE FROM `usuario` WHERE id = $id";
            $response = mysqli_query($conn, $sql);
            if(!$response){
                $respuesta =  ['status'=> 409, 'result'=>"No se ha eliminado el usuario"];
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

    // getUser($id): Obtiene la informacion de un usuario especifico. Recibe como parametro el ID del usuario.
    public function getUser($id){
        try{
            $conn = conectarbd();
            $sql = "SELECT * FROM `usuario` WHERE id = $id";
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