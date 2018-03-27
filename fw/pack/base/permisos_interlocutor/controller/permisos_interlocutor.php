<?php
class PermisosInterlocutor extends Modulo {

    function home() {

        $this->nc->verificar_notificacion();

        $campos = array('it.id_interlocutor_tipo_negocio as codigo', 'it.nombre as Negocio', 'e.descripcion as estado');
        $tablas = "fw_interlocutor_tipo_negocio it, fw_estado e";
        $condicion = "e.id_estado = it.estado_id AND it.estado_id<>" . ESTADO_ELIMINADO;

        $modulo = 'permisos_interlocutor';
        $function = array('codigo' => 'link_id');
        $accion = array('link_id' => 'formulario_edicion');

        $this->pintar_tabla($campos, $tablas, $condicion, $modulo, $accion, $function);
    }
    function formulario_edicion() {
        $this->tipo_contenido = 'ajax';
        $parametros = $this->gn->traer_parametros('GET');
        $tipo_negocio = $parametros->id;
		$clase = CLASE_WIS_CLIENTE;
		if($tipo_negocio == TIPO_NEGOCIO_WIS){
			$clase = CLASE_WIS_OWNER;
		}
        $opciones_principales = $this->db->select(array('s.id_opcion', 's.descripcion', 's.opcion_tipo_id','ot.descripcion AS tipo','s.opcion_id'),
				'fw_opcion s, fw_opcion_tipo ot',
				" s.opcion_id =" . OPCION_PRINCIPAL . " "
				. " AND s.opcion_tipo_id = ot.id_opcion_tipo "
				. " AND  s.estado_id = " . ESTADO_ACTIVO, 'orden');

        $subopciones = $this->db->select(array('s.id_opcion', 's.descripcion','s.opcion_tipo_id','ot.descripcion AS tipo', 's.opcion_id'),
				'fw_opcion s, fw_opcion_tipo ot',
				" s.opcion_tipo_id = ot.id_opcion_tipo "
				. " AND s.estado_id = " . ESTADO_ACTIVO, ' opcion_id');

        $campos_permiso = $this->traer_permisos_clase($clase, $tipo_negocio, " and estado_id = " . ESTADO_ACTIVO);
        foreach ($opciones_principales as $opcion) {
           // $opciones[$opcion['id_opcion']]['datos'] = $opcion; //OK
        }
        foreach ($subopciones as $subopcion) {
            //$opciones[$subopcion['opcion_id']]['opciones'][] = $subopcion;
            $opciones[$subopcion['opcion_id']][$subopcion['id_opcion']] = $subopcion;
        }        
        
        $tipo=$this->db->select('nombre','fw_interlocutor_tipo_negocio','id_interlocutor_tipo_negocio='.$tipo_negocio);
        require_once ("fw/pack/base/opcion_clase/view/opcion_clase.html.php");
        OpcionClaseHTML::home($opciones, $campos_permiso, $tipo_negocio, $opciones_principales,'permisos_interlocutor',$tipo[0]['nombre']);
    }

    function guardar_edicion() {
        $this->tipo_contenido = 'ajax';
        $parametros = $this->gn->traer_parametros('POST');
        $tipo_negocio = $parametros->tipo_negocio;
        $permisos_anterirores = $this->traer_permisos_clase($tipo_negocio);
		$clase = CLASE_WIS_CLIENTE;
		if($tipo_negocio == TIPO_NEGOCIO_WIS){
			$clase = CLASE_WIS_OWNER;
		}
        unset($parametros->clase);
        $resultado = true;

        if (!empty($parametros)) {

            $this->db->update(array('estado_id' => ESTADO_BLOQUEADO), 'fw_opcion_clase', "tipo_negocio_id = $tipo_negocio");

            foreach ($parametros as $key => $value) {
                if (in_array($value, $permisos_anterirores)) {
                    $resultado = $this->db->update(array('estado_id' => ESTADO_ACTIVO), 'fw_opcion_clase', "tipo_negocio_id = $tipo_negocio and opcion_id=" . $value);
                } else {
                    $resultado = $this->db->insert(array('usuario_perfil_id' => '1', 'interlocutor_clase_id' => $clase, 'opcion_id' => $value, 'tipo_negocio_id'=>$tipo_negocio,'estado_id' => ESTADO_ACTIVO), 'fw_opcion_clase');
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

    function traer_permisos_clase($clase, $tipo_negocio, $condicion = '') {
        $subopciones_permiso = $this->db->select(array('opcion_id'), 'fw_opcion_clase', " interlocutor_clase_id = ".$clase." AND tipo_negocio_id  = '" . $tipo_negocio . "' " . $condicion);
        $campos_permiso = array();

        if (!empty($subopciones_permiso)) {
            foreach ($subopciones_permiso as $key => $value) {
                $campos_permiso[] = $value['opcion_id'];
            }
        }

        return $campos_permiso;
    }
}