<?php


class Validacion {

    private $db;
    private $gn;
    private $ruta_cron='';
    private $config;

    CONST ESTADO_ACTIVO = 1;
    CONST ESTADO_BLOQUEADO = 2;
    CONST ESTADO_ELIMINADO = 3;

    private $modulos_por_defecto = array('inicio' =>'inicio','subopcion'=>'subopcion','migadepan'=>'migadepan','usuario_cambio_clave'=>'usuario_cambio_clave');


    function __construct($db,$gn,$ruta_cron='') {
        $this->db = $db;
        $this->ruta_cron=$ruta_cron;
        $this->gn =$gn;

    }


    function traer_permisos_db(){
 
        $clase = $this->gn->get_data_loged('clase');
 
        $subopciones_permiso = $this->db->select(array('oc.opcion_id',  'o.titulo'), 'fw_opcion_clase oc, fw_opcion o'," oc.opcion_id = o.id_opcion and oc.interlocutor_clase_id=".$clase." and oc.estado_id=".self::ESTADO_ACTIVO." and o.estado_id=".self::ESTADO_ACTIVO);
        $campos_permiso=array();
        foreach ($subopciones_permiso as $key => $value) {
            $campos_permiso[$value['opcion_id']]=$value['titulo'];
        }
        return $campos_permiso;
    }


    function obtener_permisos(){

        //$permisos = $this->gn->get_data_loged('permisos');

        if (empty($permisos)) {
            $permisos = $this->traer_permisos_db();
            $this->gn->set_data_loged('permisos',$permisos);
        } 

        return $permisos;
    }


    function validar_permisos_modulo($modulo){
        if ($modulo === '') { 
            return true; 
        }else{ 
            $permisos = $this->obtener_permisos();
            //echo "<pre>";
    //print_r($permisos);
            if ( in_array($modulo,$permisos) or in_array($modulo,$this->modulos_por_defecto)) {
                return true;
            }else{ return false; }
        }
    }




}

?>
