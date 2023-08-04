<?php 
    //modificar datos con UPDATE

    try {
        //fichero de conexión
        require('00_conexionbanco.php');

        //recuperar datos
        $nif        = addslashes("10000212C");
        $nombre     = addslashes("Vernita");
        $apellidos  = addslashes("Green");
        $direccion  = addslashes("malvavisco, 89");
        $telefono   = addslashes("981111543");
        //$email      = addslashes("verni@mail.com");
        $email      = null;
        /*
        if (empty($email)) {
            $email = 'NULL';
        } else {
            $email = "'$email'";
        }
        */
        //mismo if pero utilizando el if ternario
        $email  =  empty($email) ? 'NULL' : "'$email'";

        $idpersona  = 6;

        //confeccionar la sentencia SQL
        $sql = "UPDATE personas
                SET nif = '$nif', nombre = '$nombre', apellidos = '$apellidos', direccion = '$direccion', telefono = '$telefono', email = $email
                WHERE idpersona = $idpersona";
        
        //trasladar la sentencia al SGBD y controlar los errores
        if (!mysqli_query($conexionBanco, $sql)) {
            //comprobar si el error se produce al intentar insertar una fila con una clave duplicada(nif o email)
            if (mysqli_errno($conexionBanco) == 1062) {
                //discriminar si la clave duplicada es el mail o el nif

                //1 obtener el texto de error
                $textoerror = mysqli_error($conexionBanco);
                
                //2 buscar la clave (nif o email) en el texto
                if (stripos($textoerror, 'nif')) {
                    $clave = 'nif';
                } else {
                    $clave = 'email';
                }

                //3 mostrar el error de clave duplicada indicando si es el nif o el email
                throw new Exception("No pueden existir dos personas con mismo $clave", 91);
            }

            //para cualquier otro error lanzar una excepción genérica o una excepción con el mensaje de error del SGBD
            throw new Exception(mysqli_error($conexionBanco), 90);

            //throw new Exception("Ha ocurrido un error al acceder a la base de datos", 90);

            //para que el equipo de desarrollo pueda tener datos sobre la excepción del SGBD suele grabarse un fichero con el código, texto hora de la excepción y nombre de la tabla
        }

        //comprobar si se ha modificado alguna fila
        if (mysqli_affected_rows($conexionBanco) == 0) {
            throw new Exception("Persona a modificar no existe o no se han modificado datos", 20);
        }

        //mensaje de modificación efectuada
        echo "modificación efectuada";
        
    } catch (Exception $e) {
        echo $e->getMessage();
    }

?>