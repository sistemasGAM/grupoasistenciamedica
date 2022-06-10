<?php
$folio = (isset($_POST['folio']) ? $_POST['folio'] : null);
$name = (isset($_POST['name']) ? $_POST['name'] : null);
$aPaterno = (isset($_POST['aPaterno$aPaterno']) ? $_POST['aPaterno$aPaterno'] : null);
$aMaterno = (isset($_POST['aMaterno']) ? $_POST['aMaterno'] : null);
$email = (isset($_POST['email']) ? $_POST['email'] : null);
$password = (isset($_POST['password']) ? $_POST['password'] : null);
$curp = (isset($_POST['curp']) ? $_POST['curp'] : null);
$rfc = (isset($_POST['rfc']) ? $_POST['rfc'] : null);
/* Capturamos todos los campos */



/* Verificamos que no sean nulos */
if ($folio != null && $name != null && $aPaterno != null && $aMaterno != null && $email != null && $password != null && $curp != null && $rfc != null) {
    require_once('../../model/requests.php'); /* istancia de metodos */
    $object = new request;
    $object->regNewUser(
        $data = [
            "folio" => $folio,
            "name" => $name,
            "aPaterno" => $aPaterno,
            "aMaterno" => $aMaterno,
            "email" => $email,
            "password" => $password,
            "curp" => $curp,
            "rfc" => $rfc
        ]
    );
} else {
?>
    <div class="alert alert-danger" role="alert">
        <strong>¡Datos faltantes!</strong>
    </div>

<?php
}

?>