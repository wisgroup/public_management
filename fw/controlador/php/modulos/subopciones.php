<?php

require_once('fw/controlador/php/modulos/maestro.class.php');

class SubOpciones extends Maestro {

    CONST OPCION_PRINCIPAL = 0;

    public function __construct($db = "", $modulo = "") {

        $this->modulo = 'subopciones';
        $this->nombre_tabla = "fw_opcion";

        $this->encabezado_edicion = array(array('descripcion'), 'opcion', 'id_opcion=%s');

        parent::__construct($db, $this->modulo);

        $id_interlocutor = $this->gn->get_data_loged('id_interlocutor');
        $id_usuario = $this->gn->get_data_loged('id_usuario');


        $campos_formulario['id_' . $this->nombre_tabla] = array('tipo' => 'hidden', 'valor' => '', 'complemento' => 'required');
        $campos_formulario[$this->nombre_tabla . '_id'] = array('tipo' => 'select', 'valor' => '', 'complemento' => 'required', 'campo' => 'descripcion', 'condicion' => 'opcion_id=' . self::OPCION_PRINCIPAL . ' AND estado_id <> ' . self::ESTADO_ELIMINADO);
        $campos_formulario['descripcion'] = array('tipo' => 'text', 'valor' => '', 'complemento' => 'required');
        $campos_formulario['titulo'] = array('tipo' => 'text', 'valor' => '', 'complemento' => 'required', 'label' => 'Modulo');
        $campos_formulario['orden'] = array('tipo' => 'number', 'valor' => '', 'complemento' => 'required maxlength="3"');
        $campos_formulario['imagen'] = array('tipo' => 'file', 'valor' => '', 'complemento' => ' onblur="validar_file(this,' . "'" . '512000' . "'" . ')" ' . 'onChange="validar_file(this,' . "'" . '512000' . "'" . ')"');
        $campos_formulario['estado_id'] = array('tipo' => 'select', 'valor' => '', 'complemento' => 'required', 'campo' => 'descripcion', 'condicion' => 'id_estado IN (1,2)');

        $this->campos_formulario = $campos_formulario;
    }

    function home() {

        $id_interlocutor = $this->gn->get_data_loged('id_interlocutor');
        $id_usuario = $this->gn->get_data_loged('id_usuario');

        $campos = array('o.id_' . $this->nombre_tabla . ' as codigo', "o.descripcion", "o.imagen", "o.orden", "o.titulo", "e.descripcion as estado");
        $tablas = $this->nombre_tabla . ' o , estado e';
        $condicion = " o.estado_id = e.id_estado AND  o.estado_id <> '" . self::ESTADO_ELIMINADO . "' AND o.opcion_id<>" . self::OPCION_PRINCIPAL;

        $this->iniciar_maestro($campos, $tablas, $condicion, 'Opciones ' . date('Y-m-d H.i.s'));
    }

    function guardar_creacion() {

        $this->tipo_contenido = '';
        $parametros = $this->gn->traer_parametros('POST');


        if ($_FILES['imagen']['error'] != 4) {

            $carga_archivo = $this->gn->cargar_archivo('vista/media/img/opcion/big/', 'imagen', true, 512000);

            if (!$carga_archivo->respuesta) {

                $this->nc->set_notificacion($carga_archivo->descripcion);
                header('location:?opcion=' . $this->modulo);
                exit();
            }

            $parametros->imagen = $carga_archivo->descripcion;
        }

        $guardar_creacion = parent::guardar_creacion(false, $parametros);

        $ext = explode('.', $_FILES['imagen']['name']);
        $ext = array_pop($ext);
        $nombre_archivo = $guardar_creacion . "." . $ext;

        $resultado = $this->db->update(array('imagen' => $nombre_archivo), $this->nombre_tabla, 'id_opcion = ' . $guardar_creacion);

        rename("vista/media/img/opcion/big/" . $parametros->imagen, "vista/media/img/opcion/big/" . $nombre_archivo);

        header('location:?opcion=' . $this->modulo);
    }

    function guardar_edicion() {

        $this->tipo_contenido = '';
        $parametros = $this->gn->traer_parametros('POST');

        $id_tabla = 'id_' . $this->nombre_tabla;
        $id = $parametros->$id_tabla;


        if ($_FILES['imagen']['error'] != 4) {

            $ext = explode('.', $_FILES['imagen']['name']);
            $ext = array_pop($ext);

            $carga_archivo = $this->gn->cargar_archivo('vista/media/img/opcion/big/', 'imagen', true, 512000, '', $id);

            if (!$carga_archivo->respuesta) {

                $this->nc->set_notificacion($carga_archivo->descripcion);
                header('location:?opcion=' . $this->modulo);
                exit();
            }

            // $nombre_archivo = $guardar_creacion . "." . $ext;
            $parametros->imagen = $carga_archivo->descripcion;
        }

        $respuesta_edicion = $this->editar_elemento($parametros, $id, $this->nombre_tabla);


        if (!$respuesta_edicion) {
            $mensaje = $this->nc->traer_mensaje_respuesta($this->ERROR_EDICION);
            $this->nc->set_notificacion(utf8_encode($mensaje['descripcion']));
        } else {
            $mensaje = $this->nc->traer_mensaje_respuesta($this->EXITO_EDICION);
            $this->nc->set_notificacion(utf8_encode($mensaje['descripcion']));
        }

        header('location:?opcion=' . $this->modulo);
    }

}
