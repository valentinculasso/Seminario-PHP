<?php

    class soporteController {

        public function agregarSoporte($juego_id, $plataforma_id){
            try{
                $conn = conectarbd();
                $sql = "SELECT * FROM `soporte` WHERE juego_id = '$juego_id' AND plataforma_id = '$plataforma_id'";
                $result = mysqli_query($conn, $sql);
                if(mysqli_num_rows($result) === 0){
                    if((strlen($plataforma_id) >= 1)|| (strlen($plataforma_id) <= 5)){
                            $sql = "INSERT INTO `soporte` (`juego_id`, `plataforma_id`) VALUES ('$juego_id', '$plataforma_id')";
                            $response = mysqli_query($conn, $sql);
                            if(!$response){
                                $respuesta = ['status'=> 404, 'result'=>"El soporte del juego no se ha añadido"];
                            }
                            else{
                                $respuesta = ['status'=> 200, 'result'=>"El soporte del juego se ha agregado con exito"];
                            }
                        }
                }
                else{
                    $respuesta = ['status'=> 404, 'result'=>"El soporte de esta plataforma ya existe!"];
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