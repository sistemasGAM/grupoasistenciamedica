<?php
/* Capturamos todos los campos */
$noAut = (isset($_POST['noAut']) ? $_POST['noAut'] : null);
$noProvee = (isset($_POST['noProvee']) ? $_POST['noProvee'] : null);

/* Verificamos que no sean nulos */
if ($noAut != null && $noProvee != null) {
    require_once('../../model/requests.php'); /* istancia de metodos */
    $object = new request;
    $object->regNewUser(
        $data = [
            "noAut" => $noAut,
            "noProvee" => $noProvee
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