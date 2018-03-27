<?php

require_once(PATH_PLUGIN_MODULO);

class Operaciones extends Modulo {

    CONST ESTADO_ELIMINADO = 3;
    CONST VENTA = 1;
    CONST COMPRA = 2;
    CONST PROVEEDOR = 3;
    CONST CLIENTE = 4;

    public $transaccion_tipo = '';
    public $comercio_clase = '';
    public $encabezado = array();
    public $encabezado_flotante = array();

    public function __construct($db = "", $modulo = "") {
        parent::__construct($db, $modulo);
    }

    function home($titulo = '') {
        $this->nc->verificar_notificacion();
        $this->pintar_transacciones($this->modulo, $titulo);
    }

    function pintar_transacciones($modulo = '', $titulo = '') {
        $parametros = $this->gn->traer_parametros('GET');
        $interlocutor = $this->gn->get_data_loged('id_interlocutor');
        $marca_blanca = $this->gn->get_data_loged('marca_blanca');
        $comercios = $this->db->select(array('id_interlocutor', "concat(nombre,' ',apellido) as nombre"), 'fw_interlocutor', ' interlocutor_clase_id =' . $this->transaccion_tipo . ' AND  interlocutor_id=' . $interlocutor);

        if (isset($parametros->supercategoria_id)) {
            $id_supercat = $parametros->supercategoria_id;
        }

        $supercategorias = $this->db->select(array(
            'nombre',
            "id_producto_categoria",
            "producto_categoria_id"
                ), 'producto_categoria', ' marca_blanca =' . $marca_blanca . ' '
                . ' AND producto_categoria_id = 0'
                . ' AND  estado_id=' . ESTADO_ACTIVO . " ORDER BY nombre "
        );


        if (!isset($id_supercat) || $id_supercat <= 0) {
            $id_supercat = $supercategorias[0]["id_producto_categoria"];
        }
        $categorias = $this->db->select(array(
            'nombre',
            "id_producto_categoria",
            "producto_categoria_id"
                ), 'producto_categoria', ' marca_blanca =' . $marca_blanca . ' '
                . ' AND producto_categoria_id = ' . $id_supercat
                . ' AND  estado_id=' . ESTADO_ACTIVO . " ORDER BY nombre ", false, false, false
        );

        if (!((int) count($categorias) > 0)) {
            $categorias = $supercategorias;
            $supercategorias = NULL;
        }



        $c = $this->db->select(array('nombre'), 'fw_interlocutor', ' interlocutor_clase_id =' . $this->transaccion_tipo);
        $medio = $this->db->select(array('nombre'), 'transaccion_pago_medio');
        $resumen = New stdClass();
        $resumen->total = New stdClass();
        $resumen->total->subtotal = 0;
        $resumen->total->impuestos = 0;
        $resumen->total->total = 0;
        $this->tipo_contenido = 'html';
        require_once PATH_APP_PLUGINS . APP_PLUGIN_OPERACIONES_VIEW;
        OperacionesHTML::formulario($comercios, $modulo, $this->encabezado, $titulo, $categorias, $resumen, $c, $medio, $supercategorias);
    }

    function traer_productos() {
        $this->pintar_tabla_ajax($this->encabezado_flotante, 'app/pack/transaccion/' . $this->modulo . '/controller/' . $this->modulo . '_ajax.php');
        //$this->pintar_tabla_ajax($this->encabezado_flotante,'?opcion=compras&a=consultar_productos');    
    }

    function select_cat() {
        $parametros = $this->gn->traer_parametros('GET');
        $interlocutor = $this->gn->get_data_loged('id_interlocutor');
        if ($this->modulo == "compras") {
            $campo_precio = "costo";
            $producto_tipo = PDCT_INSUMO;
        } else {
            $campo_precio = "precio";
            $producto_tipo = PDCT_FABRICADO;
        }


        $productos = $this->db->select(array(
                'id_producto',
                'nombre',
                $campo_precio.' AS precio',
                'p.stock_control',
                'ps.stock',
                "p.producto_categoria_id"
            ), 
            'producto p, producto_stock ps', 
            ' p.producto_categoria_id =' . $parametros->id . ' '
            . ' AND p.id_producto = ps.producto_id '
            . ' AND p.producto_tipo_id IN (' . PDCT_PREFABRICADO . ', ' . $producto_tipo . ') '
            . ' AND  p.estado_id=' . ESTADO_ACTIVO . " ORDER BY p.nombre "
            , false, false, false);
        
        $configuracion = $this->db->selectOne(array('stock_control'), 'fw_interlocutor_configuracion', ' interlocutor_id = ' . $interlocutor);
        
        require_once PATH_APP_PLUGINS . APP_PLUGIN_OPERACIONES_VIEW;
        OperacionesHTML::tab_productos($this->modulo, $productos, $configuracion);
    }

    function buscar_producto() {
        $parametros = $this->gn->traer_parametros('GET');
        $interlocutor = $this->gn->get_data_loged('id_interlocutor');

        if ($this->modulo == "compras") {
            $producto_tipo = PDCT_INSUMO;
        } else {
            $producto_tipo = PDCT_FABRICADO;
        }

        $producto = $this->db->selectOne(array(
            'id_producto',
            'nombre',
            'precio',
            "producto_categoria_id"
                ), 'producto', ' barcode =' . $parametros->id . ' '
                . ' AND producto_tipo_id IN (' . PDCT_PREFABRICADO . ', ' . $producto_tipo . ') '
                . ' AND interlocutor_id = ' . $interlocutor
                . ' AND  estado_id=' . ESTADO_ACTIVO . " "
                , false, false, false);

        require_once PATH_APP_PLUGINS . APP_PLUGIN_OPERACIONES_VIEW;
        OperacionesHTML::producto($this->modulo, $producto);
    }

    public function traer_comercios() {

        $parametros = $this->gn->traer_parametros('GET');
        $interlocutor = $this->gn->get_data_loged('id_interlocutor');

        switch ($parametros->campo) {
            case 'numero':
                $campo = 'num_documento';
                break;
            case 'nombre':
                $campo = 'nombre';
                break;
        }

        $num_clientes = $this->db->select(array($campo), 'fw_interlocutor', "interlocutor_id='" . $interlocutor . "' AND interlocutor_clase_id=" . $this->comercio_clase . " AND " . $campo . " like '%" . $parametros->term . "%'");

        foreach ($num_clientes as $key => $value) {
            $clientes[] = $value[$campo];
        }

        echo json_encode($clientes);
    }

    public function setear_campos() {
        $parametros = $this->gn->traer_parametros('GET');
        $interlocutor = $parametros->interlocutor;

        $cliente = $this->db->selectOne(array('id_interlocutor', 'nombre', 'apellido', 'telefono', 'celular', 'email', 'direccion'), 'fw_interlocutor', "num_documento='" . $interlocutor . "' AND interlocutor_clase_id=" . $this->comercio_clase);
        if ($cliente) {
            $cliente['respuesta'] = 1;
            echo json_encode($cliente);
        } else {
            echo json_encode(array('respuesta' => 0));
        }
    }

    function trx_agregar_producto($campo) {

        $id_interlocutor = $this->gn->get_data_loged('id_interlocutor');
        $parametros = $this->gn->traer_parametros('GET');

        $tablas = 'producto p,producto_stock ps';
        $campos = array('p.id_producto as COD', 'p.referencia', 'p.barcode', 'p.nombre as PRODUCTO', 'p.iva', 'p.iva_incluido', 'ps.stock as STOCK', 'p.' . $campo . ' as precio');
        $condicion = ' interlocutor_id=' . $id_interlocutor . ' '
                . ' AND estado_id <> 3 '
                . ' AND ps.producto_id=p.id_producto';
        $condicion .= ' AND p.id_producto = ' . $parametros->id;
        $producto = $this->db->selectOne($campos, $tablas, $condicion);


        /* if ($producto['iva_incluido'] === 'N') {
          $producto['iva_valor'] = $producto['precio'] * ( $producto['iva'] / 100 );
          $producto['precio'] = $producto['precio'] ;
          $producto['subtotal'] = ($producto['precio']+$producto['iva_valor']) * $parametros->cantidad;
          }else{ */
        $producto['precio'] = $producto['precio'];
        $producto['iva_valor'] = 0;
        $producto['subtotal'] = $producto['precio'] * $parametros->cantidad;
        //}
        //echo "<pre>";
        //print_r($producto);
        require_once PATH_APP_PLUGINS . APP_PLUGIN_OPERACIONES_VIEW;
        OperacionesHTML::trx_agregar_producto($producto, $parametros->cantidad, $this->modulo);
    }

    function trx_agregar_producto_x_codigo($campo = "") {

        $id_interlocutor = $this->gn->get_data_loged('id_interlocutor');
        $parametros = $this->gn->traer_parametros('GET');

        $tablas = 'producto p,producto_stock ps';
        $campos = array('p.id_producto as COD', 'p.referencia', 'p.barcode', 'p.nombre as PRODUCTO', 'p.iva', 'p.iva_incluido', 'ps.stock as STOCK', 'p.precio');
        $condicion = ' interlocutor_id=' . $id_interlocutor . ' '
                . ' AND estado_id <> 3 '
                . ' AND ps.producto_id=p.id_producto';
        $condicion .= ' AND p.barcode = ' . $parametros->codigo;
        $producto = $this->db->selectOne($campos, $tablas, $condicion);


        if ($producto['iva_incluido'] === 'N') {
            $producto['iva_valor'] = $producto['precio'] * ( $producto['iva'] / 100 );
            $producto['precio'] = $producto['precio'] + $producto['iva_valor'];
        } else {
            $producto['precio'] = $producto['precio'];
            $producto['iva_valor'] = 0;
        }
        $producto['subtotal'] = $producto['precio'] * $parametros->cantidad;

        echo $producto['COD'];
        //require_once PATH_APP_PLUGINS . APP_PLUGIN_OPERACIONES_VIEW;
        //OperacionesHTML::trx_agregar_producto($producto, $parametros->cantidad);
    }

    function validar_stock() {
        $parametros = $this->gn->traer_parametros('GET');

        //TODO: validar si es compra o venta.
        //TODO: validar si esta en el rango de stock maximo y minimo.


        $tablas = 'producto_stock ps';
        $campos = array('ps.producto_id', 'ps.stock');
        $condicion = ' ps.producto_id = ' . $parametros->id;
        $producto = $this->db->selectOne($campos, $tablas, $condicion);

        //TODO: Modificar la validacion deacuerdo si es de entrada o salida la trx
        if ($producto['stock'] > $parametros->cantidad) {
            echo "OK";
        } else {
            echo "No existe cantidad suficiente de este producto";
        }
    }

    function trx_filtrar_categorias() {
        $parametros = $this->gn->traer_parametros('GET');

        $tablas = 'producto_categoria pc';
        $campos = array('pc.id_producto_categoria AS id', 'pc.id_producto_categoria', 'pc.nombre');
        $condicion = ' producto_categoria_id =' . $parametros->id;
        $categorias = $this->db->select($campos, $tablas, $condicion);

        require_once PATH_APP_PLUGINS . APP_PLUGIN_OPERACIONES_VIEW;
        OperacionesHTML::pintar_categorias($categorias, $this->modulo);
    }

    function trx_validar_cantidad() {
        $parametros = $this->gn->traer_parametros('GET');

        if ($this->modulo == 'compras') {
            echo $parametros->cantidad;
        } else {
            $tablas = 'producto_stock ps';
            $campos = array('ps.stock');
            $condicion = ' ps.producto_id = ' . $parametros->id;
            $producto = $this->db->selectOne($campos, $tablas, $condicion);

            if (!isset($producto['stock'])) {
                echo 0;
            } else if ($parametros->cantidad > $producto['stock']) {
                echo $producto['stock'];
            } else {
                echo $parametros->cantidad;
            }
        }
    }
    function consultar_factura($id_mesa = 0, $id_transaccion = 0) {
        $parametros = $this->gn->traer_parametros('GET');

        if ($id_mesa == 0 && isset($parametros->id_mesa)) {
            $id_mesa = $parametros->id_mesa;
        }
        if ($id_transaccion == 0 && isset($parametros->transaccion)) {
            $id_transaccion = $parametros->transaccion;
        }

        $transaccion = $this->db->selectOne(
                array('t.fecha', "t.id_transaccion", "t.cuenta_id", "t.factura_numero", "t.impuesto", "t.subtotal", "t.servicio","t.domicilio", "t.descuento", "t.total", "c.punto_atencion_id AS mesa_id"), 'transaccion t LEFT JOIN cuenta c ON t.cuenta_id = c.id_cuenta ', '  '
                . ' t.id_transaccion=' . $id_transaccion
        );

        if (!isset($parametros->id_mesa)) {
            $id_mesa = $transaccion['mesa_id'];
        }

        $transacciones = $this->db->select(array('p.nombre_corto AS producto, ti.cantidad, ti.valor_unitario, (ti.cantidad*ti.valor_unitario) AS valor'), 'transaccion t, transaccion_item ti, producto p ', ' t.id_transaccion = ti.transaccion_id '
                . ' AND ti.producto_id = p.id_producto '
                . ' AND t.id_transaccion = ' . $id_transaccion, false, false, false, false);

        if ($transaccion['factura_numero'] > 0) {
            $consecutivo = $transaccion['factura_numero'];
        } else {
            $consecutivo = false;
        }
        
        $this->pintar_factura($id_mesa, $transaccion, $transacciones, $consecutivo);

        //header('location:?opcion=pedido_recoleccion_pos&a=mostrar_menu&id=' . $id_mesa);
    }
    function pintar_factura($id_mesa, $transaccion, $items, $consecutivo = false, $prefactura = false) {
        $marcablanca = $this->gn->get_data_loged('marca_blanca');
        $id_interlocutor = $this->gn->get_data_loged('id_interlocutor');
        if($id_mesa != 0){
            $info_mesa = $this->db->selectOne('concat(nombre," ") as mesa', 'mesa', 'id_mesa=' . $id_mesa);
        }
        $info_negocio = $this->db->selectOne('concat(nombre," ",apellido) as negocio,num_documento,direccion,telefono', 'fw_interlocutor', 'id_interlocutor=' . $id_interlocutor);
        $configuracion = $this->db->selectOne(array('regimen'), 'fw_interlocutor_configuracion', ' interlocutor_id = ' . $marcablanca);
        $resolucion = $this->db->selectOne(array('resolucion_numero', 'fecha_resolucion', 'factura_minimo', 'factura_maximo'), 'factura_numeracion', ' interlocutor_id = ' . $marcablanca);

        if (!isset($transaccion['id_transaccion'])) {
            $transaccion['id_transaccion'] = "";
        }
        $impresion = ' 
        <html>       
            <head>
                <title></title>
            </head>
        <body>';
        if ((int) $consecutivo > 0) {
            $impresion .= '<p>FACTURA No ' . $consecutivo . '</p>';
        } else {
            $impresion .= '<p>FACTURA No ' . $transaccion['id_transaccion'] . '</p>';
        }
        $impresion .= '
            <table style="font-size:14px; width:280px">';
                
        if (file_exists('media/img/logo_factura/' . $marcablanca . '.jpg')){
            $impresion .= '
                <tr>
                    <td colspan="3" style="text-align:center; font-size:26px;">
                        <img src="media/img/logo_factura/' . $marcablanca . '.jpg"  width="90" height="90" >
                    </td>
                </tr>';
        }
        $impresion .= '
                <tr>
                    <td colspan="3" style="text-align:center; font-size:26px;">' . $info_negocio['negocio'] . '</td>
                </tr>';
        if (!$prefactura) {
            $impresion .= '
                <tr>
                    <th style="font-size:16px;text-align:left;">NIT</th>
                    <td style="font-size:16px;padding-left: 10px;">' . $info_negocio['num_documento'] . '</td>
                </tr>
                <tr>
                    <th style="font-size:16px;text-align:left;">Direcci&oacute;n</th>
                    <td style="font-size:16px;padding-left: 10px;">' . $info_negocio['direccion'] . '</td>
                </tr>
                <tr>
                    <th style="font-size:16px;text-align:left;">Tel&eacute;fono</th>
                    <td style="font-size:16px;padding-left: 10px;">' . $info_negocio['telefono'] . '</td>
                </tr>';
        } else {
            $impresion .= '
                <tr>
                    <th style="font-size:16px;text-align:left;">CUENTA</th>
                    <td style="font-size:16px;padding-left: 10px;">' . $transaccion['cuenta_id'] . '</td>
                </tr>';
        }
        $impresion .= '
                <tr>
                    <th style="font-size:16px;text-align:left;">Fecha</th>
                    <td style="font-size:16px;padding-left: 10px;">' . $transaccion['fecha'] . '</td>
                </tr>';
        if($id_mesa != 0){
            $impresion .= '
                    <tr>
                        <th style="font-size:16px;text-align:left;">Mesa</th>
                        <td style="font-size:16px;padding-left: 10px;">' . $info_mesa['mesa'] . '</td>
                    </tr>';
        }
        $impresion .= '
            </table>
            <br>
            <table id="factura_print_detalle" style="font-size:13px; width:280px">
                <tr>
                    <th style="border-top:1px dashed;border-bottom:1px dashed;">Producto</th>
                    <th style="border-top:1px dashed;border-bottom:1px dashed;">Cant.</th>
                    <th style="border-top:1px dashed;border-bottom:1px dashed;">V/U</th>
                    <th style="border-top:1px dashed;border-bottom:1px dashed;">Subtotal</th>
                </tr>';
        $total = 0;
        foreach ($items as $key => $trx) {
            $impresion .= ''
                    . '<tr>';
            $total += $trx['valor'];
            foreach ($trx as $k => $v) {
                $impresion .= ''
                        . '<td style="text-align:center">';
                if ($k == 'valor' || $k == 'valor_unitario') {
                    $v = "$" . number_format($v);
                }
                $impresion .= $v;
                $impresion .= '</td>';
            }
            $impresion .= ''
                    . '</tr>';
        }

        $impresion .= '
                </tr> 
                <tr>
                    <th style="border-top:1px dashed"></th>
                    <th style="border-top:1px dashed">Subtotal </th>
                    <th colspan="2" style="border-top:1px dashed; text-align:right;font-size: 13px;">$' . number_format($transaccion['subtotal']) . ' </th>
                </tr>';
        if ($configuracion['regimen'] == REGIMEN_COMUN) {
            $impresion .= '
                <tr>
                    <th></th>
                    <td style="text-align:right;font-size: 12px;">Impoconsumo </td>
                    <td colspan="2" style="text-align:right;font-size: 12px;">$' . number_format($transaccion['impuesto']) . ' </td>
                </tr>';
        }
        if (!$prefactura) {
            $impresion .= '
                <tr>
                    <th></th>
                    <td style="text-align:right;font-size: 12px;">Servicio </td>
                    <td colspan="2" style="text-align:right;font-size: 13px;">$' . number_format($transaccion['servicio']) . ' </td>
                </tr>
                <tr>
                    <th></th>
                    <td style="text-align:right;font-size: 12px;">Domicilio </td>
                    <td colspan="2" style="text-align:right;font-size: 13px;">$' . number_format($transaccion['domicilio']) . ' </td>
                </tr>
                <tr>
                    <th></th>
                    <td style="text-align:right;font-size: 12px;">Descuento </td>
                    <td colspan="2" style="text-align:right;font-size: 13px;">-$' . number_format($transaccion['descuento']) . ' </td>
                </tr>
                <tr>
                    <th style="border-top:1px dashed;border-bottom:1px dashed;"></th>
                    <th style="border-top:1px dashed;border-bottom:1px dashed;">TOTAL </th>
                    <th colspan="2" style="text-align:right;font-size: 22px;border-top:1px dashed;border-bottom:1px dashed;">$' . number_format($transaccion['total']) . ' </th>
                </tr>';
        }
        if ($configuracion['regimen'] == REGIMEN_COMUN) {
            $impresion .= '
                <tr>
                    <td colspan="4" style="text-align:center;font-size: 15px;">' . utf8_decode('Regimen Com&uacute;n del impuesto nacional al consumo') . ' </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align:center;font-size: 15px;">' . utf8_decode('Resoluci&oacute;n DIAN No. ') . $resolucion['resolucion_numero'] . ' </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align:center;font-size: 15px;">Fecha ' . $resolucion['fecha_resolucion'] . ' </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align:center;font-size: 15px;"> Desde ' . $resolucion['factura_minimo'] . ' Hasta ' . $resolucion['factura_maximo'] . ' </td>
                </tr>';
        }
        $impresion .= '
            </table>        
            <button class="nover" style="margin-top: 20px;" onclick="window.print();">IMPRIMIR</button>
            <style type="text/css" media="print">
                .nover {
                        display:none;
                }
                table#factura_print_detalle tr td{
                        padding: 2px 5px;
                }
            </style>
        </html>';
        $factura = utf8_encode($impresion);
        print_r($factura);
        //$this->imprimir_pdf();

        exit;
    }

}
