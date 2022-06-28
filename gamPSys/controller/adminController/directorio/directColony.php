
<?php 
$data = (isset($_GET['id']) ? $_GET['id'] : null);
$var = (isset($_GET['vars']) ? $_GET['vars'] : null);
require_once('../../../model/requests.php'); 
$object = new request;
$object -> selectMun($data,$var);
?>