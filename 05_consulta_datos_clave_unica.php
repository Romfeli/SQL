<?php 
    //las consultas por clave única nos entregarán siempre una fila o ninguna (en caso que la clave no exista)

    try {
        //incorporar fichero de conexión
        require('00_conexionbanco.php');

        //confeccionar la sentencia SQL
        $nif = "10000002C";
        
        //seleccionar columnas específicas
        //$sql = "SELECT nif, nombre, apellidos FROM personas WHERE nif = '$nif'";

        //seleccionar todas las columnas
        $sql = "SELECT * FROM personas WHERE nif = '$nif'";

        //trasladar la sentencia al SGBD
        if (!$consulta = mysqli_query($conexionBanco, $sql)) {
            throw new Exception(mysqli_error($conexionBanco), 10);
        }

        //visualizar el objeto de consulta
        echo '<pre>';
        print_r($consulta);
        echo '</pre>';

        //comprobar si la consulta nos devuelve alguna fila
        if ($consulta->num_rows == 0) {
            throw new Exception("Nif no existe en la base de datos", 11);   
        }
        
        //extraer los datos de la persona del objeto de consulta en formato array asociativo
        $persona = mysqli_fetch_assoc($consulta);

        echo "Nombre de la persona consultada: $persona[nombre] $persona[apellidos]";

        echo '<pre>';
        print_r($persona);
        echo '<pre>';
        
    } catch (Exception $e) {
        echo $e->getCode() . ' ' . $e->getMessage();
    }

?>