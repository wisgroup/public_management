<?php

class Subopcion extends Modulo {

    public $tipo_contenido = 'html';

    CONST TODOS_PERFILES = 0;

    function home() {
        $parametros = $this->gn->traer_parametros('GET');
        $clase = $this->gn->get_data_loged('clase');
        $id_usuario = $this->gn->get_data_loged('id_usuario');
        $perfil_usuario = $this->db->selectOne('usuario_perfil_id as perfil', 'fw_usuario', 'id_usuario=' . $id_usuario);
        $perfil = $perfil_usuario['perfil'];
        $tipo_negocio = $this->gn->get_data_loged('tipo_negocio');

        if ($parametros->id == MODULO_OPCIONES_USUARIO) {
            $subopciones = $this->WISQueries->ejecutarQuery('consultaOpcionesFW', array('id_opcion' => $parametros->id));
        } else {
            $subopciones = $this->WISQueries->ejecutarQuery('consultaOpcionesUsuario', array('id' => $parametros->id, 'clase' => $clase, 'perfil' => $perfil, 'tipo_negocio' => $tipo_negocio));
        }
        
        $subopts = $subopciones;
        $subopciones = array();
        foreach ($subopts as $opt) {
            $subopciones[$opt['id_opcion']]= $opt;
        }
        $this->gn->set_data_loged('subopciones', $subopciones);

        if (isset($subopciones) && $subopciones != '') {
            require_once($this->get_view_path());
            //require_once ("fw/pack/base/subopcion/view/subopcion.html.php");
            SubopcionHTML::home($subopciones, $clase);
        } else {
            //if() tengo que distinguir entre transacciones e inventario fisico
            if ($parametros->id == 2) {
                header('location:?opcion=' . 'transacciones');
            } elseif ($parametros->id == 3) {
                header('location:?opcion=' . 'inventarioFisico');
            } elseif ($parametros->id == 15) {
                header('location:?opcion=' . 'mesas_visual' . '&type=ajax');
            }
        }
    }

}
