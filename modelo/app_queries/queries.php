<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of queries
 *
 * @author Soldier
 */
class AppQueries {
	/* @var WISMysqli */
	public $dataBase = null;
	
	function __construct(WISMysqli $dataBase) {
		$this->dataBase = $dataBase;
	}
	
	function consultar_cuenta_x_mesa(array $param) {
		$campos = array("c.id_cuenta, m.nombre AS mesa", "m.id_mesa");
		$tables = 'cuenta c, mesa m';
		$condicion = " c.punto_atencion_id = m.id_mesa "
                    . " AND c.punto_atencion_id = " . $param['id_mesa'] . " "
                    . " AND c.estado_id = " . ESTADO_CUENTA_ABIERTA;
		
		$datos_cuenta = $this->dataBase->selectOne($campos, $tables, $condicion, '', false);
		if (!isset($datos_cuenta['id_cuenta'])) {
			return false;
		}
		return $datos_cuenta['id_cuenta'];
	}
        /*V4_102*/
        function consultar_cuentas_x_mesa(array $param) {
		$campos = array("c.id_cuenta, m.nombre AS mesa", "m.id_mesa");
		$tables = 'cuenta c, mesa m';
		$condicion = " c.punto_atencion_id = m.id_mesa "
                    . " AND c.punto_atencion_id = " . $param['id_mesa'] . " "
                    . " AND c.estado_id = " . ESTADO_CUENTA_ABIERTA;
		
		$datos_cuentas = $this->dataBase->select($campos, $tables, $condicion, '', false);
		
		return $datos_cuentas;
	}
        function contar_cuentas_x_mesa(array $param) {
		$campos = array("COUNT(c.id_cuenta) AS cuentas");
		$tables = 'cuenta c';
		$condicion = " c.punto_atencion_id = " . $param['id_mesa'] . " "
                    . " AND c.estado_id = " . ESTADO_CUENTA_ABIERTA;
		
		$datos_cuenta = $this->dataBase->selectOne($campos, $tables, $condicion, '', false);
		
		return $datos_cuenta['cuentas'];
	}
	function consultar_mesa(array $param) {
		$campos = array("m.id_mesa, m.nombre AS mesa");
		$tables = 'mesa m';
		$condicion = " m.id_mesa = " . $param['id_mesa'];
		
		$datos_mesa = $this->dataBase->selectOne($campos, $tables, $condicion, '', false);
		
		return $datos_mesa;
	}

	function consultar_pedidos_x_cuenta(array $param) {
		$campos = array("p.id_pedido");
		$tables = 'pedido p';
		$condicion = " p.cuenta_id = " . $param['id_cuenta'] . " "
                    . " AND p.estado_id IN " . $param['estado_id']
                    . " AND p.interlocutor_id=".$param['id_owner'];
		
                
		$datos = $this->dataBase->select($campos, $tables, $condicion, false, false, false);
		if (!$datos) {
			return false;
		}
		return $datos;
	}
        function consultar_cuenta_estado(array $param){
            $campos = array("p.id_cuenta", "p.estado_id");
            $tables = 'cuenta p';
            $condicion = " p.id_cuenta = " . $param['id_cuenta'] . " "
                . " AND p.interlocutor_owner_id=".$param['id_owner'];

            $datos = $this->dataBase->selectOne($campos, $tables, $condicion, false, false, false);
            if (!$datos){
                return false;
            }
            return $datos;
	}
        
        function consultar_transaccion_cuenta(array $param) {
		$campos = array("p.cuenta_id", "p.id_transaccion");
		$tables = 'transaccion p';
		$condicion = " p.cuenta_id = " . $param['id_cuenta'] . " "
                    . " AND p.interlocutor_id=".$param['id_owner'];
		
		$datos = $this->dataBase->selectOne($campos, $tables, $condicion, false, false, false);
		if (!$datos) {
                    return false;
		}
		return $datos;
	}
        
	function consultar_pedidos_detalle_x_cuenta(array $param) {
		$campos = array("p.id_pedido", "pi.estado_id", "COUNT(pi.id_pedido_item) AS cantidad" );
		$tables = 'pedido p, pedido_item pi';
		$condicion = " p.id_pedido = pi.pedido_id "
				. " AND  p.cuenta_id = " . $param['id_cuenta']
				. " GROUP BY 1, pi.estado_id";
		
		$datos = $this->dataBase->select($campos, $tables, $condicion, '', false, false);
		if (!$datos) {
			return false;
		}
		return $datos;
	}
	
	function consultar_pedido_detalle_x_pedido(array $param){
		
		$campos = array('pi.id_pedido_item','p.id_producto','p.nombre_corto','p.descripcion','p.precio', 'FORMAT(p.precio, 0) as precio_format','pi.cantidad');
		$tables="producto p, pedido_item pi,pedido pe";
		$condicion = 'pi.producto_id=p.id_producto '
				. ' AND pi.pedido_id=pe.id_pedido '
				. ' AND pe.id_pedido='.$param['id_pedido']
				. ' AND pe.interlocutor_id='.$param['id_owner'].' '
				. ' AND (pi.estado_id='.ESTADO_PEDIDO_ENTREGADO.')';

		$datos = $this->dataBase->select($campos, $tables, $condicion, 'id_pedido_item', false, false);
		if (!$datos) {
			return false;
		}
		return $datos;
		
	}
        function consultar_pedido_detalle_x_pedido_estado(array $param){
		
		$campos = array('pi.id_pedido_item','p.id_producto','p.nombre_corto','p.descripcion','p.precio', 'FORMAT(p.precio, 0) as precio_format','pi.cantidad', 'pi.estado_id');
		$tables="producto p, pedido_item pi,pedido pe";
		$condicion = 'pi.producto_id=p.id_producto '
				. ' AND pi.pedido_id=pe.id_pedido '
				. ' AND pe.id_pedido='.$param['id_pedido']
				. ' AND pe.interlocutor_id='.$param['id_owner'].' ';

		$datos = $this->dataBase->select($campos, $tables, $condicion, 'id_pedido_item', false, false);
		if (!$datos) {
			return false;
		}
		return $datos;
		
	}
        function consultar_descuentos_owner(array $param){
		
		$campos = array('d.id_descuento', 'd.nombre', 'd.descuento');
		$tables="descuento d";
		$condicion = ' d.interlocutor_id='.$param['interlocutor_id'].''
                        . ' AND d.estado_id =  '.ESTADO_ACTIVO." ";

		$datos = $this->dataBase->select($campos, $tables, $condicion, 'id_descuento', false, false);
		if (!$datos) {
			return false;
		}
		return $datos;
	}
        
        function consultar_descuentos_owner_id(array $param){
		
		$campos = array('d.id_descuento', 'd.nombre', 'd.descuento');
		$tables="descuento d";
		$condicion = ' d.id_descuento='.$param['id_descuento'];

		$datos = $this->dataBase->selectOne($campos, $tables, $condicion, 'id_descuento', false, false);
		if (!$datos) {
			return false;
		}
		return $datos;
	}
	function consultar_medios_pago(array $param){
		
		$campos = array('d.id_transaccion_pago_medio', 'd.nombre');
		$tables="transaccion_pago_medio d";
		$condicion = ' d.estado_id =  '.ESTADO_ACTIVO." ";

		$datos = $this->dataBase->select($campos, $tables, $condicion, false, false, false);
		if (!$datos) {
			return false;
		}
		return $datos;
	}
	function guardar_interlocutor_complemento(array $param){
		
		$respuesta = $this->db->insert($campos, $tabla);
		
		return $respuesta;
	}
}
