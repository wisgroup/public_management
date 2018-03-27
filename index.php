<?php 
    ini_set("display_errors",1);
    ini_set("html_errors",0);
    ini_set('max_execution_time', 400);
    date_default_timezone_set('America/Bogota');
    session_start();
    require_once 'fw/main.class.php';
    $main = new Main();