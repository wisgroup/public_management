<?php

if (!class_exists('Maestro')) {
    require_once(PATH_PLUGIN_MAESTRO);
}

class Cliente extends Interlocutor {

    public $tipo_contenido = 'html';

    public function __construct($db = "", $modulo = "") {
        $this->modulo = $modulo;
        $this->actualizacion_ajax = 'ajax';
        parent::__construct($db, $this->modulo);
        $this->nombre_tabla = FW_PREFIJO.'interlocutor';
        $this->primary_key = 'id_interlocutor';
        $id_interlocutor = $this->gn->get_data_loged('id_interlocutor');
        $campos_formulario[$this->primary_key] = array('tipo' => 'hidden', 'valor' => '', 'complemento' => '');
        $campos_formulario['nickname'] = array('tipo' => 'hidden', 'valor' => '');
        $campos_formulario['nombre'] = array('tipo' => 'text', 'valor' => '', 'complemento' => 'required');
        $campos_formulario['apellido'] = array('tipo' => 'text', 'valor' => '', 'complemento' => '');
        $campos_formulario['interlocutor_tipo_negocio_id'] = array('label' => 'Negocio', 'tipo' => 'hidden', 'valor' => '1');
        $campos_formulario['interlocutor_clase_id'] = array('tipo' => 'hidden', 'valor' => CLASE_CLIENTE, 'complemento' => '');
        $campos_formulario['interlocutor_id'] = array('tipo' => 'hidden', 'valor' => $id_interlocutor, 'complemento' => '');
        $campos_formulario['tipo_documento'] = array('tipo' => 'select', 'valor' => '', 'complemento' => '', 'opciones' => array('cedula' => 'Cedula', 'nit' => 'NIT'), 'tag' => 'select');
        $campos_formulario['num_documento'] = array('tipo' => 'text', 'valor' => '', 'complemento' => 'required minlength="8"', 'label'=>'Número Documento');
        $campos_formulario['direccion'] = array('tipo' => 'text', 'valor' => '', 'complemento' => '', 'label'=>'Dirección');
        $campos_formulario['celular'] = array('tipo' => 'number', 'valor' => '', 'complemento' => ' minlength="10" maxlength="15"');
        $campos_formulario['telefono'] = array('tipo' => 'number', 'valor' => '', 'complemento' => ' minlength="7" maxlength="10"', 'label'=>'Teléfono');
        $campos_formulario['estado_id'] = array('tipo' => 'select', 'valor' => '', 'tabla' => 'fw_estado', 'complemento' => '', 'campo' => 'descripcion', 'condicion' => 'id_estado IN (1,2)', 'tag' => 'select');
        $campos_formulario['orden'] = array('tipo' => 'hidden', 'valor' => '0');
        $campos_formulario['descripcion'] = array('tipo' => 'textarea', 'valor' => '', 'label'=>'Descripción');
        $campos_formulario['email'] = array('tipo' => 'email', 'valor' => '', 'complemento' => "");
        $campos_formulario['confirmar_email'] = array('tipo' => 'hidden');
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
