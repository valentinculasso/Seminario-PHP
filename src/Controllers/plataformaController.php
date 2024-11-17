<?php
    class plataformaController {
        public function getPlataformas(){
            try{
                $connection = conectarbd();
                $sql = "SELECT * FROM `plataforma`";
                $response = mysqli_query($connection, $sql);
                if(!$response){
                    $respuesta = ['status'=> 404, 'result'=>"No hay plataformas que mostrar"];
                }
                else{
                    $jsonData = array();
                    while($array = mysqli_fetch_assoc($response)){
                        $jsonData[]= $array;
                    }
                    if(!$jsonData){
                        $respuesta = ['status'=> 404, 'result'=>"No hay plataformas que mostrar"];
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