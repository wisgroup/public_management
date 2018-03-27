<?php

require_once('fw/controlador/php/modulos/modulo.class.php');



class MigadePan extends Modulo {

    

    function home($seccion = 'inicio',$accion ="home") {

        require_once 'fw/vista/html/migadepan.html.php';

        $elementos = array($accion,$seccion);

        if($seccion == "inicio"){

            $elementos = array($accion,$seccion);
            print_r($elementos);exit;

        }

        

        MigadePanHTML::home($elementos, $seccion);

    }



    function actualizar_miga_pan(){

        $this->tipo_contenido = 'ajax';

        $parametros = $this->gn->traer_parametros('GET');

        

        if(!isset($parametros->id) || (int)$parametros->id === 0){

            $parametros->id = 1;

        }



        if(!isset($parametros->acc) || (int)$parametros->acc === 0){

            

            $parametros->acc = "&a=".$parametros->acc;

        }else{

            $parametros->acc = "";

            

        }



        $complemento = "&id=".$parametros->id.$parametros->acc;

        

        if($parametros->modulo === 'subopcion'){

            

            // $menu = $this->db->select(array('s.descripcion','s.titulo'),'opcion s',"s.id_opcion = $parametros->id AND s.estado_id = ".self::ESTADO_ACTIVO,'orden');

            $menu = $this->db->selectOne(array('s.descripcion','s.titulo'),'fw_opcion s',"s.id_opcion = $parametros->id AND s.estado_id = ".self::ESTADO_ACTIVO,'orden');

            $this->gn->set_data_loged('migapan_menu_id', $parametros->id,'generales');

            $this->gn->set_data_loged('migapan_menu', $menu['descripcion'],'generales');

            $this->gn->set_data_loged('migapan_subopcion', 'NONE','generales');

            $this->gn->set_data_loged('migapan_subopcion_texto', 'NONE','generales');

        }else{



            // $subopcion = $this->db->selectOne(array('s.descripcion','s.titulo'),'opcion s',"s.titulo = '$parametros->modulo' AND s.estado_id = ".self::ESTADO_ACTIVO,'orden');

            $subopcion = $this->db->selectOne(array('sub.descripcion AS sub_des','sub.titulo AS sub_titulo', 'opc.descripcion AS opc_des','sub.opcion_id As id'),'fw_opcion sub, fw_opcion opc',"sub.opcion_id=opc.id_opcion  AND sub.titulo = '$parametros->modulo' AND sub.estado_id = ".self::ESTADO_ACTIVO,'sub.orden');

            $this->gn->set_data_loged('migapan_menu_id', $subopcion['id'],'generales');

            $this->gn->set_data_loged('migapan_menu', $subopcion['opc_des'],'generales');

            $this->gn->set_data_loged('migapan_subopcion', $subopcion['sub_des'],'generales');

            $this->gn->set_data_loged('migapan_subopcion_texto', $subopcion['sub_titulo'],'generales');

        }

        

        echo "?opcion=".$parametros->modulo.$complemento;

    }

    function traer_miga_pan(){

        $this->tipo_contenido = 'ajax';

        //$parametros = $this->gn->traer_parametros('GET');

        

        //$subopcion = $this->db->select(array('s.descripcion','s.titulo'),'opcion s',"s.id_opcion = $parametros->id AND s.estado = 'A' ",'orden');

        $menu_id = $this->gn->get_data_loged('migapan_menu_id','generales');

        $op = $this->gn->get_data_loged('migapan_menu','generales');

        $subop_texto = $this->gn->get_data_loged('migapan_subopcion_texto','generales');

        $subop = $this->gn->get_data_loged('migapan_subopcion','generales');

        

        if($op===0){

            $op= "operaciones";

        }

        if($subop ===0){

            $subop = "NONE";

        }

        if($menu_id ===0){

            $menu_id = 2;

        }

        echo $op."|".utf8_encode($subop)."|".$menu_id."|".$subop_texto;

    }

}