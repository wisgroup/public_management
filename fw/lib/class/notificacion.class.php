<?php

class Notificacion {
	private $db;
	private $gn;
	private $ruta_cron = '';
	private $config;

	function __construct($db, $gn, $ruta_cron = '') {
		$this->db = $db;
		$this->ruta_cron = $ruta_cron;
		$this->gn = $gn;
	}

	function traer_mensaje_respuesta($codigo) {
		$mensaje = array();
		$mensaje = $this->db->selectOne(array('descripcion'), 'fw_codigo_respuesta', "codigo = '$codigo' ");
		return $mensaje;
	}

	function retornar_respuesta_ajax($descripcion, $codigo_error = "") {
		$respuesta = "";
		if ($codigo_error != "") {
			$respuesta .= "ERROR " . $descripcion;
		} else {
			$respuesta = $descripcion;
		}
		echo $respuesta;
	}

	function set_notificacion($mensaje) {
		$this->gn->set_data_loged('mensaje_notificacion', $mensaje, 'generales');
	}

	function get_notificacion() {
		return $this->gn->get_data_loged('mensaje_notificacion', 'generales');
	}

	function mostrar_notificacion($mensaje, $titulo = '') {
		require_once($this->ruta_cron . "fw/lib/plugin/maestro/view/maestro.html.php");
		MaestroHTML::mostrar_alerta($mensaje, $titulo);
	}

	function verificar_notificacion() {
		$notificacion = $this->get_notificacion();
		if (isset($notificacion) && !empty($notificacion)) {
			$this->set_notificacion('');
			$this->mostrar_notificacion($notificacion);
		}
	}
}
