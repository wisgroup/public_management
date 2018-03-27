<?php

require_once('fw/controlador/php/modulos/modulo.class.php');



class Aside extends Modulo{

    

    public $tipo_contenido = 'html';

    protected $db;

    

    function __construct($interlocutor_configuracion = '',$db = '',$gn = '') {

        if($db != ''){

            $this->home($interlocutor_configuracion,$db,$gn);

        }

    }

    function home($interlocutor_configuracion = '',$db = '',$gn = '') {

        if($db != ''){

            $this->db = $db;

            $subopciones = $this->db->select(array('s.id_opcion','s.descripcion','s.titulo'),'fw_opcion s'," s.estado_id = 3");	

        }

        //$opciones = $this->gn->traer_parametros('GET');

        //echo "<pre>"; print_r($opciones); //exit;

        $subopciones[0]['selected'] = true;   

       

        require_once("fw/vista/html/aside.html.php");

        AsideHTML::informacion_adicional($subopciones);

    	

    }

    function cargar_contenidos(){

         $subopciones = $this->db->select(array('s.titulo'),'fw_opcion s'," s.estado_id = '3'");	

         $d = "|";

         foreach ($subopciones as $value) {

             $d .= $value['titulo'].'|';

         }

         echo $d;

    }

  }



?>



