<?php

require_once __DIR__ . "/../App/Functions.php";

class juegoController {

    public function getPagina($pagina, $clasificacion, $texto, $plataforma){
        try{
            $connection = conectarbd();

            $paginaActual = ($pagina - 1) * 5;

            $sql = "SELECT
                        j.id AS id_juego, 
                        j.nombre AS nombre_juego,
                        IFNULL(GROUP_CONCAT(DISTINCT P.nombre SEPARATOR ', '), 'Ninguno') AS plataformas,
                        j.clasificacion_edad clasificacion_edad,
                        IFNULL(AVG(c.estrellas), 0) AS calificacion_promedio
                    FROM 
                        juego j
                    LEFT JOIN 
                        calificacion c ON j.id = c.juego_id
                    LEFT JOIN soporte S ON j.id = S.juego_id
                    LEFT JOIN plataforma P ON S.plataforma_id = P.id ";

            $conditions = [];
            
            if (!empty($texto)) {
                $conditions[] = "j.nombre LIKE '%$texto%'";
            }
            if (!empty($clasificacion) && $clasificacion !== "+18") {
                if ($clasificacion === "+13") {
                    $conditions[] = "(j.clasificacion_edad = '$clasificacion' OR j.clasificacion_edad = 'ATP')";
                } else {
                    $conditions[] = "j.clasificacion_edad = '$clasificacion'";
                }
            }
            if (!empty($plataforma)) {
                $conditions[] = "P.nombre = '$plataforma'";
            }
            if (count($conditions) > 0) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }

            $sql .= "GROUP BY 
                        j.id, j.nombre, j.clasificacion_edad
                    LIMIT 5 OFFSET $paginaActual";

            $result = mysqli_query($connection, $sql);
            
            // Consulta para contar el total de juegos
            $sqlCount = "SELECT COUNT(DISTINCT j.id) AS total FROM juego j 
                        LEFT JOIN soporte S ON j.id = S.juego_id
                        LEFT JOIN plataforma P ON S.plataforma_id = P.id ";

            if (count($conditions) > 0) {
                $sqlCount .= " WHERE " . implode(" AND ", $conditions);
            }

            $resultCount = mysqli_query($connection, $sqlCount);
            $totalCount = mysqli_fetch_assoc($resultCount)['total'];

            $jsonData = array();
            while($array = mysqli_fetch_assoc($result)){
                $jsonData[]= $array;
            }
            if(!$jsonData){
                $respuesta = ['status'=> 404, 'result'=>"No hay juegos que mostrar", 'total'=> 0];
            }
            // Aca podria ir un if porque puede que el $sqlCount sea null y eso daria error
            // En el if de arriba agregue el campo 'total' tambien en la condicion deberia poner algo como: | !$totalCount (or !$totalCount)
            else{
                $respuesta = [
                    'status'=>200,
                    'result'=>$jsonData,
                    'total' => $totalCount
                ];
            }
            $connection = desconectarbd($connection);
        }
        catch(Exception $e){
            $respuesta = ['status'=>500, 'result'=> $e->getMessage()];
        }
        return $respuesta;
    }
    
    // getJuego($id): Obtiene la informacion de un juego especifico y su listado de calificaciones. Recibo como parametro el ID del juego.
    public function getJuego($id){
        try{
            $connection = conectarbd();
            $sql = "SELECT
                        j.*,
                        IFNULL(GROUP_CONCAT(DISTINCT P.nombre SEPARATOR ', '), 'Ninguno') AS plataformas,
                        IFNULL(AVG(c.estrellas), 0) AS calificacion_promedio
                    FROM
                        juego j
                    LEFT JOIN
                        calificacion c ON j.id = c.juego_id
                    LEFT JOIN soporte S ON j.id = S.juego_id
                    LEFT JOIN plataforma P ON S.plataforma_id = P.id
                    WHERE
                        j.id = $id";

            $result = mysqli_query($connection, $sql);
            $response = mysqli_fetch_assoc($result);
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
    public function agregarJuego($nombre_juego, $descripcion, $imagen, $clasificacion_edad){
        try{
            $conn = conectarbd();
            if(strlen($nombre_juego) < 45 && ($clasificacion_edad == "ATP" || $clasificacion_edad == "+13" || $clasificacion_edad == "+18")){
                    $sql = "SELECT * FROM `juego` WHERE nombre = '$nombre_juego'";
                    $response = mysqli_query($conn, $sql);
                    if(mysqli_num_rows($response) === 0){
                        $sql = "INSERT INTO `juego` (`nombre`, `descripcion`, `imagen`, `clasificacion_edad`) VALUES ('$nombre_juego', '$descripcion', '$imagen', '$clasificacion_edad')";
                        $response = mysqli_query($conn, $sql);
                        if(!$response){
                            $respuesta = ['status'=> 404, 'result'=>"El juego no se ha añadido", 'juego_id' => null];
                        }
                        else{
                            $sql2 = "SELECT * FROM `juego` WHERE nombre = '$nombre_juego'";
                            $response2 = mysqli_query($conn, $sql2);
                            if ($response2 && mysqli_num_rows($response2) > 0) {
                                $id = mysqli_fetch_assoc($response2)['id'];
                                $respuesta = [
                                    'status'=> 200,
                                    'result'=>"El juego se ha agregado con exito",
                                    'juego_id'=> $id
                                ];
                            } else {
                                $respuesta = ['status'=> 404, 'result'=>"No se encontró el juego después de agregarlo.", 'juego_id' => null];
                            }
                        }
                    }
                    else{
                        $respuesta = ['status'=> 404, 'result'=>"El juego ya existe!", 'juego_id' => null];
                    }
            }
            else{
                $respuesta = ['status'=> 401, 'result'=>"El nombre del juego ingresado no cumple con los requisitos.", 'juego_id' => null];
            }
            $conn = desconectarbd($conn);
        }
        catch(Exception $e){
            $respuesta = ['status'=>500, 'result'=> $e->getMessage()];
        }
        return $respuesta;
    }

    // editarJuego($id): actualiza los datos de un juego existente. Recibe como parametros el id del juego
    public function editarJuego($id, $nombre_juego, $descripcion, $imagen, $clasificacion_edad){
        try{
            $conn = conectarbd();
            if(strlen($nombre_juego) < 45){
                if($clasificacion_edad = "ATP" || $clasificacion_edad = "+13" || $clasificacion_edad = "+18"){
                    $img64 = base64_encode($imagen);
                    $sql = "UPDATE `juego` SET nombre = '$nombre_juego', descripcion = '$descripcion', imagen = '$img64', clasificacion_edad = '$clasificacion_edad' WHERE id = '$id'";
                    mysqli_query($conn, $sql);
                    // Uso mysqli_affected_rows para saber si la fila ha sido afectada/ha sufrido cambios
                    if(mysqli_affected_rows($conn) === 0){
                        $respuesta =  ['status'=> 404, 'result'=>"El id del juego no existe"];
                    }
                    else{
                        $respuesta = ['status'=>200, 'result'=>"Se ha editado el juego correctamente"];
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

    // eliminarJuego($id): elimina un juego de la base de datos. Recibe como parametro el id del juego
    public function eliminarJuego($id){
        try{
            $conn = conectarbd();
            $consultaSoporte = "SELECT * FROM `soporte` WHERE juego_id = $id";
            $resultS = mysqli_query($conn, $consultaSoporte);
            if(mysqli_num_rows($resultS) === 0){

                $consultaCalificaciones = "SELECT * FROM `calificacion` WHERE juego_id = $id";
                $result = mysqli_query($conn, $consultaCalificaciones);
                if(mysqli_num_rows($result) === 0){

                    $sql = "DELETE FROM `juego` WHERE id = $id";
                    mysqli_query($conn, $sql);
                    if(mysqli_affected_rows($conn) === 0){
                        $respuesta =  ['status'=> 409, 'result'=>"No se ha eliminado el juego porque el id del juego no existe"];
                    }
                    else{
                        $respuesta = ['status'=>200, 'result'=>"Se ha eliminado el juego correctamente"];
                    }
                }
                else{
                    $respuesta =  ['status'=> 409, 'result'=>"No se ha eliminado el juego porque el id del juego tiene calificaciones"];
                }
            }
            else{
                $respuesta =  ['status'=> 409, 'result'=>"No se ha eliminado el juego porque el id del juego se encuentra en soporte"];
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