<?php 
    //borrar datos con DELETE

    try {
        //conexion a la base datos
        require('00_conexionbanco.php');

        //recuperar la persona a dar de baja
        $nif = "34220000C"; //acceso por nif
        $idpersona = 50; //acceso por PK (mucho más rápido)

        //confeccionar la sentecia SQL
        //$sql = "DELETE FROM personas WHERE nif = '$nif'";
        $sql = "DELETE FROM personas WHERE idpersona = $idpersona";

        //trasladar la sentencia SQL al SGBD y comprobar los errores
        if (!mysqli_query($conexionBanco, $sql)) {
            //comprobar si la persona tiene cuentas asociadas
            if (mysqli_errno($conexionBanco) == 1451) {
                throw new Exception("Persona con cuentas asociadas no se puede borrar", 20);
            }

            //para cualquier otra excepción mostrar un mensaje genérico o el mensaje del SGBD
            throw new Exception(mysqli_error($conexionBanco), mysqli_errno($conexionBanco));
        }

        //comprobar si se ha borrado alguna fila
        if (mysqli_affected_rows($conexionBanco) == 0) {
            throw new Exception("La persona a borrar no existe", 20);
        }

        //mensaje de baja efectuada
        echo "Baja persona efectuada";
    
    } catch (Exception $e) {
        echo $e->getCode() . ' ' . $e->getMessage();
    }

?>