<?php 
    //las consultas por clave no única nos entregarán una fila o varias filas o ninguna (en caso que la clave no exista)

    try {
        //incorporar fichero de conexión
        require('00_conexionbanco.php');

        //confeccionar la sentencia SQL
        
        //seleccionar todas las columnas de todas las filas
        //$sql = "SELECT * FROM personas";

        //consultar las filas cuyo email esté sin informar (el email es clave única siempre que esté informado)
        $sql = "SELECT * FROM personas WHERE email IS NULL";

        //seleccionar todas las columnas de todas las filas ordenadas por nombre y apellidos
        //$sql = "SELECT * FROM personas ORDER BY nombre, apellidos";

        //trasladar la sentencia al SGBD
        if (!$consulta = mysqli_query($conexionBanco, $sql)) {
            throw new Exception(mysqli_error($conexionBanco), 10);
        }

        //comprobar si la consulta nos devuelve alguna fila
        if ($consulta->num_rows == 0) {
            throw new Exception("No existen datos en la tabla", 11);   
        }
        
        //extraer los datos de la/s persona/s del objeto de consulta en formato array escalar-asociativo
        $personas = mysqli_fetch_all($consulta, MYSQLI_ASSOC);

        echo '<pre>';
        print_r($personas);
        echo '<pre>';

        //Mostrar los datos de la primera persona
        echo "Nombre de la primera persona consultada: {$personas[0]['nombre']} {$personas[0]['apellidos']}";

        echo '<hr>';
        
        //mostrar por pantalla una fila por persona con la siguiente info: 
        //<b>nif</b><p>nombre apellidos direccion</p><hr>
        foreach ($personas as $persona) {
            echo "<b>$persona[nif]</b><p>$persona[nombre] $persona[apellidos] $persona[direccion]</p><hr>";
        }
        
    } catch (Exception $e) {
        echo $e->getCode() . ' ' . $e->getMessage();
    }

?>