<?php 
$numAut = (isset($_POST['origen']) ? $_POST['origen'] : null);
$numProv = (isset($_POST['provedor']) ? $_POST['provedor'] : null);
$numAutEsp = (isset($_POST['especial']) ? $_POST['especial'] : null);
$nom = (isset($_POST['nom']) ? $_POST['nom'] : null);

require_once('../../model/requests.php'); /* istancia de metodos */
$object = new request;
$object -> addNewAutEspProcess($numAut,$numAutEsp,$numProv,$nom);

?>