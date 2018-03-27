<?php

class MiPerfil extends Maestro {

    CONST ERROR_CLAVE_NO_COINCIDE = "016";
    CONST EXITO_CAMBIO_CLAVE = "017";
    CONST TABLA_MODULO = "usuario";

    function home() {
        $this->nombre_tabla = 'fw_usuario';
        $this->primary_key = 'id_usuario';
        $this->tipo_contenido = 'ajax';
        $usuario_id_actual = $this->gn->get_data_loged('id_usuario'); //id del Interlocutor actual
        $campos_formulario = array();
        $campos_formulario['id_usuario'] = array('tipo' => 'hidden', 'valor' => $usuario_id_actual, 'complemento' => 'required');
        $campos_formulario['nombre'] = array('tipo' => 'text', 'valor' => '', 'complemento' => 'required');
        $campos_formulario['apellido'] = array('tipo' => 'text', 'valor' => '', 'complemento' => 'required');
        $campos_formulario['num_documento'] = array('tipo' => 'text', 'valor' => '', 'complemento' => 'required minlength="6"');
        $id_usuario = $this->gn->get_data_loged('id_usuario');
        $campos = array('u.nombre', 'u.apellido', 'u.num_documento');
        $tablas = 'fw_usuario u';
        $condicion = 'u.id_usuario=' . $id_usuario;
        $elemento = $this->db->selectOne($campos, $tablas, $condicion);
        if ($elemento) {
            foreach ($elemento as $key => $value) {
                $campos_formulario[$key]['valor'] = $value;
            }
        }
        $accion = "guardar_edicion";
        require_once(self::PATH_PLUGINS . "maestro/view/maestro2.html.php");
        FormHTML::formulario_dinamico($campos_formulario, $accion, $this->modulo, $this->db, $detalle = '', $campos_personalizados = '', $parametros = '', $retorno = '', $titulo = 'Mi cuenta', $form_complemento = '', $ajax = 'ajax', $area = 'area_trabajo');
    }

    function guardar_edicion($redirect = false, $parametros = null) {
        $this->nombre_tabla = 'fw_usuario';
        $this->primary_key = 'id_usuario';
        parent::guardar_edicion($redirect, $parametros);
        header('location:?opcion=subopcion&id=' . NAV_OPCION_DEFAULT);
    }

}
