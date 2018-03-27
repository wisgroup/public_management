<?php

class baseObj {

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
    const CRUD_ORDERED_EDIT = 'ordered_edit';
    const CRUD_EDIT = 'edit';

    var $contentType = "";
    var $registros_mostrar = 10;
    var $modulo = "";
    var $clase = "";
    var $vista = "classes/stdReport.html.php";
    private $controles = array(); // Row controls, std or custom
    private $custom_controles = array(); // Row controls, std or custom
    private $CRUD = array(); // Row controls, std or custom
    private $listas_foraneas = array();
    private $llaves_foraneas = array();
    private $default = '';
    private $editFooter = '';
    private $filtros = array('id'=>'1', 'nombre'=>'1');
    
    function setFilter($filtros){
        foreach ($filtros as $f) {
            $this->filtros[$f] = '1';
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
    function addList($nombre, $tabla, $foranea){
        $lista = New stdClass();
        $lista->lista = $nombre;
        $lista->tabla = $tabla;
        $this->listas_foraneas[] = $lista;
        $this->llaves_foraneas[$nombre] = $foranea;
    }
    function getLists() {
        $listas = array();
        foreach ($this->listas_foraneas as  $value) {
            $listas[$value->lista] = $this->datos_lista($value->tabla, 'id, nombre');
        }
        return $listas;
    }
    
    function getControls($tipo = 'SELECT', $edit = "") {
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
            case self::CRUD_ORDERED_EDIT :
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
}

?>