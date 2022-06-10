<?php
class app{

    public function __construct()
    {
        require_once('model/routes.php');
    }
    public function view(){
         /* Funcion de platillas */
         $ruteClass = new routes;
         $print = $ruteClass->route();
         return $print;
    }
}
?>