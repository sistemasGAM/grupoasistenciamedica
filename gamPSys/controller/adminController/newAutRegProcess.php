<?php 
$numAut = (isset($_POST['numAut']) ? $_POST['numAut'] : null);
$numProv = (isset($_POST['numProv']) ? $_POST['numProv'] : null);
$nom = (isset($_POST['nom']) ? $_POST['nom'] : null);

require_once('../../model/requests.php'); /* istancia de metodos */
$object = new request;
$object -> addNewAutProcess($numAut,$numProv,$nom);

?>