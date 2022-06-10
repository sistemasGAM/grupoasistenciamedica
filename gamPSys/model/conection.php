<?php
class conection
{

    public function __construct()
    {
        /*Datos de conexion a la base de datos*/
        $db_host = "localhost";
        $db_user = "u199109938_Desarrollo"; /*USER  u199109938_Desarrollo*/
        $db_pass = " 4n##gF:#UH";
        $db_name = "u199109938_GAMCRUD"; /* u199109938_GAMCRUD*/

        return $con = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

        if (mysqli_connect_errno()) {
            echo 'No se pudo conectar a la base de datos : ' . mysqli_connect_error();
        }
    }
}
