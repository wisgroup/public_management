<?php

class Modulo {

    public $tipo_contenido = "html";
    public $base;
    public $tipo;
    public $paquete;
    protected $modulo = "";

    /* @var InfoModulo */
    protected $modulo_info = "";
    public $path;

    /* @var WISMysqli */
    protected $db;
    /* @var WISFWQueries */
    protected $WISQueries;
    /* @var AppControlQueries */
    protected $appQry;
    protected $sx;
    protected $iu;
    /* @var Generales */
    protected $gn;
    protected $tx;
    protected $nc;
    protected $grid;
    protected $va;
    protected $prefijo = 'fw_';

    /* WIS ID MARCABLANCA */

    CONST WIS_OWNER = 1;
    CONST EXITO_CAMBIO_ESTADO = "058";
    CONST ERROR_CAMBIO_ESTADO = "059";
    CONST ERROR_EDICION = "010";
    CONST ERROR_CREACION = "011";
    CONST ERROR_ELIMINAR = "012";
    CONST EXITO_EDICION = "013";
    CONST EXITO_CREACION = "014";
    CONST EXITO_ELIMINAR = "015";
    CONST IGUAL = " = ";
    CONST LIKE = " LIKE ";

    /* TIPOS LOG */
    CONST LOG_INGRESO = 1;
    CONST LOG_INGRESO_FALLIDO = 2;
    CONST LOG_CREAR = 3;
    CONST LOG_EDTAR = 4;
    CONST LOG_BORRAR = 5;

    /* CLASES */
    //CONST CLASE_ADMINISTRADOR = 1;
    //CONST CLASE_INSTITUCION = 2;
    //CONST CLASE_PROFESOR = 4;
    //CONST CLASE_ESTUDIANTE = 5;

    /* PERFILES */

    CONST PERFIL_PRINCIPAL = 1;
    CONST PERFIL_CLIENTE = 3;
    /* ESTADOS */
    CONST ESTADO_ACTIVO = 1;
    CONST ESTADO_BLOQUEADO = 2;
    CONST ESTADO_ELIMINADO = 3;
    CONST OPCION_PRINCIPAL = 0;
    CONST TYPE_HTML = 'html';
    CONST TYPE_AJAX = 'ajax';
    CONST TYPE_AJAX_PART = 'ajax_part';
    CONST CONTENT_TYPE_HTML = 'html';
    CONST CONTENT_TYPE_AJAX = 'ajax';
    CONST CONTENT_TYPE_AJAX_PART = 'ajax_part';
    CONST CONTENT_TYPE_WS = 'ws';
    CONST CONTENT_TYPE_REPORT = 'reporte';
    CONST PATH_VIEW = "view/";
    CONST PATH_CONTROLLER = "controller/";
    CONST PATH_PLUGINS = "fw/lib/plugin/";
    CONST PATH_CLASSES = "fw/lib/class/";

    protected $ruta_cron = '';

    function __construct($db = "", $modulo = "", $ruta_cron = "", $modulo_info = null, $WISQueries = null, $appQry = null) {
        $this->modulo_info = $modulo_info;
        if (isset($this->modulo_info->base)) {
            $this->base = $this->modulo_info->base;
        }
        $this->WISQueries = $WISQueries;
        $this->appQry = $appQry;
        if ($db != "") {
            $this->inicializar_libreria($db, $modulo, $ruta_cron);
        }
        $parametros = $this->gn->traer_parametros('GET');
        if (isset($parametros->type)) {
            $this->tipo_contenido = $parametros->type;
        }
        if ((in_array($this->tipo_contenido, array('html', 'ajax')))) {
            $this->borrar_timeout($modulo);
            $this->pintar_titulo($modulo);
        }
    }

    function inicializar_libreria($db, $modulo, $ruta_cron) {
        $this->db = $db;
        $this->modulo = $modulo;
        $this->ruta_cron = $ruta_cron;

        require_once($this->ruta_cron . self::PATH_CLASSES . 'seguridad.class.php');
        $this->sx = new Seguridad($db, $this->ruta_cron);

        require_once $this->ruta_cron . self::PATH_CLASSES . 'informacion_usuario.class.php';
        $this->iu = new InformacionUsuario($db, $this->ruta_cron);

        require_once $this->ruta_cron . self::PATH_CLASSES . 'generales.class.php';
        $this->gn = new Generales($db, $this->ruta_cron);

        require_once $this->ruta_cron . self::PATH_CLASSES . 'notificacion.class.php';
        $this->nc = new Notificacion($db, $this->gn, $this->ruta_cron);

        require_once $this->ruta_cron . self::PATH_CLASSES . 'validacion.class.php';
        $this->va = new Validacion($db, $this->gn, $this->ruta_cron);

        if ($this->ruta_cron == '') {
            require_once($this->ruta_cron . self::PATH_PLUGINS . '_inc/grid/grid.class.php');
            $this->grid = New Grid();
        }
    }

    function traer_accion($accion) {
        try {
            eval('$this->' . $accion . '();');
        } catch (Exception $exc) {
            echo "Funcionalidad no v&aacute;lida";
        }
    }

    function pintar_herramientas($modulo, $herramientas = array(
        'eliminar',
        'editar',
        'nuevo'), $function = 'validar_herramienta', $nombre_plantilla = "") {
        require_once(PATH_PLUGIN_MAESTRO_VIEW2);
        FormHTML::pintar_herramientas($modulo, $herramientas, $function);
    }

    function nombre_modulo($path) {
        /* LINEA PARA LINUX */
        //$path = explode('/', $path);
        /* LINEA PARA WINDOWS */
        $path = explode('\\', $path);
        $modulo = substr($path[count($path) - 1], 0, -4);
        return $modulo;
    }

    function descargar() {
        $this->tipo_contenido = "ajax";
        $params = $this->gn->traer_parametros('GET');
        if (isset($params->file)) {
            header("Content-Disposition: attachment; filename=" . $params->file . ";");
            //header ("Content-Type: application/force-download"); 
            header("Content-Type: application/octet-stream");
            header("Content-Length: " . filesize('media/recursos/' . $params->file));
            readfile('media/recursos/' . $params->file);
        }
    }

    function traer_owner_id($interlocutor = '') {
        if ($interlocutor == '') {
            $interlocutor = $this->gn->get_data_loged('id_interlocutor');
        }
        $interlocutor_id = $this->db->selectOne(array('interlocutor_id'), 'interlocutor', 'id_interlocutor = "' . $interlocutor . '"');
        return $interlocutor_id['interlocutor_id'];
    }
    
    function pintar_tabla($campos, $tabla, $condicion, $modulo = '', $accion = '', $function = array(), $titulo_pdf = '', $id_tabla = 'example', $select = "select", $boton_descarga = '', $padre = 0) {
    //function pintar_tabla($campos, $tabla, $condicion, $modulo = '', $accion = '', $function = array(), $titulo_pdf = '', $id_tabla = 'example', $select = "select", $checkbox = array(), $nombre_tabla = '') {
        switch ($select) {
            case 'select':
                $contenido = $this->db->select($campos, $tabla, $condicion, false, false, false);
                break;
            case 'selectDistinct':
                $contenido = $this->db->selectDistinct($campos, $tabla, $condicion, false, false, false);
                break;
            default:
                $contenido = $this->db->select($campos, $tabla, $condicion, false, false, false);
                break;
        }
        $filas = $this->db->countRows();
        if (intval($filas) > 5000) {
            $this->gn->set_data_loged('campos_csv', $campos);
            $this->gn->set_data_loged('tabla_csv', $tabla);
            $this->gn->set_data_loged('condicion_csv', $condicion);
            require_once PATH_PLUGIN_TABLE_VIEW;
            DatatableHTML::btn_descargar();
        } else {
            if (!empty($contenido)) {
                foreach ($contenido[0] as $key => $value) {
                    $encabezado[] = $key;  //Obtengo los campos de la consulta
                }
            } else {
                $contenido = '';
                $encabezado = '';
            }
            require_once (PATH_PLUGIN_TABLE_VIEW);
            DatatableHTML::home($encabezado, $contenido, $modulo, $accion, $function, '', $titulo_pdf, $id_tabla, $boton_descarga, $padre);
            //DatatableHTML::home($encabezado, $contenido, $modulo, $accion, $function, '', $titulo_pdf, $id_tabla, $checkbox, $nombre_tabla);
        }
    }

    function traer_detalle() {
        $respuesta = "&";
        $parametros = $this->gn->traer_parametros('GET');
        $id_interlocutor = $this->gn->get_data_loged('id_interlocutor');
        if ($parametros->condicion == 'IGUAL') {
            $operador = '=';
            $condicion = " $parametros->campo " . $operador . " '" . $parametros->valor . "' ";
        } else {
            $operador = $parametros->condicion;
            $condicion = " $parametros->campo " . $operador . " '%" . $parametros->valor . "%' ";
        }
        $condicion .= " AND interlocutor_id = '" . $id_interlocutor . "'";
        $tabla = 'producto p ';
        $contenido = $this->db->selectOne(array('id_producto', 'nombre', 'descripcion', 'cantidad'), $tabla, $condicion, false, false, true);
        foreach ($contenido as $key => $value) {
            $respuesta .= $key . "=" . $value . "&";
        }
        $this->tipo_contenido = 'ajax';
        echo trim($respuesta);
    }

    function registrar_log($log_tipo, $comentario = '', $descripcion = '', $condicion = '') {
        $ip = $this->gn->getRealIP();
        if ($log_tipo == self::LOG_INGRESO_FALLIDO) {
            $id_usuario = '';
        } else {
            $id_usuario = $this->gn->get_data_loged('id_usuario');
        }
        if ($log_tipo == self::LOG_CREAR || $log_tipo == self::LOG_EDTAR || $log_tipo == self::LOG_BORRAR) {
            $descripcion = $this->array_to_string_log($log_tipo, $descripcion, $condicion);
        }
        $respuesta_registro = $this->db->insert(array('comentario' => $comentario,
            'descripcion' => $descripcion,
            'ip' => $ip,
            'tipo_log_id' => $log_tipo,
            'usuario_id' => $id_usuario,
                ), 'fw_log');
    }

    function array_to_string_log($log_tipo, $campos, $condicion) {
        if ($log_tipo == self::LOG_CREAR) {
            foreach ($campos as $field => $val) {
                $values[$field] = " '" . $val . "' ";
            }
            $query = "`(`" . implode(array_keys($values), "`, `") . "`) VALUES (" . implode($values, ", ") . ")";
        } else {
            foreach ($campos as $field => $val) {
                $fields[] = "`" . $field . "` = '" . $val . "'";
            }
            $where = ($condicion) ? " WHERE " . $condicion : '';
            $query = implode($fields, ", ") . $where;
        }
        return $query;
    }

    function traer_nombre($nombre) {
        $nombre_modulo = "";
        $temporal = preg_split('/(?=[A-Z])/', $nombre, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($temporal as $value) {
            $nombre_modulo = $nombre_modulo . ucfirst($value) . '_';
        }
        return strtolower(substr($nombre_modulo, 0, -1));
    }

    function pintar_mesas($campos, $tablas, $zona, $modulo = '') {
        $id_interlocutor = $this->gn->get_data_loged('id_interlocutor');
        $zona_condicion = " AND m.mesa_id > 0 ";
        if($zona >= 0){
            $zona_condicion = " AND m.mesa_id = " . $zona ;
        }
        
        //$campos = array('id_mesa', 'nombre', 'descripcion', 'estado_id', 'mesero_id', 'orden');
        $mesas = $this->db->select(
                $campos, 
                $tablas,
                'm.estado_id = est.id_estado '
                . ' AND  m.estado_id <> 3 ' . $zona_condicion. ' '
                . ' AND m.interlocutor_id=' . $id_interlocutor, 
                ' m.id_mesa', false, false);
        $observaciones = $this->db->select(array('id_observacion', 'nombre'), 'observacion', '', 'orden', false, false);
        $tema_interlocutor = $this->db->selectOne('t.descripcion', 'fw_tema t,fw_interlocutor_condicion i', 'i.tema_id=t.id_tema and i.interlocutor_id=' . $id_interlocutor);

        require_once 'app/pack/pedido/mesa/view/mesa.html.php';
        mesaHTML::home($mesas, $tema_interlocutor['descripcion'], $modulo, $observaciones, $zona);
    }

    function pintar_reporte($tabla, $id, $encabezado, $campos, $from, $where = "", $group_by = "") {
        $datos_reporte = array();
        $datos_reporte['tabla'] = $tabla;
        $datos_reporte['primaryKey'] = $id;
        $datos_reporte['campos'] = $campos;
        $datos_reporte['from'] = $from;
        $datos_reporte['where'] = $where;
        $datos_reporte['groupBy'] = $group_by;
        foreach ($datos_reporte as $key => $value) {
            $this->gn->set_data_loged($key, $value);
        }
        require_once (PATH_PLUGIN_TABLE_VIEW);
        DatatableHTML::reporte($encabezado, $this->modulo);
    }

    function pintar_tabla_ajax($encabezado, $file) {
        require_once (PATH_PLUGIN_TABLE_VIEW);
        DatatableHTML::reporte($encabezado, $this->modulo, 'TituloReporte', 'example', '', $file);
    }

    function pintar_titulo($modulo) {
        $parametros = $this->gn->traer_parametros('GET');
        $usuario = $this->gn->get_data_loged('id_usuario');
        if (!isset($usuario) || ($usuario <= 0)) {
            return;
        }

        if ($modulo !== 'subopcion') {
            $modulo_datos = $this->db->selectOne(array('descripcion', 'opcion_id'), 'fw_opcion', 'nombre_modulo = "' . $modulo . '"', false, false);
        } else {
            $modulo_datos = $this->db->selectOne(array('descripcion', 'opcion_id'), 'fw_opcion', 'id_opcion = "' . $parametros->id . '"', false, false);
        }
        $modulo_datos['titulo'] = $modulo;
        $url_anterior = $this->crear_url_atras();

        require_once self::PATH_PLUGINS . 'modulo/view/modulo.html.php';
        ModuloHTML::pintar_titulo($modulo_datos, $url_anterior);
    }

    function crear_url_atras() {
        $opcion = "";
        $accion = "";
        $id = "";

        $modulo_anterior = $this->gn->get_data_loged('modulo_anterior');
        $accion_anterior = $this->gn->get_data_loged('accion_anterior');
        $params_anterior = $this->gn->get_data_loged('parametros_anterior');

        if ($modulo_anterior != '') {
            $opcion = "?opcion=" . $modulo_anterior;
        }
        if ($accion_anterior != '') {
            $accion = "&a=" . $accion_anterior;
        }
        if ($params_anterior != '' && isset($params_anterior->id)) {
            $id = "&id=" . $params_anterior->id;
        }
        return $opcion . $accion . $id;
    }

    function borrar_timeout($modulo) {
        require_once (PATH_PLUGIN_MODULO_VIEW);
        ModuloHTML::borrar_timeout($modulo);
    }

    function get_view_path() {
        return $this->modulo_info->base . PATH_PACK_VIEW . $this->modulo_info->pack . "/" . $this->modulo_info->nombre . FOLDER_VIEW . $this->modulo_info->nombre . SUFFIX_PACK_VIEW;
    }

    function get_module_view_path($pack) {
        require_once PATH_APP_PACK . $pack . '/' . $this->modulo . FOLDER_VIEW . $this->modulo . ".html.php";
    }

}
