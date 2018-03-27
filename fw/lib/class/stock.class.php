<?php

/**
 * 
 */
class Stock {
    /* @var Mysql */

    private $db;

    function __construct($db) {
        $this->db = $db;
    }

    function actualizar_stock($id_producto, $cantidad, $operacion = OPERACION_DECREMENTO, $id_transaccion = 0, $interlocutor = 0) {
        $producto = $this->db->selectOne(array('id_producto', 'stock_control', 'producto_tipo_id', 'costo'), 'producto', ' id_producto = ' . $id_producto, false, false);
        $interlocutor_configuracion = $this->db->selectOne(array( 'stock_control'), 'fw_interlocutor_configuracion', ' interlocutor_id = ' . $interlocutor, false, false);
        $update = "";
        $tipo_movimiento = TIPO_MOVIMIENTO_SALIDA;
        
        if(isset($interlocutor_configuracion['stock_control']) && $interlocutor_configuracion['stock_control']== 1 ){
            if (isset($producto['producto_tipo_id']) && ($producto['producto_tipo_id'] == PDCT_FABRICADO)) {
                if (isset($producto['stock_control']) && $producto['stock_control'] == 'S'){
                    $this->actualizar_insumos($producto, $id_transaccion, $cantidad);
                }
            } else {
                if (isset($producto['stock_control']) && $producto['stock_control'] == 'S'){
                    $update = $this->db->query('UPDATE producto_stock SET stock = stock ' . $operacion . ' ' . $cantidad . ' WHERE producto_id = ' . $id_producto, false);
                    if($operacion == OPERACION_INCREMENTO){
                        $tipo_movimiento = TIPO_MOVIMIENTO_INGRESO;
                    }
                    $this->registrar_insumo_movimiento($id_transaccion, $id_producto, 0, $cantidad, $producto['costo'], $tipo_movimiento);
                }
            }
        }
        return $update;
    }

    function actualizar_insumos($producto, $id_transaccion, $cantidad = 1) {
        $ingredientes = $this->db->select(array('r.producto_id', 'ri.producto_id AS ingrediente_id', 'ri.cantidad', 'p.costo'), 'receta r, receta_ingrediente ri, producto p', ' r.id_receta = ri.receta_id '
                . ' AND ri.producto_id = p.id_producto '
                . ' AND ri.estado_id = '.ESTADO_ACTIVO
                . ' AND r.estado_id = '.ESTADO_ACTIVO
                . ' AND r.producto_id = ' . $producto['id_producto']
                , false, false);

        foreach ($ingredientes as $ingrediente) {
            $cantidad_actualizacion = $ingrediente['cantidad']*$cantidad;
            $update = $this->db->query('UPDATE producto_stock SET stock = stock - ' . $cantidad_actualizacion . ' WHERE producto_id = ' . $ingrediente['ingrediente_id']. ' and stock >0', false);
            $this->registrar_insumo_movimiento($id_transaccion, $ingrediente['ingrediente_id'], $producto['id_producto'], $cantidad_actualizacion, $ingrediente['costo'], 1);
        }
        
        return true;
    }

    function registrar_insumo_movimiento($id_transaccion, $id_insumo, $id_producto, $cantidad, $costo, $tipo = TIPO_MOVIMIENTO_SALIDA) {
        $valores = array(
                    "transaccion_id" => $id_transaccion,
                    "marca_blanca" => "0",
                    "insumo_movimiento_tipo_id" => $tipo,
                    "insumo_id" => $id_insumo,
                    "producto_id" => $id_producto,
                    "cantidad" => $cantidad,
                    "costo_individual" => $costo,
                    "costo" => $cantidad * $costo,
                    "estado_id" => ESTADO_ACTIVO
                );
        $update = $this->db->insert($valores, 'insumo_movimiento');
    }
}
