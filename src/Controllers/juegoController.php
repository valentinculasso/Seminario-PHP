<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . "/../App/Functions.php";

class juegoController {

    /*  GET
    /juegos?pagina={pagina}&clasificacion={clasificacion}&texto={texto}&pl
    ataforma={plataforma} Listar los juegos de la página según los parámetros
    de búsqueda incluyendo la puntuación promedio del juego.
    */
    public function getPagina($pagina, $clasificacion, $texto, $plataforma){
        try{
            $connection = conectarbd();
            $pagina = ($pagina - 1) * 5;
            // Consulta SQL normal sin ningun filtro
            $sql = "SELECT 
                        j.id AS id_juego, 
                        j.nombre AS nombre_juego,
                        P.nombre nombre_plataforma,
                        j.clasificacion_edad clasificacion_edad,
                        IFNULL(AVG(c.estrellas), 0) AS calificacion_promedio
                    FROM 
                        juego j
                    LEFT JOIN 
                        calificacion c ON j.id = c.juego_id
                    INNER JOIN soporte S ON j.id = S.juego_id
                    INNER JOIN plataforma P ON S.plataforma_id = P.id ";

            // Inicializa un array para almacenar las condiciones de filtrado
            $conditions = [];
            
            if (!empty($texto)) {
                $conditions[] = "j.nombre LIKE '%$texto%'"; // Agrega condición para el nombre
            }
            if (!empty($clasificacion)) {
                $conditions[] = "j.clasificacion_edad = '$clasificacion'"; // Agrega condición para la clasificación
            }
            if (!empty($plataforma)) {
                $conditions[] = "P.nombre IN ('$plataforma')"; // Agrega condición para la plataforma
            }

            if (count($conditions) > 0) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }

            $sql .= " GROUP BY 
                        j.id, j.nombre
                    LIMIT 5 OFFSET $pagina";

            $result = mysqli_query($connection, $sql);

            // DE ACA PARA ABAJO ES LO QUE DEVUELVO A ROUTES

            $response = mysqli_fetch_all($result);

            if(!$response){
                $respuesta = ['status'=> 404, 'result'=>"No hay juegos que mostrar"];
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
    public function agregarJuego($nombre_juego, $descripcion, $imagen, $clasificacion_edad){
        try{
            $conn = conectarbd();
            if(strlen($nombre_juego) < 45){
                if($clasificacion_edad = "ATP" || $clasificacion_edad = "+13" || $clasificacion_edad = "+18"){
                    $img64 = base64_encode($imagen);
                    $sql = "INSERT INTO `juego` (`nombre`, `descripcion`, `imagen`, `clasificacion_edad`) VALUES ('$nombre_juego', '$descripcion', '$img64', '$clasificacion_edad')";
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
            // Aca tengo un problema, porque si el id de mi juego esta en soporte no me va a dejar eliminarlo ¿deberia chequear que no este en soporte tambien?
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