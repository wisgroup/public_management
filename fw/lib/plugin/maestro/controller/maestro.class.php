<?php

require_once(PATH_PLUGIN_MODULO);

class Maestro extends Modulo {

    public $tipo_contenido = 'html';
    public $detalle = '';
    public $EXITO_EDICION = "013";
    public $EXITO_CREACION = "014";
    public $EXITO_ELIMINAR = "015";
    public $ERROR_EDICION = "010";
    public $ERROR_CREACION = "011";
    public $ERROR_ELIMINAR = "012";
    public $modulo = "";
    public $nombre_tabla = "";
    public $primary_key = "";
    public $herramientas = array('nuevo', 'editar', 'borrar');
    public $mensaje = "";
    public $encabezado_creacion = "CREACI&Oacute;N";
    public $encabezado_edicion = "EDICI&Oacute;N";
    public $actualizacion_ajax = 'ajax';
    public $area_actualizacion = 'area_trabajo';

    function __construct($db = "", $modulo = "", $ruta_cron = "", $modulo_info = null, $WISQueries = null, $appQry = null) {
        $this->modulo = $modulo;
        if (!isset($this->nombre_tabla)){
            $this->nombre_tabla = $modulo;
        }
        $this->primary_key = 'id_' . $modulo;
        $this->actualizacion_ajax = 'ajax';
        parent::__construct($db, $this->modulo, $ruta_cron, $modulo_info, $WISQueries, $appQry);
    }

    function iniciar_maestro($campos, $tablas, $condicion, $titulo_pdf = '', $checkbox = array(), $modulo = '', $tabla = '') {
        $function = array();
        $this->nc->verificar_notificacion();

        $parametros = $this->gn->traer_parametros('GET');
        if (isset($parametros->type)) {
            $this->tipo_contenido = $parametros->type;
        }

        $this->pintar_herramientas($this->modulo, $this->herramientas);

        if (in_array("editar", $this->herramientas) or in_array("eliminar", $this->herramientas)) {

            $primer_campo = $campos[0];
            $primer_campo = explode(" ", $primer_campo);        //Verificando si es necesario
            $codigo = $primer_campo[0];             //colocar el boton para la edicion
            //para la edicion de los registros
            $selected = $codigo . ' as empty';
            array_unshift($campos, $selected);
            $function = array('empty' => 'radio_edicion', 'Principal' => 'checkbox');
        }

        $accion = array('radio_edicion' => 'formulario_edicion');
        $this->pintar_tabla($campos, $tablas, $condicion, $modulo, $accion, $function, $titulo_pdf, 'tabla_' . $this->modulo, "select", $checkbox, $tabla);
    }

    function iniciar_maestro_funcion($campos, $tablas, $condicion, $titulo_pdf = '', $accion, $function) {

        $this->nc->verificar_notificacion();
        $parametros = $this->gn->traer_parametros('GET');

        if (isset($parametros->type)) {
            $this->tipo_contenido = $parametros->type;
        }

        if (in_array("editar", $this->herramientas) or in_array("borrar", $this->herramientas)) {

            $primer_campo = $campos[0];
            $primer_campo = explode(" ", $primer_campo);        //Verificando si es necesario
            $codigo = $primer_campo[0];             //colocar el boton para la edicion

            $selected = $codigo . ' as empty';
            array_unshift($campos, $selected);

            $function['empty'] = 'radio_edicion';
        }

        $accion['radio_edicion'] = 'formulario_edicion';
        $this->pintar_tabla($campos, $tablas, $condicion, $this->modulo, $accion, $function, $titulo_pdf, 'tabla_' . $this->modulo);
    }

    function iniciar_maestro_master($campos, $tablas, $condicion, $titulo_pdf = '') {

        $this->nc->verificar_notificacion();

        $parametros = $this->gn->traer_parametros('GET');

        if (isset($parametros->type)) {
            $this->tipo_contenido = $parametros->type;
        }

        $this->pintar_herramientas($this->modulo, $this->herramientas);

        if (in_array("editar", $this->herramientas) or in_array("eliminar", $this->herramientas)) {

            $primer_campo = $campos[0];
            $primer_campo = explode(" ", $primer_campo);        //Verificando si es necesario
            $codigo = $primer_campo[0];             //colocar el boton para la edicion
            //para la edicion de los registros
            $selected = $codigo . ' as empty';
            array_unshift($campos, $selected);
            $function = array('empty' => 'radio_edicion_maestro');
        }

        $accion = array('radio_edicion_maestro' => 'ver_detalle_' . $this->modulo);
        $this->pintar_tabla($campos, $tablas, $condicion, $this->modulo, $accion, $function, $titulo_pdf, 'tabla_' . $this->modulo);

        require_once(self::PATH_PLUGINS . "table/view/datatable.html.php");
        DatatableHTML::detalle_maestro();
    }
 /* 2016/07/14: Método que reemplaza a iniciar_maestro_master */

    function iniciar_maestro_detalle($campos, $tablas, $condicion, $titulo_pdf = '') {

        $this->nc->verificar_notificacion();
        $parametros = $this->gn->traer_parametros('GET');

        if (isset($parametros->type)) {
            $this->tipo_contenido = $parametros->type;
        }

        $this->pintar_herramientas($this->modulo, $this->herramientas, "validar_herramienta", $this->nombre_plantilla_csv);

        if (in_array("editar", $this->herramientas) or in_array("borrar", $this->herramientas)) {
            $primer_campo = $campos[0];
            $primer_campo = explode(" ", $primer_campo);        //Verificando si es necesario
            if ($primer_campo[2] != "empty_no_check") {
                $codigo = $primer_campo[0];             //colocar el boton para la edicion
                //para la edicion de los registros
                $selected = $codigo . ' as empty';
                array_unshift($campos, $selected);
                $function = array('empty' => 'radio_edicion_maestro');
            } else {
                $function = array('empty_no_check' => 'radio_edicion_maestro');
            }
        }
        $accion = array('radio_edicion_maestro' => 'ver_detalle');
        if (in_array("cerrar", $this->herramientas)) {

            $primer_campo = $campos[0];
            $primer_campo = explode(" ", $primer_campo);        //Verificando si es necesario
            $codigo = $primer_campo[0];             //colocar el boton para la edicion
            //para la edicion de los registros
            $selected = $codigo . ' as empty';
            array_unshift($campos, $selected);
            $function = array('empty' => 'radio_edicion_maestro_datos');
            $accion = array('radio_edicion_maestro_datos' => 'ver_detalle');
        }

        $this->pintar_tabla($campos, $tablas, $condicion, $this->modulo, $accion, $function, $titulo_pdf, 'tabla_' . $this->modulo);

        require_once(PATH_PLUGIN_TABLE_VIEW);
        DatatableHTML::detalle_maestro();
    }
    function iniciar_maestro_slave($campos, $tablas, $condicion, $titulo_pdf = '', $elementos = array(), $event = 'formulario_edicion', $padre = 0) {
        $function = array();
        $this->nc->verificar_notificacion();
        $parametros = $this->gn->traer_parametros('GET');

        if (isset($parametros->type)) {
            $this->tipo_contenido = $parametros->type;
        }

        $this->pintar_herramientas($this->modulo, $this->herramientas, 'validar_herramienta_slave', $this->nombre_plantilla_csv);

        if (in_array("editar", $this->herramientas) or in_array("borrar", $this->herramientas) or in_array("csv_importar", $this->herramientas) or in_array("subir_csv", $this->herramientas)) {
            $primer_campo = $campos[0];
            $primer_campo = explode(" ", $primer_campo);        //Verificando si es necesario
            if ($primer_campo[2] != "empty_no_check") {
                $codigo = $primer_campo[0];             //colocar el boton para la edicion
                //para la edicion de los registros
                $selected = $codigo . ' as empty';
                array_unshift($campos, $selected);
                $function = array('empty' => 'radio_edicion_slave');
            } else {
                $function = array('empty_no_check' => 'radio_edicion_slave');
            }
        }
        //$elementos llenarlo con los elementos del detalle que esten almacenados
        $elementos['radio_edicion_slave'] = $event;
        if ($padre > 0) {
            $elementos[$event] = $padre;
        }
        $accion = $elementos;
        $this->pintar_tabla($campos, $tablas, $condicion, $this->modulo, $accion, $function, $titulo_pdf, 'tabla_' . $this->modulo, "", '', $padre);
    }

    function iniciar_maestro_directo($campos, $tablas, $modulo = '') {
        $this->nc->verificar_notificacion();
        $parametros = $this->gn->traer_parametros('GET');
        if (isset($parametros->type)) {
            $this->tipo_contenido = $parametros->type;
        }

        //$accion = array('radio_edicion' => 'formulario_edicion');
        if ($this->modulo == 'transacciones') {
            $this->pintar_transacciones($this->modulo, $campos, $tablas, $this->modulo);
        } elseif ($modulo == 'inventariofisico') {
            $this->pintar_inventario($this->modulo, $campos, $tablas, $this->modulo);
        } elseif ($modulo == 'mesas_visual') {
            $this->pintar_mesas($campos, $tablas, $this->modulo);
        } elseif ($modulo == 'facturar') {
            $this->pintar_mesas($campos, $tablas, $this->modulo);
        }
    }

    function formulario_edicion($campos_formulario = null) {
        if ($campos_formulario === null) {
            $campos_formulario = $this->campos_formulario;
        }

        $campos = array();
        $campos_personalizados = array();

        foreach ($campos_formulario as $key => $input) {      //Seteando el array con le nombre de los campos
            if (!in_array('personalizado', $input)) {
                array_push($campos, $key);
            }
        }
        $this->tipo_contenido = '';
        $accion = "guardar_creacion&type=html";
        $parametros = $this->gn->traer_parametros('GET');
        $encabezado = $this->encabezado_creacion;
        if (isset($parametros->type)) {
            $this->tipo_contenido = $parametros->type;
        }

        if (isset($parametros->id) && (int) $parametros->id > 0) {
            $accion = "guardar_edicion";

            if (is_array($this->encabezado_edicion)) {

                $p = $this->db->selectone($this->encabezado_edicion[0], $this->encabezado_edicion[1], sprintf($this->encabezado_edicion[2], $parametros->id));
                $encabezado = '';
                foreach ($p as $key => $value) {
                    $encabezado = $encabezado . $value . " ";
                }
            } else {
                $encabezado = $this->encabezado_edicion;
            }

            $elemento = $this->db->selectone($campos, $this->nombre_tabla, $parametros->id . ' = ' . $this->primary_key);

            if (is_array($elemento)) {

                foreach ($elemento as $key => $value) {

                    $campos_formulario[$key]['valor'] = $value;
                }
            }
        }

        require_once(self::PATH_PLUGINS . "maestro/view/maestro2.html.php");
        FormHTML::formulario_dinamico($campos_formulario, $accion, $this->modulo, $this->db, $this->detalle, $campos_personalizados, $parametros, $retorno = '', $encabezado, '', $this->actualizacion_ajax, $this->area_actualizacion);
    }

    function guardar_edicion($redirect = true, $parametros = null) {

        $this->tipo_contenido = 'html';

        if (is_null($parametros)) {
            $parametros = $this->gn->traer_parametros('POST');
        }

        $param_get = $this->gn->traer_parametros('GET');

        $id_tabla = $this->primary_key;
        $id = $parametros->$id_tabla;
        $respuesta_edicion = $this->editar_elemento($parametros, $id, $this->nombre_tabla);

        if (isset($param_get->type)) {
            $this->tipo_contenido = $param_get->type;
        }

        if (!$respuesta_edicion) {
            $mensaje = $this->nc->traer_mensaje_respuesta($this->ERROR_EDICION);
            $this->nc->set_notificacion(utf8_encode($mensaje['descripcion']));
        } else {
            $mensaje = $this->nc->traer_mensaje_respuesta($this->EXITO_EDICION);
            $this->nc->set_notificacion(utf8_encode($mensaje['descripcion']));
        }

        if (isset($param_get->type) && $param_get->type === 'ajax') {
            echo 'OK';
        } else if (!$redirect) {
            return $respuesta_edicion;
        } else {
            if ($this->actualizacion_ajax == 'ajax') {
                $this->home();
            } else {
                header('location:?opcion=' . $this->modulo);
            }
        }
    }

    function guardar_creacion($redirect = true, $parametros = null, $last_id = 0) {
        $this->tipo_contenido = 'ajax';
        if (is_null($parametros)) {
            $parametros = $this->gn->traer_parametros('POST');
        }
        $params_get = $this->gn->traer_parametros('GET');
        $id_tabla = 'id_' . $this->nombre_tabla;
        unset($parametros->$id_tabla);
        $campos = array();

        foreach ($parametros as $key => $value) {
            $campos[$key] = $value;
        }
        $log = $this->registrar_log(self::LOG_CREAR, $this->nombre_tabla, $campos);
        $respuesta_edicion = $this->db->insert($campos, $this->nombre_tabla);

        if (!$respuesta_edicion) {
            $mensaje = $this->nc->traer_mensaje_respuesta($this->ERROR_CREACION);
            $this->nc->set_notificacion(utf8_encode($mensaje['descripcion']));
        } else {
            $last_id = $this->db->insertId();
            $mensaje = $this->nc->traer_mensaje_respuesta($this->EXITO_CREACION);
            $this->nc->set_notificacion(utf8_encode($mensaje['descripcion']));
        }

        if (!$redirect) {
            if ($last_id > 0) {
                return $last_id;
            } else {
                header('location:?opcion=' . $this->modulo . "&type" . $this->tipo_contenido);
            }
            $retorno = $this->gn->get_data_loged('retorno');

            if (isset($retorno) && $retorno != '') {
                $this->gn->set_data_loged('retorno', '');
                $campo = $this->gn->get_data_loged('campo');
                $this->gn->set_data_loged('campo', '');
                $this->gn->set_data_loged($campo, $last_id);

                header('location:?opcion=' . $retorno . "&type=html");
            } else {

                if ($this->actualizacion_ajax == 'ajax') {
                    $this->home();
                } else {
                    header('location:?opcion=' . $this->modulo . "&type=html");
                }
            }
        } else {
            header('location:?opcion=' . $this->modulo . "&type=html");
        }
    }

    function cambiar_estado($redirect = true, $parametros = null) {

        $this->tipo_contenido = 'ajax';

        if (is_null($parametros)) {
            $parametros = $this->gn->traer_parametros('GET');
        }

        $respuesta_edicion = $this->cambiar_estado_elementos_multiples(self::ESTADO_ELIMINADO, $parametros->id, $this->nombre_tabla);

        if (!$respuesta_edicion) {
            $mensaje = $this->nc->traer_mensaje_respuesta($this->ERROR_ELIMINAR);
            $this->nc->set_notificacion($mensaje['descripcion']);
        } else {
            $mensaje = $this->nc->traer_mensaje_respuesta($this->EXITO_ELIMINAR);
            $this->nc->set_notificacion($mensaje['descripcion']);
        }

        $this->home();
    }

    function editar_elemento($parametros, $id, $tabla) {

        $campos = array();

        foreach ($parametros as $key => $value) {
            $campos[$key] = $value;
        }

        $condicion = $this->primary_key . " = '$id'";
        $resultado = $this->db->update($campos, $tabla, $condicion);

        $log = $this->registrar_log(self::LOG_EDTAR, $tabla, $campos, $condicion);

        return $resultado;
    }

    function editar_elemento_condicion($parametros, $condicion, $tabla) {
        $campos = array();
        foreach ($parametros as $key => $value) {
            $campos[$key] = $value;
        }

        $resultado = $this->db->update($campos, $tabla, $condicion);
        $log = $this->registrar_log(self::LOG_EDTAR, $tabla, $campos, $condicion);

        return $resultado;
    }

    function cambiar_estado_elemento($estado, $id, $tabla) {

        $condicion = $this->primary_key . " = '$id'";
        $log = $this->registrar_log(self::LOG_BORRAR, $tabla, array('estado_id' => $estado), $condicion);
        $resultado = $this->db->update(array('estado_id' => $estado), $tabla, $condicion);

        return $resultado;
    }

    function cambiar_estado_elementos_multiples($estado, $condicion, $tabla) {

        $condicion = str_replace("campo", $this->primary_key, $condicion);
        $log = $this->registrar_log(self::LOG_BORRAR, $tabla, array('estado_id' => $estado), $condicion);
        $resultado = $this->db->update(array('estado_id' => $estado), $tabla, $condicion);

        return $resultado;
    }

    function crear_elemento($parametros, $tabla) {
        $campos = array();

        foreach ($parametros as $key => $value) {
            $campos[$key] = $value;
        }

        $log = $this->registrar_log(self::LOG_CREAR, $tabla, $campos);
        $resultado = $this->db->insert($campos, $tabla);
        return $resultado;
    }

    function crear_elemento_id($parametros, $tabla) {
        $campos = array();

        foreach ($parametros as $key => $value) {
            $campos[$key] = $value;
        }

        $log = $this->registrar_log(self::LOG_CREAR, $tabla, $campos);

        $resultado = $this->db->insert($campos, $tabla);
        return $this->db->insertId();

        /* return $resultado; */
    }

    function cambiar_estado_master() {
        $parametros = $this->gn->traer_parametros('GET');
        $estado = $parametros->valor;
        $id = $parametros->id;
        $respuesta_update = $this->db->update(array('estado_id' => $estado), $this->nombre_tabla, 'id_' . $this->nombre_tabla . '=' . $id);
        header('location:?opcion=' . $this->modulo . '&type=ajax');
    }

}
