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
        $campos_formulario[$this->primary_key] = array('tipo' => 'hidden', 'valor' => '', 'complemento' => 'required');
        $campos_formulario['nickname'] = array('tipo' => 'text', 'valor' => '', 'complemento' => 'required');
        $campos_formulario['nombre'] = array('tipo' => 'text', 'valor' => '', 'complemento' => 'required');
        $campos_formulario['apellido'] = array('tipo' => 'text', 'valor' => '', 'complemento' => 'required');
        $campos_formulario['interlocutor_tipo_negocio_id'] = array('label' => 'Negocio', 'tipo' => 'select', 'valor' => '', 'tabla' => 'fw_interlocutor_tipo_negocio', 'complemento' => 'required', 'campo' => 'nombre', 'tag' => 'select');
        $campos_formulario['interlocutor_clase_id'] = array('tipo' => 'select', 'valor' => '', 'complemento' => 'required', 'tabla' => 'fw_interlocutor_clase', 'campo' => 'nombre', 'condicion' => 'id_interlocutor_clase IN (2,3)', 'tag' => 'select');
        $campos_formulario['interlocutor_id'] = array('tipo' => 'hidden', 'valor' => $id_interlocutor, 'complemento' => 'required');
        $campos_formulario['tipo_documento'] = array('tipo' => 'select', 'valor' => '', 'complemento' => 'required', 'opciones' => array('cedula' => 'Cedula', 'nit' => 'NIT'), 'tag' => 'select');
        $campos_formulario['num_documento'] = array('tipo' => 'text', 'valor' => '', 'complemento' => 'required minlength="8"');
        $campos_formulario['direccion'] = array('tipo' => 'text', 'valor' => '', 'complemento' => 'required');
        $campos_formulario['celular'] = array('tipo' => 'number', 'valor' => '', 'complemento' => 'required minlength="10" maxlength="15"');
        $campos_formulario['telefono'] = array('tipo' => 'number', 'valor' => '', 'complemento' => 'required minlength="7" maxlength="10"');
        $campos_formulario['estado_id'] = array('tipo' => 'select', 'valor' => '', 'tabla' => 'fw_estado', 'complemento' => 'required', 'campo' => 'descripcion', 'condicion' => 'id_estado IN (1,2)', 'tag' => 'select');
        $campos_formulario['orden'] = array('tipo' => 'number', 'valor' => '', 'complemento' => 'required', 'campo' => 'descripcion', 'label' => 'orden');
        $campos_formulario['descripcion'] = array('tipo' => 'textarea', 'valor' => '');
        $campos_formulario['email'] = array('tipo' => 'email', 'valor' => '', 'complemento' => "required onchange='validar_email_usuario()'");
        $campos_formulario['confirmar_email'] = array('tipo' => 'email', 'valor' => '', 'complemento' => "required onchange='validar_email_usuario()'", 'personalizado');
        $this->campos_formulario = $campos_formulario;
    }

    function home() {
        $marcablanca = $this->gn->get_data_loged('marca_blanca');
        $interlocutor_id_actual = $this->gn->get_data_loged('id_interlocutor'); //id del Interlocutor actual
        $campos = array('i.' . $this->primary_key . " Codigo", 'i.num_documento as documento', "concat(i.nombre,' ',i.apellido) as nombre", "itn.nombre as Negocio", 'i.email', "est.descripcion as Estado");
        $tablas = $this->nombre_tabla . ' i, fw_interlocutor_tipo_negocio itn, fw_estado est, fw_interlocutor_condicion itc';
        $condicion = 'itc.interlocutor_id=' . $interlocutor_id_actual . ''
                . ' AND i.interlocutor_clase_id = '.CLASE_CLIENTE
                . ' AND i.id_interlocutor <>' . $interlocutor_id_actual . ' '
                . ' AND i.estado_id <> ' . ESTADO_ELIMINADO . ' '
                . ' AND itn.id_interlocutor_tipo_negocio=i.interlocutor_tipo_negocio_id '
                . ' AND i.estado_id=est.id_estado';
        $this->iniciar_maestro($campos, $tablas, $condicion);
    }
}
