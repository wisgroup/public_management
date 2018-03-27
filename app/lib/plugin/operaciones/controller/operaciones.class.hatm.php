<?php

require_once(PATH_PLUGIN_MODULO);

class Operaciones extends Modulo {

    CONST ESTADO_ELIMINADO = 3;
    CONST VENTA = 1;
    CONST COMPRA = 2;
    CONST PROVEEDOR = 3;
    CONST CLIENTE = 4;

    public $transaccion_tipo ='';
    public $comercio_clase ='';
    public $encabezado = array();
    public $encabezado_flotante = array();
    
    public function __construct($db = "", $modulo = "") {
        parent::__construct($db,$modulo);
    }

    function home($titulo = '') {
        $this->nc->verificar_notificacion();
        $this->pintar_transacciones($this->modulo, $titulo);
    }

    function pintar_transacciones($modulo = '', $titulo= '') { 
        $interlocutor = $this->gn->get_data_loged('id_interlocutor');
        $marca_blanca = $this->gn->get_data_loged('marca_blanca');
        $comercios = $this->db->select(array('id_interlocutor',"concat(nombre,' ',apellido) as nombre"), 'fw_interlocutor', 
            ' interlocutor_clase_id ='.$this->transaccion_tipo.' AND  interlocutor_id='.$interlocutor );
        
        $categorias = $this->db->select(array(
                    'nombre',
                    "id_producto_categoria",
                    "producto_categoria_id"
                ),
                'producto_categoria', 
                ' marca_blanca ='.$marca_blanca.' '
                . ' AND  estado_id='.ESTADO_ACTIVO ." ORDER BY nombre "
            );

        $c = $this->db->select(array('nombre'),'fw_interlocutor',' interlocutor_clase_id ='.$this->transaccion_tipo);
        $medio = $this->db->select(array('nombre'),'transaccion_pago_medio');
        $resumen = New stdClass();
        $resumen->total = New stdClass();
        $resumen->total->subtotal = 0;
        $resumen->total->impuestos = 0;
        $resumen->total->total = 0;
        $this->tipo_contenido = 'html';
        require_once PATH_APP_PLUGINS.APP_PLUGIN_OPERACIONES_VIEW;
        OperacionesHTML::formulario($comercios, $modulo,$this->encabezado, $titulo, $categorias, $resumen,$c,$medio);
    }

    function traer_productos() {
        $this->pintar_tabla_ajax($this->encabezado_flotante,'app/pack/transaccion/'.$this->modulo.'/controller/'.$this->modulo.'_ajax.php');    
        //$this->pintar_tabla_ajax($this->encabezado_flotante,'?opcion=compras&a=consultar_productos');    
    }
    function select_cat() {
        $parametros = $this->gn->traer_parametros('GET');
        if($this->modulo == "compras"){
            $producto_tipo = PDCT_INSUMO;
        }else{
            $producto_tipo = PDCT_FABRICADO;
        }
        
        $productos = $this->db->select(array(
                    'p.id_producto',
                    'p.nombre',
                    'p.precio',
                    'ps.stock',
                    "p.producto_categoria_id"
                ),
                'producto p, producto_stock ps', 
                ' p.producto_categoria_id ='.$parametros->id.' '
                .' AND p.id_producto = ps.producto_id '
                . ' AND p.producto_tipo_id IN ('.PDCT_PREFABRICADO.', '.$producto_tipo.') '
                . ' AND  p.estado_id='.ESTADO_ACTIVO ." ORDER BY p.nombre "
            , false, false, false);
        
        require_once PATH_APP_PLUGINS.APP_PLUGIN_OPERACIONES_VIEW;
        OperacionesHTML::tab_productos($this->modulo, $productos);
    }

    public function traer_comercios(){

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

        $num_clientes = $this->db->select(array($campo),
                            'fw_interlocutor',
                            "interlocutor_id='".$interlocutor."' AND interlocutor_clase_id=".$this->comercio_clase." AND ".$campo." like '%".$parametros->term."%'");
        
        foreach ($num_clientes as $key => $value) {
            $clientes[] = $value[$campo];
        }

       echo json_encode($clientes);
    }

    public function setear_campos(){
        $parametros = $this->gn->traer_parametros('GET');
        $interlocutor =$parametros->interlocutor;

        $cliente = $this->db->selectOne(array('id_interlocutor','nombre','apellido','telefono','celular','email','direccion'),'fw_interlocutor',
             "num_documento='".$interlocutor."' AND interlocutor_clase_id=".$this->comercio_clase);
        if ($cliente) {
            $cliente['respuesta'] = 1;
            echo json_encode($cliente);
        }else{
            echo json_encode(array('respuesta'=>0));
        }
    }
    
    


    function trx_agregar_producto($campo) {

        $id_interlocutor = $this->gn->get_data_loged('id_interlocutor');
        $parametros = $this->gn->traer_parametros('GET');

        $tablas = 'producto p,producto_stock ps';
        $campos = array('p.id_producto as COD', 'p.referencia','p.barcode', 'p.nombre as PRODUCTO', 'p.iva', 'p.iva_incluido', 'ps.stock as STOCK', 'p.'.$campo.' as precio');
        $condicion = ' interlocutor_id=' . $id_interlocutor . ' '
                . ' AND estado_id <> 3 '
                . ' AND ps.producto_id=p.id_producto';
        $condicion .= ' AND p.id_producto = ' . $parametros->id;
        $producto = $this->db->selectOne($campos, $tablas, $condicion);

        
        if ($producto['iva_incluido'] === 'N') {
            $producto['iva_valor'] = $producto['precio'] * ( $producto['iva'] / 100 );
            $producto['precio'] = $producto['precio'] + $producto['iva_valor'];
        }else{
            $producto['precio'] = $producto['precio'];
            $producto['iva_valor'] = 0;
        }
        $producto['subtotal'] = $producto['precio'] * $parametros->cantidad;
        
        require_once PATH_APP_PLUGINS.APP_PLUGIN_OPERACIONES_VIEW;
        OperacionesHTML::trx_agregar_producto($producto, $parametros->cantidad);
    }
    function trx_agregar_producto_x_codigo($campo = NULL) {
        $id_interlocutor = $this->gn->get_data_loged('id_interlocutor');
        $parametros = $this->gn->traer_parametros('GET');

        $tablas = 'producto p,producto_stock ps';
        $campos = array('p.id_producto as COD', 'p.referencia','p.barcode', 'p.nombre as PRODUCTO', 'p.iva', 'p.iva_incluido', 'ps.stock as STOCK', 'p.'.$campo.' as precio');
        $condicion = ' interlocutor_id=' . $id_interlocutor . ' '
                . ' AND estado_id <> 3 '
                . ' AND ps.producto_id=p.id_producto';
        $condicion .= ' AND p.barcode = ' . $parametros->codigo;
        $producto = $this->db->selectOne($campos, $tablas, $condicion, false, true);
        if($campo!= NULL){
            $producto = $this->db->selectOne($campos, $tablas, $condicion, false, true);

            if ($producto['iva_incluido'] === 'N') {
                $producto['iva_valor'] = $producto['precio'] * ( $producto['iva'] / 100 );
                $producto['precio'] = $producto['precio'] + $producto['iva_valor'];
            }else{
                $producto['precio'] = $producto['precio'];
                $producto['iva_valor'] = 0;
            }
            $producto['subtotal'] = $producto['precio'] * $parametros->cantidad;
            require_once PATH_APP_PLUGINS.APP_PLUGIN_OPERACIONES_VIEW;
            OperacionesHTML::trx_agregar_producto($producto, $parametros->cantidad);
        }
    }
    
    function validar_stock(){
        $parametros = $this->gn->traer_parametros('GET');
        
        //TODO: validar si es compra o venta.
        //TODO: validar si esta en el rango de stock maximo y minimo.
        $tablas = 'producto_stock ps';
        $campos = array('ps.producto_id', 'ps.stock');
        $condicion = ' ps.producto_id = ' . $parametros->id;
        $producto = $this->db->selectOne($campos, $tablas, $condicion);
        
        //TODO: Modificar la validacion deacuerdo si es de entrada o salida la trx
        if($producto['stock'] > $parametros->cantidad ){
            echo "OK";
        }else{
            echo "No existe cantidad suficiente de este producto";
        }
    }
    
}



