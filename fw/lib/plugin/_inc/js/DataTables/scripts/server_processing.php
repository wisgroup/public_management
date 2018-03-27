<?php
session_start();
require('../../../../php/lib/app_config.class.php');
// SQL server connection information

$config = new App_Config();

$sql_details = array(
	'user' => $config->_get('usuario'),
	'pass' => $config->_get('clave'),
	'db'   => $config->_get('base_datos'),
	'host' => $config->_get('host')
);

$datos = $_SESSION[$config->_get('session')]['configuracion'];

// DB table to use
$table = $datos['tabla'];

// Table's primary key
$primaryKey = $datos['primaryKey'];

$columns = $datos['campos'];

$joinQuery = (isset($datos['from']))?$datos['from']:'';
$extraWhere = (isset($datos['where']))?$datos['where']:'';        
$groupBy = (isset($datos['groupBy']))?$datos['groupBy']:'';     

require('ssp.customized.class.php' );
echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere,$groupBy)
);