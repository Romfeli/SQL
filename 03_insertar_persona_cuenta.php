<?php 
    //insertar datos en banco
    
    try {
        //recuperar datos de un formulario hipotético. Para evitar problemas con los apóstrofes deberiamos escapar los carácteres especiales con addslashes()
        $nif        = addslashes("34220000C");
        $nombre     = addslashes("Tucco");
        $apellido   = addslashes("Beneditto");
        $direccion  = addslashes("sad hill, 22");
        $telefono   = addslashes('12345690');
        $email      = null;
        if (empty($email)) {
            $email = 'NULL';
        } else {
            $email = "'$email'";
        }
        
        //conexión a la base de datos
        require("00_conexionbanco.php");

        //confeccionar la sentencia SQL
        $sql = "INSERT INTO personas
                VALUES(NULL, '$nif', '$nombre', '$apellido', '$direccion', '$telefono', $email, DEFAULT)";

        //trasladar la sentencia sql al SGBD para que se ejecute (variable con los parámetros de conexión, variable con la sentencia sql)
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
        $cuenta     = '1234567899';
        $saldo      = 250.60;

        //confeccionar la sentencia SQL de insert en la tabla de Cuentas
        $sql = "INSERT INTO cuentas
                VALUES (NULL, '$entidad', '$oficina', '$dc', '$cuenta', '$saldo', DEFAULT, $idpersona)";

        if (!mysqli_query($conexionBanco, $sql)) {
            //detectar si la cuenta ya existe en la tabla
            if (mysqli_errno($conexionBanco) == 1062) {
                throw new Exception("Cuenta ya existe en la base de datos", 80);
            }

            //cualquier otro error lanzamos la excepción con el texto del error que nos devuelva el SGBD
            throw new Exception(mysqli_error($conexionBanco), 89);  
        }

        //mensaje de alta efectuada
        echo "Alta de persona y cuenta efectuadas";

    } catch (Exception $error) {
        //mostrar código  y mensaje de error
        echo $error->getCode() . ' ' . $error->getMessage();
    }

?>