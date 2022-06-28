
<?php
$id = (isset($_GET['id']) ? $_GET['id'] : null);
require_once('../../model/requests.php'); /* istancia de metodos */
$object = new request;
$object -> viewListProver($id);
?>