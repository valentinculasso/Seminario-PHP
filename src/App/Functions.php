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
    
?>