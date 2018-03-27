<?php

class stdModule {

    const STD_LOG = '5';
    const STD_CTRL_BASIC = '4';
    const ONLYCTRL_IMAGE = '3';
    const CUSCTRL_FOREING = '2';
    const CUSCTRL_IMAGE = '1';
    const STD_CTRL = '0';
    
    const EDIT = 'editar';
    const CREATE = 'crear';
    const DELETE = 'eliminar';
    const STATUS = 'estado';
    const ORDER = 'orden';
    
    const ALL = 'crud';
    const CRUD_ONLY_EDIT = 'only_edit';
    const CRUD_EDIT = 'edit';

    var $contentType = "";
    var $registros_mostrar = 10;
    var $vista = "classes/stdBill.html.php";
    var $modulo = "";
    var $clase = "";
    private $controles = array(); // Row controls, std or custom
    private $custom_controles = array(); // Row controls, std or custom
    private $CRUD = array(); // Row controls, std or custom
    private $listas_foraneas = array();
    private $llaves_foraneas = array();
    private $default = '';
    private $editFooter = '';
    private $filtros = array('id'=>'1', 'nombre'=>'1');
    
    
    function getContenido($a) {
        $this->contentType = "";
       
        switch ($a) {
            case "submenu":
                if($this->default == 'formEdicion')
                    $this->formEdicion();
                else
                    $this->submenu();
                break;
            case "formEdicion":
                $this->formEdicion();
                break;
            case "guardarItem":
                $this->guardarItem();
                break;
            case "editarItem":
                $this->editarItem();
                break;
            case "eliminarItem":
                $this->eliminarItem();
                break;
            case "publicarItem":
                $this->publicarItem();
                break;
            case "publicarPorCheck":
                $this->publicarPorCheck();
                break;
            case "editarOrden":
                $this->editarOrden();
                break;
            case "registrar":
                $this->registrar_trx();
                break;
            case "set_product":
                $this->setProduct();
                break;
            case "set_tipo":
                $this->setTipo();
                break;
            case "buscar":
                $this->buscar();
                break;
            default:
                $this->contentType = "html";
                if($this->default == 'formEdicion')
                    $this->formEdicion();
                else
                    $this->submenu();
        }
    }
    function  setConfigs($default, $editFooter){
        $this->default = $default;
        $this->editFooter = $editFooter;
    }
    function addControls($type, $controls = NULL, $foreing = '') {

        switch ($type) {
            case self::CUSCTRL_IMAGE:
                $this->controles = array('nombre', 'descripcion', 'imagen', 'estado', 'orden');
                break;
            
            case self::ONLYCTRL_IMAGE:
                $this->controles = array('nombre', 'imagen', 'estado', 'orden');
                break;
            case self::STD_CTRL_BASIC: //4
                $this->controles = array('nombre', 'estado', 'orden');
                break;
            case self::STD_LOG: //4
                $this->controles = array('fecha', 'hora', 'estado');
                break;
            default: // Default to text
                $this->controles = array('nombre', 'descripcion', 'estado', 'orden');
                break;
        }
        $this->addCustomControls($controls);
    }
    function addCustomControls($controls) {
        $campo = New stdClass();
        foreach ($controls as $key => $value) {
            $campo = New stdClass();
            $campo->nombre = $key;
            $campo->tipo = $value;
            $this->custom_controles[] = $campo; 
        }
    }
    function addList($nombre, $tabla, $foranea, $campos_adicionales = "", $orden = ' orden ', $where = '', $alias = ''){
        $lista = New stdClass();
        $lista->lista = $nombre;
        $lista->alias = $alias;
        $lista->tabla = $tabla;
        $lista->campos_adicionales = $campos_adicionales;
        $lista->where = $where;
        $lista->orden = $orden;
        $this->listas_foraneas[] = $lista;
        $this->llaves_foraneas[$nombre] = $foranea;
    }
    function getLists() {
        $listas = array();
        foreach ($this->listas_foraneas as  $value) {
            $listas[$value->lista] = $this->datos_lista($value->tabla, $value->alias.'id, '.$value->alias.'nombre '.$value->campos_adicionales, $value->orden, $value->where, $value->alias);
        }
        return $listas;
    }
    
    function getControls($tipo = 'SELECT', $edit = "") {
        $return = New stdClass();
        $return->indices = "";
        $return->campos = "";
        switch ($tipo) {
            case 'SELECT':
                $return->valores = "id";
                break;
            case 'INSERT':
                $return->valores = "id";
                $return->campos = "NULL";
                break;
            case 'UPDATE':
                $return->valores = array();
                break;
            default:
                break;
        }
        
        foreach ($this->controles as $key => $value){
            $return->indices.= $key;
            switch ($tipo) {
                case 'SELECT':
                    $return->valores.= ", ".$value;
                    break;
                case 'UPDATE':
                    $return->valores[]=$value;
                    break;
                default:
                    break;
            }
        }
        foreach ($this->custom_controles as $key => $value){
            $return->indices.= $key;
            switch ($tipo) {
                case 'SELECT':
                    $return->valores.= ", ".$value->nombre;
                    break;
                case 'INSERT':
                    $return->campos .= ", '"."$"."parametros->".$value->nombre."' ";
                    $return->valores.= ", ".$value->nombre;
                    break;
                case 'UPDATE':
                    $return->valores[] = $value->nombre;
                    break;
                default:
                    break;
            }
        }
        if($edit == 'EDIT'){
            foreach ($this->llaves_foraneas as $key => $value){
                $return->indices.= $key;
                switch ($tipo) {
                    case 'SELECT':
                        $return->valores.= ", ".$value;
                        break;
                    case 'INSERT':
                        $return->campos .= ", '"."$"."parametros->".$value->nombre."' ";
                        $return->valores.= ", ".$value->nombre;
                        break;
                    case 'UPDATE':
                        $return->valores[] = $value;
                        break;
                    default:
                        break;
                }
            }
        }
        return $return;
    }
    function setCrud($type = 'crud') {

        switch ($type) {
            case self::ALL:
                $this->CRUD = array( self::EDIT, self::CREATE, self::DELETE, self::STATUS, self::ORDER);
                break;
            case self::CRUD_ONLY_EDIT :
                $this->CRUD = array( self::EDIT, self::ORDER);
                break;
            case self::CRUD_EDIT :
                $this->CRUD = array( self::EDIT);
                break;
            
            default: // Default to text
                $this->CRUD = array( self::EDIT, self::CREATE, self::DELETE, self::STATUS, self::ORDER);
                break;
        }
    }
    function getCrud($type = 'estado') {
        return in_array($type , $this->CRUD);
    }
    
    function getClassName($file) {
        $file = explode('\\', $file);
        //$file = explode('/', $file);
        $file = $file[(count($file) - 1)];
        $file = substr($file, 0, -4);
        return $file;
    }

    function submenu() {
        include_once 'classes/funciones.php';
        $fx = New Funciones();

        $inicio = 0;
        $select = $this->getControls();
        
        if (isset($_GET['pagina']) && $_GET['pagina'] != 0)
            $inicio = (mysql_real_escape_string($_GET['pagina']) - 1) * $this->registros_mostrar;

        $sql = "SELECT $select->valores
                FROM $this->modulo
                WHERE estado != 'E'
                ORDER BY orden ASC
                LIMIT $inicio,$this->registros_mostrar";

        $result = mysql_query($sql) or die("Error: " . mysql_error() . ". Linea: " . __LINE__ . ", Funcion: " . __FUNCTION__);

        $select = "id";
        $tabla = $this->modulo;
        $where = "WHERE estado != 'E'";
        $group_by = "";
        $order_by = "ORDER BY orden ASC";

        $datos = array();

        while ($dato = mysql_fetch_object($result)) {
            $datos[] = $dato;
        }

        include($this->vista);
        ClaseHTML::submenu($this->clase, $this->CRUD, $datos);
        $fx->mostrarPaginacion($select, $tabla, $where, $group_by, $order_by);
    }

    function formEdicion() {
        include($this->vista);
        include_once 'classes/funciones.php';
        $fx = New Funciones();
        
        $tipo = $fx->getDataLoged('bill_tipo');
        if($tipo == NULL)
            $tipo = '1';
        
        $id_item = "";
        $dato = NULL;
        $listas = NULL;
        
        $select = $this->getControls('SELECT', 'EDIT');
        $listas = $this->getLists();
        
        ClaseHTML::formEdicion($this->clase, $listas, $id_item, $tipo);
    }
    function registrar(){
        include_once 'classes/funciones.php';
        $fx = New Funciones();
        $bill = $fx->getDataLoged('bill');
        $parametros = $fx->getParametros('GET');
        $query_update_stock = array();
        $operacion = " - ";
        
        if(!isset($bill->productos)){
            echo 'Debe seleccionar un producto como minimo';
            return;
        }
        if(!isset($parametros->fecha) || $parametros->fecha == '')
            $parametros->fecha = DATE('Y-m-d');
        
        $query = "INSERT INTO ventas ( cliente, identificacion, direccion, telefono, fecha, hora) 
                  values( '$parametros->cliente', '$parametros->cc','$parametros->dir', '$parametros->tel', '$parametros->fecha', NOW());";
        
        if((int)$parametros->tipo === 2){
            $query = "INSERT INTO compras ( vendedor_id, fecha, hora) 
                      values( '$parametros->vendedor', NOW(), NOW());";
            
            $operacion = " + ";
        }
        
        $result = mysql_query($query) or die("Error: " . mysql_error() . ". Linea: " . __LINE__ . ", Funcion: " . __FUNCTION__);
        if(!$result){
            echo 'error';
            return;
        }
        $trans_id = mysql_insert_id();
        
        $query_items ="INSERT INTO ventas_item ( venta_id, producto_id, cantidad, valor_unitario) values ";
        if((int)$parametros->tipo === 2){
            $query_items ="INSERT INTO compras_item ( compra_id, producto_id, cantidad, valor_unitario) values ";
            
        }
        foreach ($bill->productos as $key => $value) {
            $query_items .="( $trans_id, $key, $value->cantidad, $value->valor ), "; 
            $query_update_stock[] = " UPDATE productos SET cantidad = cantidad $operacion $value->cantidad 
                    WHERE id = $key ; ";
        }
        
        $query_items = substr($query_items, 0, -2);
        $query_items .= ";";
        
        $result_items = mysql_query($query_items) or die("Error: " . mysql_error() . ". Linea: " . __LINE__ . ", Funcion: " . __FUNCTION__);
        if(!$result_items){
            echo 'error';
            return;
        }
        foreach ($query_update_stock as $sql) {
            $result_update = mysql_query($sql) or die("Error: " . mysql_error() . ". Linea: " . __LINE__ . ", Funcion: " . __FUNCTION__);
            if(!$result_update){
                echo 'error';
                return;
            }
        }
        $fx->setDataLoged('bill', NULL);
        $fx->setDataLoged('msj', 'Transaccion realizada correctamente');
        echo 'ok';
        return;
    }
    function registrar_trx(){
        include_once 'classes/funciones.php';
        $fx = New Funciones();
        $bill = $fx->getDataLoged('bill');
        $parametros = $fx->getParametros('GET');
        $query_update_stock = array();
        
        $usuario = $_SESSION["AID"];
        if(!isset($bill->productos)){
            echo 'Debe seleccionar un producto como minimo';
            return;
        }
        if(!isset($parametros->fecha) || $parametros->fecha == ''){
            $parametros->fecha = DATE('Y-m-d');
        }
        
        $operacion = " + ";
        if((int)$parametros->tipo === 1 || (int)$parametros->tipo === 4 ) {
            $operacion = " - ";
            $query_acreedor = "INSERT INTO acreedores (`nombre`, documento, `direccion`, `telefono`, `estado`) "
                    . "VALUES ('$parametros->acreedor', '$parametros->cc','$parametros->direccion', '$parametros->telefono', '1');";
            $result_acreedor = mysql_query($query_acreedor) or die("Error: " . mysql_error() . ". Linea: " . __LINE__ . ", Funcion: " . __FUNCTION__);
            if(!$result_acreedor){
                echo 'error';
                return;
            }
            $parametros->actor_id = mysql_insert_id();
        }
        
        $query = "INSERT INTO transacciones ( tipo_id, responsable_id, actor_id, estado_id, fecha, hora, observacion) 
                  values( $parametros->tipo, $usuario, $parametros->actor_id, 1, '$parametros->fecha', NOW(), '$parametros->obs');";
        //echo $query;
        $result = mysql_query($query) or die("Error: " . mysql_error() . ". Linea: " . __LINE__ . ", Funcion: " . __FUNCTION__);
        if(!$result){
            echo 'error';
            return;
        }
        $trans_id = mysql_insert_id();
        
        $query_items ="INSERT INTO transacciones_item ( transaccion_id, producto_id, cantidad, valor_unitario, estado_id) values ";
        
        foreach ($bill->productos as $key => $value) {
            if(($value->valor == '')){
                $value->valor = 0;
            }
            $query_items .="( $trans_id, $key, $value->cantidad, $value->valor, 1 ), "; 
            $query_update_stock[] = " UPDATE productos SET cantidad = cantidad $operacion $value->cantidad 
                    WHERE id = $key ; ";
        }
        
        $query_items = substr($query_items, 0, -2);
        $query_items .= ";";
        
        $result_items = mysql_query($query_items) or die("Error: " . mysql_error() . ". Linea: " . __LINE__ . ", Funcion: " . __FUNCTION__);
        if(!$result_items){
            echo 'error';
            return;
        }
        foreach ($query_update_stock as $sql) {
            $result_update = mysql_query($sql) or die("Error: " . mysql_error() . ". Linea: " . __LINE__ . ", Funcion: " . __FUNCTION__);
            if(!$result_update){
                echo 'error';
                return;
            }
        }
        $fx->setDataLoged('bill', NULL);
        $fx->setDataLoged('msj', 'Transaccion realizada correctamente');
        echo 'ok';
        return;
    }
    function setProduct(){
        include_once 'classes/funciones.php';
        $fx = New Funciones();
        $parametros = $fx->getParametros('GET');
        $bill = $fx->getDataLoged('bill');

        if(isset($parametros->tipo)){
            $fx->setDataLoged('bill_tipo', $parametros->tipo);
        }
        if($parametros->metodo == 'del' ){
            $fx->setDataLoged('bill', NULL);
            echo 'ok';
        }else{
        
            if($bill == NULL)
                $bill = New stdClass();

            if(isset($parametros->metodo) && $parametros->metodo == 'add'){

                if(isset($bill->productos[$parametros->id])){
                    $bill->productos[$parametros->id]->cantidad = $parametros->cantidad; 
                    $bill->productos[$parametros->id]->valor = $parametros->valor; 
                }else{
                    $bill->productos[$parametros->id]->cantidad = $parametros->cantidad; 
                    $bill->productos[$parametros->id]->valor = $parametros->valor; 
                }
            }else{
                unset($bill->productos[$parametros->id]); 
            }
            $fx->setDataLoged('bill', $bill);
            echo 'ok';
        }
    }

    function datos_item($id, $tabla = '') {
        if ($tabla === '')
            $tabla = $this->modulo;
        $sql = "SELECT *
                FROM $tabla
                WHERE id = '$id'
                LIMIT 1";

        $result = mysql_query($sql) or die("Error: " . mysql_error() . ". Linea: " . __LINE__ . ", Funcion: " . __FUNCTION__);
        $item = mysql_fetch_object($result);
        return $item;
    }

    function datos_lista($tabla = '', $select = '*', $orden = ' orden ', $where = '', $alias = '') {
        if ($tabla === '')
            $tabla = $this->modulo;
        $sql = "SELECT $select
                FROM $tabla
                WHERE ".$alias."estado = 'A' $where
                ORDER BY $orden
                ";
        
        $result = mysql_query($sql) or die("Error: " . mysql_error() . ". Linea: " . __LINE__ . ", Funcion: " . __FUNCTION__);
        while ($row = mysql_fetch_object($result)) {
            $datos[] = $row;
        }
        return $datos;
    }
    function buscar(){
        include($this->vista);
        include_once 'classes/funciones.php';
        $fx = New Funciones();
        $parametros = $fx->getParametros('GET');
        $where = ''; 
        
        if(isset($parametros->id) && $parametros->id > 0){
            $where .= " AND p.id = '".$parametros->id."' "; 
        }
        if(isset($parametros->nombre) && $parametros->nombre != ''){
            $where .= " AND p.nombre LIKE '".$parametros->nombre."%' "; 
        }
        
        $listas['PRODUCTOS'] = $this->datos_lista('productos p LEFT JOIN referencia_color rc ON p.color_id = rc.id ', 'p.id, p.nombre, rc.nombre as color, p.cantidad, p.valor_compra, p.valor_venta , p.cantidad ', ' p.nombre', " ".$where, 'p.');
        
        ClaseHTML::busqueda($listas);
    }
}

?>