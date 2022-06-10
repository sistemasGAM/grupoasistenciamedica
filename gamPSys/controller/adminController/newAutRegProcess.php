<?php 
$id = (isset($_GET['id']) ? $_GET['id'] : null);
$numAut = (isset($_GET['numAut']) ? $_GET['numAut'] : null);
$numProv = (isset($_GET['numProv']) ? $_GET['numProv'] : null);
$nom = (isset($_GET['nom']) ? $_GET['nom'] : null);

require_once('../../model/requests.php'); /* istancia de metodos */
$object = new request;
$object -> newAutRegCon($id);

?>