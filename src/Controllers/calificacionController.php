<?php
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    class calificacionController {

        // createCalification($estrellas, $userID, $juegoID): Crea una nueva calificacion. Recibe como parametros las estrellas, id del usuario y id del juego.
        public function createCalification($estrellas, $userID, $juegoID){
            try{
                $conn = conectarbd();
                $sql = "INSERT INTO `calificacion` (`estrellas`, `usuario_id`, `juego_id`) VALUES ('$estrellas', '$userID', '$juegoID')";
                $response = mysqli_query($conn, $sql);
                if(!$response){
                    $respuesta = ['status'=> 404, 'result'=>"La calificacion no se ha creado"];
                }
                else{
                    $respuesta = ['status'=> 200, 'result'=>"La calificacion se ha creado con exito"];
                }
                $conn = desconectarbd($conn);
            }
            catch(Exception $e){
                $respuesta = ['status'=>500, 'result'=> $e->getMessage()];
            }
            return $respuesta;
        }

        // editCalification($id_calificacion, $estrellas, $id_usuario, $id_juego): Edita una calificacion existente. Recibe como parametros 
        public function editCalificacion($id_calificacion, $estrellas, $id_usuario, $id_juego){
            try{
                $conn = conectarbd();
                $sql = "UPDATE `calificacion` SET estrellas = '$estrellas', usuario_id = '$id_usuario', juego_id = '$id_juego' WHERE id = '$id_calificacion'"; 
                // DUDAS: id_juego no lo uso, xq modifico la calificacion (estrellas) no el juego, y el id del usuario seguiria siendo el mismo al igual que el id del juego
                $response = mysqli_query($conn, $sql);
                if(!$response){
                    $respuesta =  ['status'=> 404, 'result'=>"La calificacion no existe"];
                }
                else{
                    $respuesta = ['status'=>200, 'result'=>"Se ha editado la calificacion correctamente"];
                }
                $conn = desconectarbd($conn);
            }
            catch(Exception $e){
                $respuesta = ['status'=>500, 'result'=> $e->getMessage()];
            }
            return $respuesta;
        }

        // deleteCalification($id): Elimina una calificacion. Recibe como parametros el id de la calificacion.
        public function deleteCalification($id_calificacion){
            try{
                $conn = conectarbd();
                $sql = "DELETE FROM `calificacion` WHERE id = $id_calificacion";
                $response = mysqli_query($conn, $sql);
                if(!$response){
                    $respuesta =  ['status'=> 409, 'result'=>"No se ha eliminado la calificacion"];
                }
                else{
                    $respuesta = ['status'=>200, 'result'=>"Se ha eliminado la calificacion correctamente"];
                }
            }
            catch(Exception $e){
                $respuesta = ['status'=>500, 'result'=> $e->getMessage()];
            }
            return $respuesta;
        }

    }
?>