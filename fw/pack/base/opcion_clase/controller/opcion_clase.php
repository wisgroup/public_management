<?php
class OpcionClase extends Modulo {

    function home() {
        $this->nc->verificar_notificacion();
        $marcablanca=$this->gn->get_data_loged('marca_blanca');
        $campos = array('up.id_usuario_perfil as codigo', 'up.nombre as Perfil', 'e.descripcion as estado');
        $tablas = "fw_usuario_perfil up, fw_estado e";
        $condicion = "e.id_estado = up.estado_id AND up.estado_id<>" . self::ESTADO_ELIMINADO." AND up.id_usuario_perfil<>1";//.' AND up.marca_blanca='.$marcablanca;

        $modulo = 'opcion_clase';
        $function = array('codigo' => 'link_id');
        $accion = array('link_id' => 'formulario_edicion');

        $this->pintar_tabla($campos, $tablas, $condicion, $modulo, $accion, $function);
    }
    function formulario_edicion() {
        $this->tipo_contenido = 'ajax';
        $parametros = $this->gn->traer_parametros('GET');
        $perfil = $parametros->id;
        $clase=$this->gn->get_data_loged('clase');
        $tipo_negocio=$this->gn->get_data_loged('tipo_negocio');
        $marcablanca=$this->gn->get_data_loged('marca_blanca');
        $opciones_principales = $this->db->select(array('id_usuario_perfil as id_opcion','nombre'),'fw_usuario_perfil','estado_id='. self::ESTADO_ACTIVO);

        //$subopciones = $this->db->selectDistinct(array('s.id_opcion', 's.descripcion','s.tipo', 's.opcion_id'), 'fw_opcion s, fw_opcion_clase oc', "oc.usuario_perfil_id=".$perfil." AND oc.tipo_negocio_id=".$tipo_negocio." AND oc.interlocutor_clase_id=".$clase." AND oc.estado_id=".self::ESTADO_ACTIVO, ' opcion_id');
        
        $subopciones=array();
        $subopciones1 = $this->db->selectDistinct(array('oc.opcion_id'), 'fw_opcion_clase oc', "oc.tipo_negocio_id=".$tipo_negocio." AND oc.estado_id=".self::ESTADO_ACTIVO, 'oc.opcion_id');
        foreach ($subopciones1 as $key => $value) {            
                $push=$this->db->selectOne(array('o.id_opcion', 'o.descripcion','o.opcion_tipo_id', 'o.opcion_id'),'fw_opcion o','o.id_opcion='.$value['opcion_id']);
                array_push($subopciones,$push);
        }
        $menu=$this->db->selectDistinct(array('o.id_opcion', 'o.descripcion','o.opcion_tipo_id', 'o.opcion_id'),'fw_opcion o','o.opcion_id=0');
        
        foreach ($menu as $llave => $valor) {
            array_push($subopciones,$valor);
        }
        $campos_permiso = $this->traer_permisos_perfil($perfil,$clase,$tipo_negocio,$marcablanca, " and estado_id = " . self::ESTADO_ACTIVO);
       
        foreach ($opciones_principales as $opcion) {
           // $opciones[$opcion['id_opcion']]['datos'] = $opcion; //OK
        }
        foreach ($subopciones as $subopcion) {
            //$opciones[$subopcion['opcion_id']]['opciones'][] = $subopcion;
            $opciones[$subopcion['opcion_id']][$subopcion['id_opcion']] = $subopcion;
        }       
        
        require_once($this->get_view_path());
        OpcionClaseHTML::home($opciones, $campos_permiso, $perfil, $opciones_principales,'opcion_clase');
    }

    function guardar_edicion() {
        $this->tipo_contenido = 'ajax';
        $parametros = $this->gn->traer_parametros('POST');
        $perfil = $parametros->clase;

        $tipo_negocio=$this->gn->get_data_loged('tipo_negocio');
        $clase=$this->gn->get_data_loged('clase');
        $marcablanca=$this->gn->get_data_loged('marca_blanca');
        $permisos_anterirores = $this->traer_permisos_perfil($perfil,$clase,$tipo_negocio,$marcablanca);
        
        unset($parametros->clase);
        $resultado = true;

        if (!empty($parametros)) {
            $this->db->update(array('estado_id' => '2'), 'fw_opcion_clase', "usuario_perfil_id = $perfil AND interlocutor_clase_id=".$clase.' AND marca_blanca='.$marcablanca);

            foreach ($parametros as $key => $value) {

                if (in_array($value, $permisos_anterirores)) {
                    $resultado = $this->db->update(array('estado_id' => '1'), 'fw_opcion_clase', "usuario_perfil_id = $perfil and opcion_id=" . $value);
                } else {
                    $resultado = $this->db->insert(array('usuario_perfil_id' => $perfil, 'interlocutor_clase_id' => $clase, 'opcion_id' => $value,'tipo_negocio_id'=>$tipo_negocio,'marca_blanca'=>$marcablanca,'estado_id' => '1'), 'fw_opcion_clase');
                }
            }
        }
        if (!$resultado) {
            $this->nc->set_notificacion('Error al Editar Permisos');
        } else {
            $this->nc->set_notificacion('Permisos Editados Correctamente');
        }
        header('location:?opcion=' . $this->modulo);
    }

    function traer_permisos_perfil($perfil,$clase,$tipo_negocio,$marcablanca,$condicion = '') {
		$condicion_clase = "";
		if($clase > 1){
			$condicion_clase =  " AND interlocutor_clase_id=".$clase." ";
		}
        $subopciones_permiso = $this->db->select(
				array('opcion_id'), 
				'fw_opcion_clase',
				"usuario_perfil_id  = '" . $perfil . "'  " 
				. $condicion_clase
				. " AND marca_blanca=".$marcablanca." "
				. " AND tipo_negocio_id=".$tipo_negocio .$condicion
			);
        $campos_permiso = array();

        if (!empty($subopciones_permiso)) {
            foreach ($subopciones_permiso as $key => $value) {
                $campos_permiso[] = $value['opcion_id'];
            }
        }
        return $campos_permiso;
    }
}