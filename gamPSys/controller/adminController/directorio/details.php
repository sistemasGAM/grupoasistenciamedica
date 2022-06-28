<?php 
$select1 = (isset($_GET['select1']) ? $_GET['select1'] : null);
$select2 = (isset($_GET['select2']) ? $_GET['select2'] : null);
$select3 = (isset($_GET['select3']) ? $_GET['select3'] : null);
require_once('../../../model/requests.php'); 
$object = new request;
$object -> selectEst($select2, $select1, $select3);
?>