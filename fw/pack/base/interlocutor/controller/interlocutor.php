<?php
if (!class_exists('Maestro')) {
    require_once(PATH_PLUGIN_MAESTRO);
}
class interlocutor extends Maestro {
	public $tipo_contenido = 'html';
	CONST PERFIL_PRINCIPAL = 1;
	CONST PERFIL_OPERATIVO = 2;

	public function __construct($db = "", $modulo = "") {
		$this->modulo = $modulo;
		
		$this->primary_key = 'id_interlocutor';
		$this->actualizacion_ajax = 'ajax';
                
		parent::__construct($db, $this->modulo);
                $this->nombre_tabla = "fw_interlocutor";
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
            $campos = array('i.' . $this->primary_key . " Codigo", 'i.num_documento as documento', "concat(i.nombre,' ',i.apellido) as nombre", "ic.nombre as Tipo", "itn.nombre as Negocio", 'i.email', "est.descripcion as Estado");
            $tablas = $this->nombre_tabla . ' i, fw_interlocutor_clase ic, fw_interlocutor_tipo_negocio itn, fw_estado est, fw_interlocutor_condicion itc';
            $condicion = 'itc.interlocutor_id=' . $interlocutor_id_actual . ' AND i.interlocutor_clase_id = ic.id_interlocutor_clase AND i.id_interlocutor <>' . $interlocutor_id_actual . ' AND i.estado_id <> ' . self::ESTADO_ELIMINADO . ' AND itn.id_interlocutor_tipo_negocio=i.interlocutor_tipo_negocio_id AND i.estado_id=est.id_estado';
            $this->iniciar_maestro($campos, $tablas, $condicion);
	}

	function formulario_edicion($campos_formulario = null) {

		if ($campos_formulario === null) {
			$campos_formulario = $this->campos_formulario;
		}
		$campos = array();
		$campos_personalizados = array();

		foreach ($campos_formulario as $key => $input) {					 //Seteando el array con le nombre de los campos
			if (!in_array('personalizado', $input)) {
				array_push($campos, $key);
			}
		}
		$this->tipo_contenido = 'ajax';
		$accion = "guardar_creacion&type=html";
		$parametros = $this->gn->traer_parametros('GET');

		$encabezado = $this->encabezado_creacion;
		if (isset($parametros->type)) {
			$this->tipo_contenido = $parametros->type;
		}

		if (isset($parametros->id) && (int) $parametros->id > 0) {
			$accion = "guardar_edicion";

			if (is_array($this->encabezado_edicion)) {

				$p = $this->db->selectone($this->encabezado_edicion[0], $this->encabezado_edicion[1], sprintf($this->encabezado_edicion[2], $parametros->id));
				$encabezado = '';
				foreach ($p as $key => $value) {
					$encabezado = $encabezado . $value . " ";
				}
			} else {
				$encabezado = $this->encabezado_edicion;
			}

			unset($campos[1]);
			$elemento = $this->db->selectone($campos, $this->nombre_tabla, $parametros->id . ' = ' . $this->primary_key);
			if (is_array($elemento)) {
				foreach ($elemento as $key => $value) {
					$campos_formulario[$key]['valor'] = $value;
				}
			}
			unset($campos_formulario['nickname']);
			unset($campos_formulario['confirmar_email']);
		}
		require_once(PATH_PLUGIN_MAESTRO_VIEW2);
		FormHTML::formulario_dinamico($campos_formulario, $accion, $this->modulo, $this->db, $this->detalle, $campos_personalizados, $parametros, $retorno = '', $encabezado, '', $this->actualizacion_ajax, $this->area_actualizacion);
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
		$nickname = $campos['nickname'];
		unset($campos['nickname']);
		$campos_interlocutor_condicion = array();
		$campos_interlocutor_condicion['tema_id'] = $campos['interlocutor_tipo_negocio_id'];
		$log = $this->registrar_log(self::LOG_CREAR, $this->nombre_tabla, $campos);
		$respuesta_interlocutor = $this->db->insert($campos, $this->nombre_tabla);
		$id_inter = str_pad($this->db->insertId(), 10, "0", STR_PAD_LEFT);
		unset($campos['interlocutor_tipo_negocio_id']);
		unset($campos['interlocutor_clase_id']);
		unset($campos['tipo_documento']);
		unset($campos['direccion']);
		unset($campos['celular']);
		unset($campos['telefono']);
		unset($campos['orden']);
		unset($campos['descripcion']);
		unset($campos['id_interlocutor']);
		$campos['usuario_perfil_id'] = 1; //tipo de perfil de aqui sacamos meseros o admin o demas        
		$campos['interlocutor_id'] = $id_inter;
		$campos_interlocutor_condicion['interlocutor_id'] = $id_inter;
		$campos_interlocutor_condicion['imagen_perfil'] = '14093288915400a6fbacff5.jpg';
		$campos_interlocutor_condicion['imagen_logo'] = 'logo.png';
		$campos_interlocutor_condicion['titulo_sitio'] = 'WIS::FRAMEWORK';
		$campos_interlocutor_condicion['marca_blanca'] = $id_inter;
		$respuesta_ic = $this->db->insert($campos_interlocutor_condicion, 'fw_interlocutor_condicion');
		$campos['nickname'] = $nickname;
		$clave = '111111'; //clave provicional hasta definir.
		$campos['clave'] = $this->sx->encriptar_clave($clave);
		$campos['interlocutor_id'] = $id_inter;
		$respuesta_usuario = $this->db->insert($campos, 'fw_usuario');

		if (!$respuesta_interlocutor || !$respuesta_usuario || !$respuesta_ic) {
			$mensaje = $this->nc->traer_mensaje_respuesta($this->ERROR_CREACION);
			$this->nc->set_notificacion(utf8_encode($mensaje['descripcion']));
		} else {
			$last_id = $this->db->insertId();
			$mensaje = $this->nc->traer_mensaje_respuesta($this->EXITO_CREACION);
			$this->nc->set_notificacion(utf8_encode($mensaje['descripcion']));
		}

		if (!$redirect) {

			if ($last_id > 0) {
				return $last_id;
			} else {
				header('location:?opcion=' . $this->modulo);
			}
		}

		$retorno = $this->gn->get_data_loged('retorno');

		if (isset($retorno) && $retorno != '') {
			$this->gn->set_data_loged('retorno', '');
			$campo = $this->gn->get_data_loged('campo');
			$this->gn->set_data_loged('campo', '');
			$this->gn->set_data_loged($campo, $last_id);

			header('location:?opcion=' . $retorno);
		} else {

			if ($this->actualizacion_ajax == 'ajax') {
				$this->home();
			} else {
				header('location:?opcion=' . $this->modulo);
			}
		}
	}
	public function guardar_interlocutor_complemento($campos, $tabla) {
		$respuesta = $this->db->insert($campos, $tabla);
		if ($respuesta) {
			$log = $this->registrar_log(self::LOG_CREAR, $this->nombre_tabla, $campos);
			return $this->db->insertId();
		} else {
			return false;
		}
	}
		
	public function guardar_interlocutor($campos) {

            $respuesta = $this->db->insert($campos, $this->nombre_tabla);
            if ($respuesta) {
                $log = $this->registrar_log(self::LOG_CREAR, $this->nombre_tabla, $campos);
                return $this->db->insertId();
            } else {
                return false;
            }
	}

}
