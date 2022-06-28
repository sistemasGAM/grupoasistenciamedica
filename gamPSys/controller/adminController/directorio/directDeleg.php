<?php 
$data = (isset($_GET['id']) ? $_GET['id'] : null);
$var = (isset($_GET['vars']) ? $_GET['vars'] : null);
$est = (isset($_GET['est']) ? $_GET['est'] : null);

require_once('../../../model/requests.php'); 
$object = new request;
$object -> selectEst($data,$var,$est);
?>