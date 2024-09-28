<?php

    require_once __DIR__ . "/../Controllers/usuarioController.php";

    function conectarbd(){

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Permite manejar excepcion

        $conn = mysqli_connect("localhost", "root", "", "seminariophp");
        
        if (!$conn) {
            echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
            echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
            echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
            exit;
        }
        
        return $conn;
    }

    function desconectarbd($conn){
        mysqli_close($conn);
    }

    // verificarLogin(): Me verifica si el usuario con un ID que se recibe por parametro esta logeado o no.
    function verificarLogin($id){
        try {
            // Conectar a la base de datos
            $connect = conectarbd();
            // Usar una consulta preparada para evitar SQL Injection
            $stmt = $connect->prepare("SELECT token, vencimiento_token FROM usuario WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $response = $stmt->get_result();
            // Si el usuario existe
            if ($response->num_rows > 0) {
                $user = $response->fetch_assoc();
                // Obtener vencimiento_token
                $tokenVenc = $user['vencimiento_token'];
                // Verificar si el token de vencimiento no está vacío
                if (!empty($tokenVenc)) {
                    // Verificar si la decodificación fue correcta
                    $fechaVencimiento = new DateTime($tokenVenc);
                    $horaActual = new DateTime();
                    // Comparar las fechas
                    if ($horaActual > $fechaVencimiento) {
                        $ok = 0; // Token expirado
                    } 
                    else {
                        $ok = 1; // Token válido
                    }
                }
                else{
                    // Si el token de vencimiento está vacío
                    $ok = 0;
                }
            } else {
                // Usuario no encontrado
                $ok = 0;
            }
            // Cerrar la conexión y liberar recursos
            $stmt->close();
            desconectarbd($connect);
        } catch (Exception $e) {
            // Si ocurre cualquier error, devolvemos 0
            $ok = 0;
        }
        return $ok;
    }
?>