<?php

class Header {
    /* @var Mysql */

    private $db;
    /* @var Generales */
    private $gn;

    CONST OPCION_NAV = 0;
    CONST ESTADO_ACTIVO = 1;

    function __construct($sesion, $interlocutor_configuracion, $interlocutor_saldo, $db, $gn) {
        if ($sesion) {
            
        }
        $this->db = $db;
        $this->gn = $gn;
        $opciones = $gn->get_data_loged('opciones_menu');
        $id_interlocutor = $this->gn->get_data_loged('id_interlocutor');
        $id_usuario = $this->gn->get_data_loged('id_usuario');
        $marcablanca = $this->gn->get_data_loged('marca_blanca');

        $perfil = $interlocutor_configuracion['perfil'];
        $imagen_logo = $id_interlocutor . '.jpg';
        $imagen_perfil = $interlocutor_configuracion['imagen_perfil'];
        $clase = $interlocutor_configuracion['interlocutor_clase_id'];

        $tema_interlocutor = $this->db->selectOne('t.descripcion', 'fw_tema t,fw_interlocutor_condicion i', 'i.tema_id=t.id_tema and i.interlocutor_id=' . $id_interlocutor);

        require_once("fw/template/standard/view/header.html.php");
        HeaderHTML::home($sesion, $imagen_logo, $imagen_perfil, $interlocutor_configuracion['nombre_usuario'], $interlocutor_saldo, $clase, $perfil, $opciones, $tema_interlocutor, $marcablanca, $id_usuario);
    }

}
