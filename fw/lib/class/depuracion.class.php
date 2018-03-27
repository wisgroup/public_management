<?php

class Depuracion{

	private $db;

	function __construct($db){
		$this->db = $db;
	}

	/* ------------- Reestructurar Funciones ---------- */

	function debug($object, $out = NULL){
		echo "<pre>";
		print_r($object);
		if ($out == 1)
			exit;
	}

	function setLogError($modulo, $concepto, $datos_adicionales = ""){
		$fecha = date("Y_m_d");
		$log_file = fopen("media/logs/topup_error_log_" . $fecha . ".txt", 'a');
		$fecha = date("Y-m-d ");

		$log_line = $fecha . ";" . $modulo . ";" . $concepto;
		if ($datos_adicionales != "")
			$log_line .= ";" . $datos_adicionales;

		$log_line .="\n";

		fputs($log_file, $log_line);
		fclose($log_file);
	}

}
