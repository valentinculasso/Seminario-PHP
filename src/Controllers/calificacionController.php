<?php
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    class calificacionController {

        // createCalification($estrellas, $userID, $juegoID): Crea una nueva calificacion. Recibe como parametros las estrellas, id del usuario y id del juego.
        public function createCalification($estrellas, $userID, $juegoID){
            try{
                $conn = conectarbd();
                $sql2 = "SELECT * FROM `calificacion` WHERE usuario_id = '$userID' AND juego_id = '$juegoID'";
                $response2 = mysqli_query($conn, $sql2);
                $result2 = mysqli_fetch_array($response2);
                if(!$result2){
                    $sql = "INSERT INTO `calificacion` (`estrellas`, `usuario_id`, `juego_id`) VALUES ('$estrellas', '$userID', '$juegoID')";
                    $response = mysqli_query($conn, $sql);
                    if(!$response){
                        $respuesta = ['status'=> 404, 'result'=>"La calificacion no se ha creado"];
                    }
                    else{
                        $respuesta = ['status'=> 200, 'result'=>"La calificacion se ha creado con exito"];
                    }
                }
                else{
                    $respuesta =  ['status'=> 404, 'result'=>"La calificacion no puede ser creada porque el usuario ya cuenta con una calificacion para dicho juego"];
                }
                $conn = desconectarbd($conn);
            }
            catch(Exception $e){
                $respuesta = ['status'=>500, 'result'=> $e->getMessage()];
            }
            return $respuesta;
        }

        // editCalification($id_calificacion, $estrellas, $id_usuario, $id_juego): Edita una calificacion existente. Recibe como parametros 
        public function editCalificacion($id_calificacion, $estrellas, $id_juego, $user_log_id){
            try{
                $conn = conectarbd();
                $sql = "SELECT * FROM `calificacion` WHERE id = $id_calificacion AND usuario_id = $user_log_id";
                $response = mysqli_query($conn, $sql);
                $result = mysqli_fetch_array($response);
                if($result){
                    // Agrego esta consulta ya que si un usuario edita una calificacion con un id de juego que no existe en la tabla juegos, da error.
                    $sql2 = "SELECT * FROM `juego` WHERE id = $id_juego";
                    $response2 = mysqli_query($conn, $sql2);
                    $result2 = mysqli_fetch_array($response2);
                    if($result2){
                        $sql = "UPDATE `calificacion` SET estrellas = '$estrellas', juego_id = '$id_juego' WHERE id = '$id_calificacion'"; 
                        mysqli_query($conn, $sql);
                        if(mysqli_affected_rows($conn) === 0){
                            $respuesta =  ['status'=> 404, 'result'=>"La calificacion no existe o no fue modificada"];
                        }
                        else{
                            $respuesta = ['status'=>200, 'result'=>"Se ha editado la calificacion correctamente"];
                        }
                    }
                    else{
                        $respuesta =  ['status'=> 404, 'result'=>"La calificacion no puede ser modificada porque el ID del juego ingresado no existe"];
                    }
                }
                else{
                    $respuesta =  ['status'=> 404, 'result'=>"La calificacion no puede ser modificada porque no existe o pertenece a otro usuario"];
                }
                $conn = desconectarbd($conn);
            }
            catch(Exception $e){
                $respuesta = ['status'=>500, 'result'=> $e->getMessage()];
            }
            return $respuesta;
        }

        // deleteCalification($id): Elimina una calificacion. Recibe como parametros el id de la calificacion.
        public function deleteCalification($id_calificacion, $user_log_id){
            try{
                $conn = conectarbd();
                $sql = "SELECT * FROM `calificacion` WHERE id = $id_calificacion AND usuario_id = $user_log_id";
                $response = mysqli_query($conn, $sql);
                $result = mysqli_fetch_array($response);
                if($result){
                    $sql = "DELETE FROM `calificacion` WHERE id = $id_calificacion";
                    mysqli_query($conn, $sql);
                    if(mysqli_affected_rows($conn) === 0){
                        $respuesta =  ['status'=> 409, 'result'=>"No se ha eliminado la calificacion porque la calificacion no existe"];
                    }
                    else{
                        $respuesta = ['status'=>200, 'result'=>"Se ha eliminado la calificacion correctamente"];
                    }
                }
                else{
                    $respuesta =  ['status'=> 409, 'result'=>"No se ha eliminado la calificacion porque pertenece a otro usuario"];
                }
                $conn = desconectarbd($conn);
            }
            catch(Exception $e){
                $respuesta = ['status'=>500, 'result'=> $e->getMessage()];
            }
            return $respuesta;
        }

        public function getCalificaciones($id) {
            try{
                $connection = conectarbd();
                $sql = "SELECT * FROM `calificacion` WHERE usuario_id = $id";
                $response = mysqli_query($connection, $sql);
                if(!$response){
                    $respuesta = ['status'=> 404, 'result'=>"El usuario no tiene calificaciones en ese juego"];
                }
                else{
                    $jsonData = array();
                    while($array = mysqli_fetch_assoc($response)){
                        $jsonData[]= $array;
                    }
                    if(!$jsonData){
                        $respuesta = ['status'=> 404, 'result'=>"No hay juegos que mostrar"];
                    }
                    else{
                        $respuesta = ['status'=>200, 'result'=>$jsonData];
                    }
                }
                $connection = desconectarbd($connection);
            }
            catch(Exception $e){
                $respuesta = ['status'=>500, 'result'=> $e->getMessage()];
            }
            return $respuesta;
        }

        public function getAllCalificaciones($juego_id) {
            try{
                $connection = conectarbd();
                $sql = "SELECT * FROM `calificacion` WHERE juego_id = '$juego_id'";
                $response = mysqli_query($connection, $sql);
                if(!$response){
                    $respuesta = ['status'=> 404, 'result'=>"No hay calificaciones que mostrar"];
                }
                else{
                    $jsonData = array();
                    while($array = mysqli_fetch_assoc($response)){
                        $jsonData[]= $array;
                    }
                    if(!$jsonData){
                        $respuesta = ['status'=> 404, 'result'=>"No hay juegos que mostrar"];
                    }
                    else{
                        $respuesta = ['status'=>200, 'result'=>$jsonData];
                    }
                }
                $connection = desconectarbd($connection);
            }
            catch(Exception $e){
                $respuesta = ['status'=>500, 'result'=> $e->getMessage()];
            }
            return $respuesta;
        }
    }
?>