<?php

require_once('controlador/php/modulos/maestro.class.php');



class UsuarioDatos extends Maestro {



    public $tipo_contenido = 'html';

    CONST ERROR_EDICION = "";

    CONST ESTADO_ACTIVO = 1;

    CONST ESTADO_BLOQUEADO = 2;

    CONST ESTADO_ELIMINADO = 3;

   

    function home() {

        

        $this->tipo_contenido = 'ajax';

        $interlocutor = $this->gn->get_data_loged('id_interlocutor');

       // print_r($interlocutor);exit;

        $datos_usuario = $this->db->selectone(array('CONCAT(i.nombre," ",i.apellido) empresa','ic.descripcion clase','CONCAT(u.nombre," ",u.apellido) usuario','up.nombre perfil','CONCAT("", FORMAT(s.mensajes, 0)) mensajes'), 

            'fw_interlocutor i, fw_interlocutor_clase ic, fw_usuario u, fw_usuario_perfil up, saldo s',

            "i.id_interlocutor = u.interlocutor_id AND up.id_usuario_perfil = u.usuario_perfil_id AND i.interlocutor_clase_id = ic.id_interlocutor_clase AND u.usuario_perfil_id = up.id_usuario_perfil AND id_interlocutor = ".$interlocutor);

      // print_r($datos_usuario); exit;

        require_once("vista/html/usuario_datos.html.php");

        UsuarioDatosHTML::home($datos_usuario);

       }

    }

