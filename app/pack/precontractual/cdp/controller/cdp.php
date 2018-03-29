<?php

class Cdp extends Modulo {

    public $tipo_contenido = 'html';

    public function __construct($db = "", $modulo = "") {
        $this->modulo = $modulo;
        $this->actualizacion_ajax = 'ajax';
        parent::__construct($db, $this->modulo);
        $this->nombre_tabla = FW_PREFIJO.'interlocutor';
        $this->primary_key = 'id_interlocutor';
        $id_interlocutor = $this->gn->get_data_loged('id_interlocutor');
        $campos_formulario['estado_id'] = array('tipo' => 'hidden', 'valor'=>ESTADO_ACTIVO);
        $this->campos_formulario = $campos_formulario;
    }

    function home() {
        $interlocutor_id_actual = $this->gn->get_data_loged('id_interlocutor'); //id del Interlocutor actual
        $campos = array('i.' . $this->primary_key . " AS empty", 'i.num_documento AS documento', "CONCAT(i.nombre,' ',i.apellido) AS nombre", 'i.email', "est.descripcion AS Estado");
        $tablas = $this->nombre_tabla . ' i, fw_estado est, fw_interlocutor_condicion itc';
        $condicion = 'itc.interlocutor_id=' . $interlocutor_id_actual . ''
                . ' AND i.interlocutor_clase_id = '.CLASE_CLIENTE
                . ' AND i.id_interlocutor <>' . $interlocutor_id_actual . ' '
                . ' AND i.estado_id <> ' . ESTADO_ELIMINADO . ' '
                . ' AND i.estado_id=est.id_estado';
        $this->iniciar_maestro($campos, $tablas, $condicion);
    }
}
