<?php 
    //insertar datos en banco
    
    try {
        //recuperar datos de un formulario hipotético. Para evitar problemas con los apóstrofes deberiamos escapar los carácteres especiales con addslashes()
        $nif        = addslashes("34334440C");
        $nombre     = addslashes("Connie");
        $apellido   = addslashes("Corleone");
        $direccion  = addslashes("sad hill, 22");
        $telefono   = addslashes('12345690');
        $email      = null;
        if (empty($email)) {
            $email = 'NULL';
        } else {
            $email = "'$email'";
        }
        
        //conexión a la base de datos
        require("00_conexionbanco_dos.php");

        //activar entorno transaccional para que se realicen los tres insert en la base de datos como un todo
        mysqli_autocommit($conexionBanco, FALSE);

        //confeccionar la sentencia SQL
        $sql = "INSERT INTO personas
                VALUES(NULL, '$nif', '$nombre', '$apellido', '$direccion', '$telefono', $email, DEFAULT)";

        //trasladar la sentencia sql al SGBD para que se ejecute (variable con los parámetros de conexión, variable con la sentencia sql)
        if (!mysqli_query($conexionBanco, $sql)) {
            /*
            echo '<pre>';
            print_r($conexionBanco);
            echo '</pre>';
            */
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

            //mostrar error genérico o literalmente el error que nos devuelva el SGBD
            //throw new Exception('Se ha producido un error al insertar el dato', 90);
            throw new Exception(mysqli_error($conexionBanco), mysqli_errno($conexionBanco));
        }

        //recuperar el id de la clave primaria que el SGBD ha asignado a la persona que acabamos de dar de alta
        //$idpersona = $conexionBanco->insert_id;
        $idpersona = mysqli_insert_id($conexionBanco);

        //insertar una cuenta en la tabla 'Cuentas'
        $entidad    = '0019';
        $oficina    = '0200';
        $dc         = '12';
        $cuenta     = '1234562222';
        $saldo      = 250.60;

        //confeccionar la sentencia SQL de insert en la tabla de Cuentas
        $sql = "INSERT INTO cuentas
                VALUES (NULL, '$entidad', '$oficina', '$dc', '$cuenta', '$saldo', DEFAULT)";

        if (!mysqli_query($conexionBanco, $sql)) {
            //detectar si la cuenta ya existe en la tabla
            if (mysqli_errno($conexionBanco) == 1062) {
                throw new Exception("Cuenta ya existe en la base de datos", 80);
            }

            //cualquier otro error lanzamos la excepción con el texto del error que nos devuelva el SGBD
            throw new Exception(mysqli_error($conexionBanco), 89);  
        }

        //recuperar el id de la cuenta que acabamos de dar de alta
        $idcuenta = $conexionBanco->insert_id;

        //confeccionar la sentencia sql para la tabla de vinculación
        $sql = "INSERT INTO personas_cuenta
                VALUES ($idpersona, $idcuenta)";

        //trasladar la sentencia SQL al SGBD para que se ejecute
        if (!mysqli_query($conexionBanco, $sql)) {
            if (mysqli_errno($conexionBanco) == 1062) {
                throw new Exception("Relación persona cuenta ya existe", 80);   
            }

            throw new Exception(mysqli_error($conexionBanco), 94);
        }

        //traslade los cambios de forma definitiva a la base de datos
        mysqli_commit($conexionBanco);

        //mensaje de alta efectuada
        echo "Alta de persona y cuenta efectuadas";

    } catch (Exception $error) {
        //mostrar código  y mensaje de error
        echo $error->getCode() . ' ' . $error->getMessage();
    }

?>