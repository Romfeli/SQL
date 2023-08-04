<?php 
    //insertar datos en banco
    
    try {
        //conexión a la base de datos
        require("00_conexionbanco.php");

        //confeccionar la sentencia SQL
        $sql = "INSERT INTO personas 
                VALUES(NULL, '10000009B', 'Frank', 'Green', 'av katana, 69, '12345678', 'frank@mail.com', DEFAULT)";

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
        $idpersona = $conexionBanco->insert_id;
        
        //mensaje de alta efectuada
        echo "Alta persona efectuada con id $idpersona";

    } catch (Exception $error) {
        //mostrar código  y mensaje de error
        echo $error->getCode() . ' ' . $error->getMessage();
    }

?>