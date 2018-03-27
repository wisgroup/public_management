<?php
require_once('fw/controlador/php/modulos/maestro.class.php');

class Opcion extends Maestro{

    public function __construct($db = "",$modulo ="") {
        $this->modulo = strtolower(__CLASS__);
        $this->nombre_tabla = 'fw_'.strtolower(__CLASS__);
        $this->EXITO_EDICION  = "048";
        $this->EXITO_CREACION = "047";
        $this->EXITO_ELIMINAR = "049"; 
        // $this->encabezado_edicion =  array(array('nombre'),'contacto','id_contacto=%s');
        parent::__construct($db ,$this->modulo);

        $id_interlocutor = $this->gn->get_data_loged('id_interlocutor');
        $id_usuario = $this->gn->get_data_loged('id_usuario');

        $campos_formulario['id_'.$this->nombre_tabla]=  array('tipo'=>'hidden','complemento'=>'required');
        $campos_formulario['descripcion']=              array('tipo'=>'text','complemento'=>'required');
        $campos_formulario['titulo']=                   array('tipo'=>'text','complemento'=>'required');
        $campos_formulario['opcion_id']=                array('tipo'=>'select', 'valor'=> '', 'complemento'=> 'required', 'campo'=>'descripcion', 'condicion'=>'id_opcion IN (1, 2 ,3, 4)','label'=>'Opcion Menu');
        $campos_formulario['imagen']=                   array('tipo'=>'file','valor'=> 'administracion.png'); 
        $campos_formulario['estado_id']=                array('tipo'=>'hidden','valor'=>'2','complemento'=>'required', 'campo'=>'descripcion', 'condicion'=>'id_estado IN (1,2)');
        $this->campos_formulario = $campos_formulario;
    }

    function home() {

        $campos = array('p.id_'.$this->nombre_tabla.' as Codigo','p.descripcion','p.imagen', 'p.estado_id estado');
        $tablas = $this->nombre_tabla." p";
        $condicion = "p.estado_id <>".self::ESTADO_ELIMINADO;

        $function= array('Activo' => 'radio_estado');
        $accion= array('radio_estado'=>'');

        $this->iniciar_maestro_funcion($campos, $tablas, $condicion,'Opciones '.date('Y-m-d H.i.s'),$accion,$function);
    }

    function cambiar_estado(){

        $this->tipo_contenido = 'ajax';
        $parametros = $this->gn->traer_parametros('GET');

        $condicion = str_replace("campo","id_".$this->nombre_tabla,$parametros->id);

        $proveedores = $this->db->select(array('id_proveedor','estado_id'), 'proveedor', $condicion);

        foreach ($proveedores as $key => $value) {

            if ($value['estado_id']==self::ACTIVO) {
                $this->nc->set_notificacion('Desactive primero el proveedor que desa eliminar.');
                $this->home();
                exit();
            }
        }
        parent::cambiar_estado(true, $parametros);
    }

    function guardar_edicion() {
        $this->tipo_contenido = '';
        $parametros = $this->gn->traer_parametros('POST');

        $valor = str_replace('.', '',$parametros->precio_compra);
        $valor = str_replace(',', '.',$valor);

        $parametros->precio_compra=$valor;
        // print_r($parametros);

        parent::guardar_edicion(true, $parametros);

    }


    function formulario_edicion($campos_formulario = null) {

        if($campos_formulario === null){
            $campos_formulario = $this->campos_formulario;
        }
        
        $campos = array();
        $campos_personalizados = array();
        
        foreach ($campos_formulario as $key => $input) {                     //Seteando el array con le nombre de los campos

            if (!in_array('personalizado',$input) ) {
                array_push($campos,$key);
            } else {
                array_push($campos_personalizados, $key);
            }
        }

        $this->tipo_contenido = '';
        $accion = "guardar_creacion";
        $parametros = $this->gn->traer_parametros('GET');
        $encabezado=$this->encabezado_creacion;
        
        if(isset($parametros->type)){
            $this->tipo_contenido = $parametros->type;
        }

        if (isset($parametros->id) && (int) $parametros->id > 0) {
            $accion = "guardar_edicion";

            if (is_array($this->encabezado_edicion)) {

                $p = $this->db->selectone($this->encabezado_edicion[0],$this->encabezado_edicion[1],sprintf($this->encabezado_edicion[2],$parametros->id));
                $encabezado='';
                foreach ($p as $key => $value) {
                    $encabezado=$encabezado.$value." ";
                }
                
            }else{
               $encabezado=$this->encabezado_edicion; 
            }

            $elemento = $this->db->selectone($campos, $this->nombre_tabla, $parametros->id . ' = id_' . $this->nombre_tabla);
            
            foreach ($elemento as $key => $value) {                 //Guardando el valor del input en el array $campos_formulario
                
                if ($key=='precio_compra') {
                    $campos_formulario[$key]['valor'] = number_format($value,2,',','.');
                }else{
                   $campos_formulario[$key]['valor'] = $value; 
                }
            }
        }

        $retorno='';
        require_once("fw/vista/html/maestro2.html.php");
        FormHTML::formulario_dinamico($campos_formulario, $accion, $this->modulo, $this->db, $campos_personalizados, $parametros,$retorno,$encabezado);
    }    
}