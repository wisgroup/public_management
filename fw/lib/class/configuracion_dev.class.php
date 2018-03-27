<?php
class Configuracion{
    
    /* BASE DE DATO */
    private $host = 'localhost';
    private $usuario = 'root';
    private $clave = '';
    private $base_datos = 'framework_wis_v0000';

    /* GENERAL */
    private $session = 'framework_wis';
    private $site_title = "MOSS: WIS Framework";
    
    /* MAILING */
    private $nombre_from = "MOSS";
    private $email_from = "info@comred.com";
    private $live_site = "http://www.wisgroup.com.co/apps/wis_fw/";
    private $live_site1 = "http://www.wisgroup.com.co/apps/wis_fw/";
    
    function __construct() {

    }

    function _get($var){
        return $this->$var;
    }

    function _set($var, $dato ){
        return $this->$var = $dato;
     }
}