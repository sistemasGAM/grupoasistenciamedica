<?php 
$data = (isset($_GET['id']) ? $_GET['id'] : null);
require_once('../../../model/requests.php'); 
$object = new request;
$object -> selectEstate($data);
?>