<?php

class Nav {

    private $db;

    CONST NAV_OPCION = 0;
    CONST NAV_ORDER = ' o.orden ASC';

    function __construct($db, $gn) {
        $this->db = $db;
        //$opciones = $gn->get_data_loged('opciones_menu');
        $perfil = $gn->get_data_loged('perfil');
        $clase= $gn->get_data_loged('clase');
        
        
        if (!isset($opciones) || empty($opciones)) {
            $opciones = $this->db->select(array('o.id_opcion', 'o.descripcion', 'o.nombre_modulo', 'o.imagen'), 
                    'fw_opcion o, fw_opcion oo, fw_opcion_clase oc', 
                    " (o.id_opcion = oo.opcion_id "
                    . " AND oo.id_opcion = oc.opcion_id "
                    . " AND o.opcion_id=" . self::NAV_OPCION 
                    . " AND oc.interlocutor_clase_id = " . $clase . " "
                    . " AND oc.usuario_perfil_id = " . $perfil . " "
                    . " AND o.estado_id = " . ESTADO_ACTIVO . " "
                    . " AND oc.estado_id = " . ESTADO_ACTIVO . " "
                    . " AND o.estado_id = " . ESTADO_ACTIVO . " "
                    . " AND oo.estado_id = " . ESTADO_ACTIVO . ") OR (o.id_opcion = 27) GROUP BY o.id_opcion ",
                    self::NAV_ORDER, false, false);
            $gn->set_data_loged('opciones_menu', $opciones);
        }
        require_once("fw/template/standard/view/nav.html.php");
        NavHTML::home($opciones);
    }

}
