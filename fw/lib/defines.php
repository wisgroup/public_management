<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
CONST PATH_CONTROLLER = "fw/template/standard/controller/";
CONST PATH_EXT_PLUGIN = "fw/lib/plugin/_inc/";
CONST PATH_JS_LIB = "fw/lib/js/";
CONST PATH_TEMPLATE = "fw/template/standard/";
CONST PATH_FW_DEFAULT = "fw/template/_default/";
CONST PATH_FW_PACK = "fw/pack/modulo/";
 * */
/*WIS FRAMEWORK DATA*/
define('WIS_FW_DATA', 'WIS FW V4.101');

/* WIS PATHS PLUGINS CONTROLES*/
define('PATH_PLUGIN_MODULO','fw/lib/plugin/modulo/controller/modulo.class.php');
define('PATH_PLUGIN_MAESTRO','fw/lib/plugin/maestro/controller/maestro.class.php');
define('PATH_PLUGIN_REPORTE','fw/lib/plugin/reporte/controller/reporte.class.php');
define('PATH_PLUGIN_INTERLOCUTOR','fw/pack/base/interlocutor/controller/interlocutor.php');

/* WIS PATHS PLUGINS VISTAS*/
define('PATH_PLUGIN_MODULO_VIEW','fw/lib/plugin/modulo/view/modulo.html.php');
define('PATH_PLUGIN_MAESTRO_VIEW','fw/lib/plugin/maestro/view/maestro.html.php');
define('PATH_PLUGIN_MAESTRO_VIEW2','fw/lib/plugin/maestro/view/maestro2.html.php');
define('PATH_PLUGIN_TABLE_VIEW','fw/lib/plugin/table/view/datatable.html.php');

/*WIS APP CONFIG*/
define('PATH_APP_CONFIG','fw/lib/config/app_config.class.php');

define('PATH_PACK_VIEW','/pack/');
define('SUFFIX_PACK_VIEW','.html.php');
define('FOLDER_VIEW', "/view/");
define('FOLDER_CONTROLLER',"/controller/");
define('PATH_EXT_PLUGIN', "fw/lib/plugin/_inc/");
define('PATH_LIB_CLASS', "fw/lib/class/");

define('LIB_GENERALES', "generales.class.php");
define('LIB_INFO_USER', "informacion_usuario.class.php");
//define('LIB_GENERALES', "generales.class.php");
//define ('BASE_TYPES', json_encode(array('fw', 'app')));

define('PATH_APP_PACK', "app/pack/");

define('FW_PREFIJO', "fw_");

/* WIS CONFIG */
//const BASE_TYPES = array('fw', 'app');
define('NAV_OPCION_DEFAULT', 1);

/*WIS FW TIPOS DE TRX*/
define('TRX_VENTA', 1);
define('TRX_COMPRA', 2);

/*WIS FW TIPOS DE PRODUCTO*/
define('PDCT_PREFABRICADO', 1);
define('PDCT_INSUMO', 2);
define('PDCT_FABRICADO', 3);

/*WIS FW TIPOS DE NEGOCIO*/
define('TIPO_NEGOCIO_WIS', 1);
define('TIPO_NEGOCIO_COMERCIO', 1);
define('TIPO_NEGOCIO_BAR', 2);
define('TIPO_NEGOCIO_RESTAURANTE', 3);

/*WIS FW CLASES*/
define('CLASE_WIS_OWNER', 1);
define('CLASE_WIS_CLIENTE', 2);
define('CLASE_PROVEEDOR', 3);
define('CLASE_CLIENTE', 4);

/*WIS FW Opciones Standard*/
define('MODULO_OPCIONES_USUARIO', 27);

/*WIS FW TIPO MODULO*/
define('CONTENIDO_MODULO', 1);
define('CONTENIDO_MAESTRO', 2);
define('CONTENIDO_REPORTE', 3);
define('CONTENIDO_OPERACION', 4);
define('CONTENIDO_INTERLOCUTOR', 5);

/* WIS ESTADOS */
define('ESTADO_ACTIVO', 1);
define('ESTADO_BLOQUEADO', 2);
define('ESTADO_ELIMINADO', 3);
define('ESTADO_DISPONIBLE', 11);
define('ESTADO_RESERVADO', 12);
define('ESTADO_OCUPADO', 13);
define('ESTADO_EN_ESPERA', 14);
define('ESTADO_PEDIDO_TOMADO', 15);
define('ESTADO_PEDIDO_SERVIDO', 16);
define('ESTADO_PEDIDO_ENTREGADO', 17);
define('ESTADO_VIGENTE', 18);

define('ESTADO_CUENTA_ABIERTA', 20);
define('ESTADO_CUENTA_CERRADA', 21);

define('ESTADO_FACTURADO', 22);
define('ESTADO_CAJA_CERRADA', 23);

define('SI', 'S');
define('NO', 'N');

define('REGIMEN_COMUN', 'C');
define('REGIMEN_SIMPLIFICADO', 'S');


define('TIPO_MOVIMIENTO_SALIDA', 1); 
define('TIPO_MOVIMIENTO_INGRESO', 2); 

/* WIS MENU */
define('OPCION_PRINCIPAL', 0);

/* TIPOS LOG */
define('LOG_INGRESO', 1);
define('LOG_INGRESO_FALLIDO',2);
define('LOG_CREAR', 3);
define('LOG_EDTAR', 4);
define('LOG_BORRAR', 5);
define('LOG_RELOAD', 6);


Class InfoModulo {
	public $nombre;
	public $nombre_clase;
	public $base;
	public $tipo;
	public $id_tipo;
	public $pack;
	public $file_path;
	public $content_type;
}

Class baseTypes{
	public $types = array('fw', 'app');
	function __get($name) {
		return $this->$name;
	}
}