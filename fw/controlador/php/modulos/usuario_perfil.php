<?php
require_once('fw/controlador/php/modulos/maestro.class.php');

class UsuarioPerfil extends Maestro {
	CONST ERROR_CLAVE_NO_COINCIDE = "016";
	CONST EXITO_CAMBIO_CLAVE = "017";
	CONST TABLA_MODULO = "usuario";

	function __construct($db = "", $modulo = "") {
		$prefijo = $this->prefijo;
		$this->actualizacion_ajax = 'ajax';
		parent::__construct($db, $modulo);
		$this->modulo = $this->traer_nombre(__CLASS__);
		$this->nombre_tabla = $prefijo . $this->traer_nombre(__CLASS__);
		$this->primary_key = 'id_' . $this->traer_nombre(__CLASS__);

		$this->encabezado_edicion = array(array('id_usuario_perfil', 'nombre'), 'usuario', 'id_usuario=%s');
		$marca_blanca = $this->gn->get_data_loged('marca_blanca');

		$campos_formulario[$this->primary_key] = array('tipo' => 'hidden', 'valor' => '', 'complemento' => 'required');
		$campos_formulario['marca_blanca'] = array('tipo' => 'hidden', 'valor' => $marca_blanca, 'complemento' => 'required');
		$campos_formulario['nombre'] = array('tipo' => 'text', 'valor' => '', 'complemento' => 'required', 'label'=>'Perfil');
		$this->campos_formulario = $campos_formulario;
	}

	function home() {
		$id_interlocutor = $this->gn->get_data_loged('id_interlocutor');
		$campos = array('u.' . $this->primary_key . ' as Codigo', 'u.nombre AS Perfil', 'est.id_estado as Estado');
		$tablas = $this->nombre_tabla . " u, fw_estado est";
		$condicion = "u.estado_id=est.id_estado AND u.interlocutor_id=$id_interlocutor AND u.estado_id <>" . self::ESTADO_ELIMINADO ;
		$checkbox = array('id_estado');
		$this->iniciar_maestro($campos, $tablas, $condicion, 'Perfiles de Usuarios ' . date('Y-m-d H.i.s'), $checkbox, $this->modulo, $this->nombre_tabla);
	}
}
