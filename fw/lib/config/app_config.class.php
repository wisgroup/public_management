<?php

class App_Config {

    private $host = 'localhost';
    private $usuario = 'root';
    /* CLAVE PARA DESARROLLO */
    private $clave = '';
    /* CLAVE PARA PRODUCCION */
    //private $clave = 'JDJ5JDEwJHp1YVF2Z2Jab1A4OHdXZGhhUUJvMHU1akNTQnVuMEJzNmNJMi9laG9aa2lxNnlFaUxlclUu';
    private $base_datos = 'wis_fw_siac_v0';

    /* GENERAL */
    private $session = 'wis_fw_siac';
    private $site_title = "SIAC";

    /* MAILING */
    private $nombre_from = "";
    private $email_from = "info@domain.com";
    private $live_site = "http://localhost/";

    function __construct() {
        
    }

    function _get($var) {
        return $this->$var;
    }

    function _set($var, $dato) {
        return $this->$var = $dato;
    }

}
