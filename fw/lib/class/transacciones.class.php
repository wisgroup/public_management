<?php

Class Transacciones {
    /* @var Mysql */

    private $db;
    private $stock;
    private $appQry;

    function __construct($db) {
        $this->db = $db;

        require_once('stock.class.php');
        $this->stock = new Stock($this->db);
        require_once 'modelo/app_queries/app_control_queries.class.php';
        $this->appQry = new AppControlQueries($this->db);
    }

    public function numeracion_factura($interlocutor, $medio_pago = 1, $pago_electronico = 0) {

        $factura = $this->db->selectOne(array('id_factura_numeracion', 'factura_actual'), 'factura_numeracion', 'estado_id =' . ESTADO_VIGENTE . ' '
                . ' AND interlocutor_id = ' . $interlocutor, false, false);
        
        if (!$factura) {
            return 0;
        }
        
        $configuracion = $this->db->selectOne(array('facturacion_control', 'facturacion_intervalo', 'factura_contador'), 'fw_interlocutor_configuracion', ' interlocutor_id = ' . $interlocutor);
        
        switch ($configuracion['facturacion_control']) {
            case 'N':
               return 0;
               break;
            case 'S':
                $numeracion = $factura['factura_actual'] + 1;
                $this->db->update(array('factura_actual' => $numeracion), 'factura_numeracion', 'id_factura_numeracion = ' . $factura['id_factura_numeracion']);
               break;
           case 'CE':
                if($medio_pago > 1){
                    $numeracion = $factura['factura_actual'] + 1;
                    $this->db->update(array('factura_actual' => $numeracion), 'factura_numeracion', 'id_factura_numeracion = ' . $factura['id_factura_numeracion']);
                    //$configuracion['factura_contador'] = 1;
                }else if(!((int)$pago_electronico === 1)){
                    //$configuracion['factura_contador'] = $configuracion['factura_contador'] +1;
                    $numeracion = 0;
                }else if($configuracion['factura_contador'] < $configuracion['facturacion_intervalo']){
                    $configuracion['factura_contador'] = $configuracion['factura_contador'] +1;
                    $numeracion = 0;
                }else{
                    $numeracion = $factura['factura_actual'] + 1;
                    $this->db->update(array('factura_actual' => $numeracion), 'factura_numeracion', 'id_factura_numeracion = ' . $factura['id_factura_numeracion']);
                    $configuracion['factura_contador'] = 1;
                }
                $this->db->update(array('factura_contador' => $configuracion['factura_contador']), 'fw_interlocutor_configuracion', 'interlocutor_id = ' . $interlocutor);
               break;
            default:
                return 0;
                break;
        }
        
        return $numeracion;
    }

    public function venta($factura, $interlocutor, $cliente, $observacion, $productos, $cuenta_id = 0, $servicio = 0, $descuento = 0, $domicilio = 0, $pago_electronico = 1, $medio_pago = 1) {
        $total = 0;
        
        $factura = $this->numeracion_factura($interlocutor, $medio_pago, $pago_electronico);
        
        if($descuento > 0){
            $datos_descuento = $this->appQry->ejecutarQuery('consultar_descuentos_owner_id', array('id_descuento' => $descuento));
        }else{
            $datos_descuento['descuento'] = 0;
        }
        
        $datos_transaccion = array(
            "factura_numero" => $factura,
            "cuenta_id" => $cuenta_id,
            "transaccion_tipo_id" => TRX_VENTA,
            "interlocutor_externo" => $cliente,
            "interlocutor_id" => $interlocutor,
            "pago_electronico" => $pago_electronico,
            "observacion" => $observacion,
            "servicio" => $servicio,
            "domicilio" => $domicilio,
            "descuento_porcentaje" => $datos_descuento['descuento'],
            "estado_id" => ESTADO_ACTIVO
        );
        
        $id_transaccion = $this->registrar_transaccion($datos_transaccion);
        $subtotal = 0;
        
        if ($id_transaccion) {
            foreach ($productos as $id_producto => $producto) {
                if ($id_producto > 0) {
                    if(isset($producto['precio'])){
                        $valor = $producto['precio'];
                    }else if(isset($producto['valor'])){
                        $valor = $producto['valor'];
                    }
                    $datos_transaccion_item = array(
                        'transaccion_id' => $id_transaccion,
                        'producto_id' => $producto['id_producto'],
                        'cantidad' => $producto['cantidad'],
                        'valor_unitario' => $valor,
                        //'iva' => $producto['iva'],
                        'iva' => 0,
                        'estado_id' => ESTADO_ACTIVO
                    );
                    
                    $id_transaccion_item = $this->registrar_transaccion_item($datos_transaccion_item);
                    $subtotal = $subtotal + ($valor * $producto['cantidad']);
                    $this->stock->actualizar_stock($producto['id_producto'], $producto['cantidad'], OPERACION_DECREMENTO, $id_transaccion, $interlocutor);
                }
            }
            
            $subtotal_noimp = $subtotal;
            if(TIPO_IMPUESTO == 'comun'){
                //TODO: Este parametro se debe discriminar por interlocutor clienteWis
                if(IMPUESTO_INCLUIDO == 'SI'){
                    $subtotal_noimp = $subtotal / (1+ IMPOCONSUMO);
                    $impuesto = $subtotal - $subtotal_noimp;
                }else{
                    $impuesto = $subtotal * IMPOCONSUMO;
                }
            }else{
                $impuesto  = 0;
            }
            if($datos_descuento['descuento'] > 0){
                $total_descuento = $subtotal_noimp*($datos_descuento['descuento']/100);
            }else{
                $total_descuento = 0;
            }
            
            $total = $subtotal_noimp -($total_descuento);
            /*IMPUESTO INCLUIDO*/
            $total += $impuesto + $servicio + $domicilio;
            
            /*IMPUESTO NO INCLUIDO*/
            //$total += $impuesto + $servicio;
            $campos = array(
                "impuesto" => $impuesto,
                "descuento" => $total_descuento,
                'subtotal' => $subtotal,
                'servicio' => $servicio,
                'total' => $total
            );
            
            $this->actualizar_transaccion($campos, $id_transaccion);
            $this->caja_actualizar('ventas', $total, '+', $interlocutor);
        }
        
        return $id_transaccion;
    }

    public function compra($interlocutor, $proveedor, $observacion, $productos,$dato_adicional=NULL) {
        //TODO: Verificar los datos que se envian para registrar transaccion
            
            $datos_transaccion = array(
                "transaccion_tipo_id" => TRX_COMPRA,
                "interlocutor_externo" => $proveedor,
                "interlocutor_id" => $interlocutor,
               
                "observacion" => $observacion,
                "estado_id" => ESTADO_ACTIVO);
        if($dato_adicional != NULL){
            $datos_transaccion['factura_numero'] =  $dato_adicional['factura_numero'];
        }

        $id_transaccion = $this->registrar_transaccion($datos_transaccion);
        
        if ($id_transaccion) {
            $total = 0;
            foreach ($productos as $id_producto => $producto) {
                $datos_transaccion_item = array(
                    'transaccion_id' => $id_transaccion,
                    'producto_id' => $id_producto,
                    'cantidad' => $producto->cantidad,
                    'valor_unitario' => $producto->valor,
                    'iva' => $producto->iva,
                    'estado_id' => ESTADO_ACTIVO);

                $id_transaccion_item = $this->registrar_transaccion_item($datos_transaccion_item);
                $total = $total + ($producto->valor * $producto->cantidad);

                $this->stock->actualizar_stock($id_producto, $producto->cantidad, OPERACION_INCREMENTO, $id_transaccion, $interlocutor);
                //$resultado_update_transaccion = $this->db->query("UPDATE transaccion SET total='" . $total . "' WHERE id_transaccion= " . $id_transaccion, false);
            }
            
            $campos = array(
                'total' => $total
            );
            $this->actualizar_transaccion($campos, $id_transaccion);
            $this->caja_actualizar('compras', $total, '-', $interlocutor);
        }

        return $id_transaccion;
    }

    public function registrar_transaccion($campos) {
        $insert = $this->db->insert($campos, 'transaccion',false);
        if ($insert) {
            $id = $this->db->insertId();
            return $id;
        } else {
            return false;
        }
    }

    public function registrar_transaccion_item($campos){
        $insert = $this->db->insert($campos, 'transaccion_item');
        if($insert){
            $id = $this->db->insertId();
            return $id;
        }else{
            return true;
        }
    }

    public function actualizar_transaccion($campos, $id){
        $result = $this->db->update($campos, 'transaccion', " id_transaccion = " . $id);
        return $result;
    }

    public function caja_actualizar($campo, $valor, $operacion, $owner = null){

        $campos = array(
            $campo => $campo . ' + ' . $valor,
            'caja' => ' caja ' . $operacion . ' ' . $valor
        );

        $condicion = " estado_id = " . ESTADO_VIGENTE
            . " AND interlocutor_id = " . $owner;
        $result = $this->db->update_number($campos, 'caja', $condicion, false, false);
        return $result;
    }
}