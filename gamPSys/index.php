<?php
session_start();
require_once('model/app.php');
$view = new app;
$view = $view->view();
