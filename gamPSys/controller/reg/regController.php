<?php
$user = (isset($_POST['user']) ? $_POST['user'] : null);
$email = (isset($_POST['email']) ? $_POST['email'] : null);
$passOne = (isset($_POST['passOne']) ? $_POST['passOne'] : null);
$passTwo = (isset($_POST['passTwo']) ? $_POST['passTwo'] : null);
$nomina = (isset($_POST['nomina']) ? $_POST['nomina'] : null);
/* Capturamos todos los campos */



/* Verificamos que no sean nulos */
if ($user != null && $email != null && $passOne != null && $passTwo != null && $nomina != null) {
    require_once('../../model/requests.php'); /* istancia de metodos */
    $object = new request;
    $object->regNewUser(
        $data = [
            "user" => $user,
            "email" => $email,
            "passOne" => $passOne,
            "passTwo" => $passTwo,
            "nomina" => $nomina
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