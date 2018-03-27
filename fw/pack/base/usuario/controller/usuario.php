<?php

class Usuario extends Maestro {

    public function __construct($db = "", $modulo = "") {
        $this->actualizacion_ajax = 'ajax';
        $this->modulo = strtolower(__CLASS__);
        $this->nombre_tabla = FW_PREFIJO . strtolower(__CLASS__);
        $this->primary_key = 'id_' . strtolower(__CLASS__);
        //$this->instancia_modulo='fw';

        $this->encabezado_edicion = array(array('id_usuario', 'concat(nombre," ",apellido)'), 'usuario', 'id_usuario=%s');
        parent::__construct($db, $this->modulo);
        $marca_blanca = $this->gn->get_data_loged('marca_blanca');
        $id_interlocutor = $this->gn->get_data_loged('id_interlocutor');

        $owner_filtro = "( marca_blanca = " . $marca_blanca . " OR marca_blanca = " . self::WIS_OWNER . ")";

        $campos_formulario[$this->primary_key] = array('tipo' => 'hidden', 'valor' => '', 'complemento' => 'required');
        $campos_formulario['interlocutor_id'] = array('tipo' => 'hidden', 'valor' => $id_interlocutor, 'complemento' => 'required');
        $campos_formulario['usuario_perfil_id'] = array('tipo' => 'select', 'valor' => '', 'complemento' => 'required', 'tabla' => 'fw_usuario_perfil', 'campo' => 'nombre', 'condicion' => ' estado_id = ' . ESTADO_ACTIVO . " AND " . $owner_filtro, 'tag' => 'select');
        $campos_formulario['nickname'] = array('tipo' => 'text', 'valor' => '', 'complemento' => "required onchange='validar_nickname_usuario()'");
        $campos_formulario['nombre'] = array('tipo' => 'text', 'valor' => '', 'complemento' => 'required');
        $campos_formulario['apellido'] = array('tipo' => 'text', 'valor' => '', 'complemento' => 'required');
        $campos_formulario['num_documento'] = array('tipo' => 'text', 'valor' => '', 'complemento' => 'required minlength="6"', 'label'=>'No. Documento');
        $campos_formulario['email'] = array('tipo' => 'email', 'valor' => '', 'complemento' => " onchange='validar_email()'");
        $campos_formulario['confirmar_email'] = array('tipo' => 'email', 'valor' => '', 'complemento' => "required onchange='validar_email_usuario()'", 'personalizado');
        $campos_formulario['clave'] = array('tipo' => 'hidden', 'valor' => '111111', 'complemento' => 'required', 'label' => 'Password');
        $this->campos_formulario = $campos_formulario;
    }

    function home() {
        $id_interlocutor = $this->gn->get_data_loged('id_interlocutor');
        $id_usuario = $this->gn->get_data_loged('id_usuario');
        $campos = array('u.' . $this->primary_key . ' AS empty', 'u.nickname', 'up.nombre AS perfil','concat(u.nombre," ",u.apellido)  as usuario', 'u.num_documento AS `No. Documento`', 'u.email', 'est.id_estado as Estado');
        $tablas = $this->nombre_tabla . " u, fw_estado est, fw_usuario_perfil up";
        $condicion = "u.estado_id=est.id_estado 
			AND u.usuario_perfil_id=up.id_usuario_perfil 
			AND u.interlocutor_id=$id_interlocutor 
			AND u.estado_id <>" . ESTADO_ELIMINADO . ' 
			AND u.id_usuario <>' . $id_usuario; //Preguntar la condicion para mostrar usuarios es que el estado no sea eliminado.
        $checkbox = array('id_estado');
        $this->iniciar_maestro($campos, $tablas, $condicion, 'Usuarios ' . date('Y-m-d H.i.s'), $checkbox, $this->modulo, $this->nombre_tabla);
    }

    function formulario_edicion($campos_formulario = null) {
        $this->tipo_contenido = '';
        $parametros = $this->gn->traer_parametros('GET');
        if ($campos_formulario === null) {
            $campos_formulario = $this->campos_formulario;
        }

        $accion = "guardar_creacion&type=html";
        $encabezado = "Crear Usuario";
        if (isset($parametros->id) && (int) $parametros->id > 0) {
            $accion = "guardar_edicion&type=html";
            $campos = array();
            unset($campos_formulario['confirmar_email']);
            foreach ($campos_formulario as $key => $input) {
                array_push($campos, $key);  //Setea el array con le nombre de los campos
            }
            $elemento = $this->db->selectone($campos, $this->nombre_tabla, $parametros->id . ' = ' . $this->primary_key); //Obtiene el valor de los campos de la tabla en  bd
            $encabezado = $parametros->id . ' ' . $elemento['nombre'] . ' ' . $elemento['apellido'];
            foreach ($elemento as $key => $value) {
                $campos_formulario[$key]['valor'] = $value;
            }
        }
        require_once(PATH_PLUGIN_MAESTRO_VIEW2);
        FormHTML::formulario_dinamico($campos_formulario, $accion, $this->modulo, $this->db, '', '', $parametros, $retorno = '', $encabezado, '', $this->actualizacion_ajax);
    }

    function guardar_creacion($redirect = true, $parametros = null, $last_id = 0) {
        $this->tipo_contenido = '';
        if (is_null($parametros)) {
            $parametros = $this->gn->traer_parametros('POST');
        }
        $params_get = $this->gn->traer_parametros('GET');
        $id_tabla = 'id_' . $this->nombre_tabla;
        unset($parametros->$id_tabla);
        $campos = array();

        foreach ($parametros as $key => $value) {
            $campos[$key] = $value;
        }
        unset($campos['confirmar_email']);
        $campos['interlocutor_id'] = $this->gn->get_data_loged('id_interlocutor');
        $clave = $campos['clave']; //$this->sx->generar_nueva_clave();
        $campos['clave'] = $this->sx->encriptar_clave($clave);
        $log = $this->registrar_log(self::LOG_CREAR, $this->nombre_tabla, $campos);
        $respuesta_edicion = $this->db->insert($campos, $this->nombre_tabla);

        if (!$respuesta_edicion) {
            $mensaje = $this->nc->traer_mensaje_respuesta($this->ERROR_CREACION);
            $this->nc->set_notificacion(utf8_encode($mensaje['descripcion']));
        } else {
            $last_id = $this->db->insertId();
            $mensaje = $this->nc->traer_mensaje_respuesta($this->EXITO_CREACION);
            $this->nc->set_notificacion(utf8_encode($mensaje['descripcion']));
        }

        if(!$redirect){
            if($last_id > 0){
                return $last_id;
            }else{
                header('location:?opcion=' . $this->modulo);
            }
        }
        $retorno = $this->gn->get_data_loged('retorno');

        if(isset($retorno) && $retorno != ''){
            $this->gn->set_data_loged('retorno', '');
            $campo = $this->gn->get_data_loged('campo');
            $this->gn->set_data_loged('campo', '');
            $this->gn->set_data_loged($campo, $last_id);
            header('location:?opcion=' . $retorno);
        }else{
            if($this->actualizacion_ajax == 'ajax'){
                $this->home();
            }else{
                header('location:?opcion=' . $this->modulo);
            }
        }
    }

    function validar_nickname(){
        $this->tipo_contenido = '';
        $parametros = $this->gn->traer_parametros('GET');
        $nickname = $this->db->select(array($this->primary_key), $this->nombre_tabla, "nickname = '$parametros->id' ");
        if($nickname){
            $disabled = '1';
        }else{
            $disabled = '0';
        }
        echo $disabled;
    }

    function editar_mi_perfil(){
        $this->nombre_tabla = 'fw_usuario';
        $this->tipo_contenido = 'ajax';
        $campos_formulario = array();
        $campos_formulario['nombre'] = array('tipo' => 'text', 'valor' => '', 'complemento' => 'required');
        $campos_formulario['apellido'] = array('tipo' => 'text', 'valor' => '', 'complemento' => 'required');
        $campos_formulario['num_documento'] = array('tipo' => 'text', 'valor' => '', 'complemento' => 'required minlength="6"');
        $id_usuario = $this->gn->get_data_loged('id_usuario');
        $campos = array('u.nombre', 'u.apellido', 'u.num_documento');
        $tablas = 'fw_usuario u';
        $condicion = 'u.id_usuario=' . $id_usuario;
        $elemento = $this->db->selectOne($campos, $tablas, $condicion);
        if ($elemento) {
            foreach ($elemento as $key => $value) {
                $campos_formulario[$key]['valor'] = $value;
            }
        }
        $accion = "guardar_edicion";
        require_once self::PATH_PLUGINS . 'maestro/view/maestro2.html.php';
        FormHTML::formulario_dinamico($campos_formulario, $accion, $this->modulo, $this->db, $detalle = '', $campos_personalizados = '', $parametros = '', $retorno = '', $titulo = 'Mi cuenta', $form_complemento = '', $ajax = 'ajax', $area = 'area_trabajo');
    }

}
