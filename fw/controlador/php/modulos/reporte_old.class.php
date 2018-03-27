<?php

require_once('fw/controlador/php/modulos/modulo.class.php');

class Reporte extends Modulo {
	public $tipo_contenido = 'html';

	function __construct($db = "", $modulo = "") {

        parent::__construct($db, $modulo);

    }

    function iniciar_reporte($campos,$tablas,$condicion,$group_by='',$order='') {
        $parametros = $this->gn->traer_parametros('GET');

        if (isset($parametros->type)) {
            $this->tipo_contenido = $parametros->type;
        }
        $this->pintar_tabla($campos,$tablas,$condicion,$group_by,$order);
    }
	function traerProductos(){
		$id_interlocutor = $this->gn->get_data_loged('id_interlocutor');
		
        $campos = array('id_producto AS ID', 'nombre', 'precio');
        $tablas = "producto";
        $condicion = "interlocutor_id=".$id_interlocutor." ";
		
		$response['lista']= $this->db->select($campos,$tablas,$condicion);
		$response['campos']= $campos;
		return $response;
	}
}