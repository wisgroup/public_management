<?php

class Main {
    /* @var WISMysqli */

    private $conexion_base_datos = 0;

    /* @var WisFwQueries */
    private $WISQueries;
    private $ruta_cron = '';
    private $configuracion;

    /* @var Generales */
    private $gn;

    /* @var Notificacion */
    private $nc;

    /* @var Validacion */
    private $va;

    /* @var App_Config */
    private $appConfig;

    /* @var user_Config */
    private $customConfig;

    /* @var Modulo */
    private $modulo;

    /* @var InfoModulo */
    private $info_modulo;
    private $estado_sesion;
    private $interlocutor_conf;

    CONST CONTENT_TYPE_HTML = 'html';
    CONST CONTENT_TYPE_AJAX = 'ajax';
    CONST CONTENT_TYPE_EXPORT = 'export';
    CONST CONTENT_TYPE_AJAX_PART = 'ajax_part';
    CONST CONTENT_TYPE_WS = 'ws';
    CONST CONTENT_TYPE_REPORT = 'reporte';

    function __construct($opcion = NULL, $accion = '') {
        require_once 'fw/lib/defines.php';
        require_once 'app/lib/defines.php';
        require_once 'fw/lib/functions.php';

        if ($opcion !== NULL) {
            $this->ruta_cron = '/usr/share/nginx/html/';
        }
        $this->info_modulo = New InfoModulo();
        $this->estado_sesion = false;

        $this->cargar_librerias();
        $parametros = $this->gn->traer_parametros('GET');
        
        $this->cargar_configuracion();
        $this->cargar_modulo($opcion, $accion);
    }

    function cargar_modulo($opcion = NULL, $accion = '') {
        /* VALIDACIONES DE PARAMETROS REFERENTES AL MODULO */
        $this->info_modulo->nombre = $opcion;
        if ($opcion === NULL) {
            $parametros = $this->gn->traer_parametros('GET');
            if (!isset($parametros->opcion) || empty($parametros->opcion)) {
                $parametros->opcion = 'inicio';
            }
            $this->info_modulo->nombre = $parametros->opcion;
        }

        $this->info_modulo->metodo = $accion;
        if (isset($parametros->a)) {
            $this->info_modulo->metodo = $parametros->a;
        }

        if (!isset($parametros->type)) {
            $parametros->type = self::CONTENT_TYPE_HTML;
        }
        $this->info_modulo->content_type = $parametros->type;

        $this->validarSesion();

        /* CARGAR MODULO SOLICITADO */
        $this->cargarInfoModulo($this->info_modulo->nombre);

        /* VALIDACIONES DE EXISTENCIA DE ARCHIVO DE MODULO */
        if (!file_exists($this->info_modulo->file_path)) {
            if (!file_exists($this->info_modulo->file_path)) {
                echo "ERROR 404: Archivo no existe - " . $this->info_modulo->file_path;
                return false;
            } else {
                $this->info_modulo->base = "fw";
                require_once($this->ruta_cron . $this->info_modulo->file_path);
            }
        } else {
            require_once($this->info_modulo->file_path);
        }
        if (isset($parametros->type) && ($parametros->type != self::CONTENT_TYPE_AJAX_PART)&& ($parametros->type != self::CONTENT_TYPE_EXPORT)) {
            $this->limpiar_entorno();
        }
        
        /* NOMBRE DE CLASE: Se transforma el nombre de modulo en el formato de nombre de clase */
        if (strpos($this->info_modulo->nombre, '_')) {
            $temporal = explode('_', $this->info_modulo->nombre);
            foreach ($temporal as $value) {
                $this->info_modulo->nombre_clase .= ucfirst($value);
            }
        } else {
            $this->info_modulo->nombre_clase = ucfirst($this->info_modulo->nombre);
        }

        /* VALIDAR EXISTENCIA DE CLASE */
        if (!class_exists(ucfirst($this->info_modulo->nombre_clase))) {
            echo "ERROR 612: Clase no existe (" . $this->info_modulo->nombre_clase . ")";
            return false;
        }

        $this->almacenar_ultimo_modulo($this->info_modulo->nombre, $this->info_modulo->metodo, $this->info_modulo->content_type, $parametros);
        ob_start();

        /* INSTANCIANDO MODULO */
        eval("$" . "this->modulo = new " . $this->info_modulo->nombre_clase . "($" . "this->conexion_base_datos, $" . "this->info_modulo->nombre, $" . "this->ruta_cron, $" . "this->info_modulo, $" . "this->WISQueries , $" . "this->appQry );");

        if (!array_key_exists('a', $parametros) || $parametros->a === '') {
            $this->info_modulo->metodo = 'home';
        }

        $this->modulo->traer_accion($this->info_modulo->metodo);
        $contenido = ob_get_contents();
        ob_end_clean();

        if ($this->info_modulo->nombre == 'subopcion') {
            $this->info_modulo->nombre = $this->traer_opcion($parametros->id, 'opciones_menu');
        }

        /* TODO: Se debe borrar la siguiente linea, solo se usa para propositos de debugging */
        //$modulo->tipo_contenido = 'html';

        /* VALIDACION DE TIPO DE CONTENIDO */
        if (($this->modulo->tipo_contenido == self::CONTENT_TYPE_HTML) && (!isset($this->info_modulo->content_type) || $this->info_modulo->content_type === self::CONTENT_TYPE_HTML)) {
            require("fw/template/standard/view/base.html.php");
            $plantilla = new TemplateBase($contenido, $this->conexion_base_datos, $this->gn, $this->estado_sesion, $this->interlocutor_conf);
            $plantilla->titulo_sitio = $this->configuracion->titulo_sitio;
            $plantilla->seccion = $this->info_modulo->nombre;
            $plantilla->modulo = $this->info_modulo;
            $plantilla->accion = $this->info_modulo->metodo;
            $plantilla->home();
            $this->nc->verificar_notificacion();
        } else {
            echo $contenido;
        }

        $this->conexion_base_datos->close();
    }

    function cargar_configuracion() {
        $configuracion = $this->gn->get_data_loged('configuracion_inicial');
        if (empty($configuracion)) {
            $configuracion = $this->conexion_base_datos->select(array('nombre', 'valor'), "fw_configuracion", "estado_id = 1");
            $this->gn->set_data_loged('configuracion_inicial', $configuracion);
        }
        //$query= array("@@GLOBAL.sql_mode = ''");
        //mysqli_query($this->conexion_base_datos, $query);
        //TODO: Debe verificarse la causa del fallo de la siguiente linea para reemplazar el query anterior
        //$this->conexion_base_datos->query($query);
        $this->configuracion = new stdClass();
		//echo "<pre>";
		
        foreach ($configuracion as $value) {
			$nombre = $value['nombre'];
            $this->configuracion->$nombre = $value['valor'];
        }
    }

    function cargar_configuracion_interlocutor() {
        $interlocutor = $this->gn->get_data_loged('id_interlocutor');
        $usuario = $this->gn->get_data_loged('id_usuario');
        $configuracion_interlocutor = $this->WISQueries->ejecutarQuery(
                'consultaInterlocutorxIDUsuario', array('interlocutor' => $interlocutor, 'usuario' => $usuario));

        $this->gn->set_data_loged('proveedor_marca_blanca', $configuracion_interlocutor['marca_blanca']);
        return $configuracion_interlocutor;
    }

    function traer_opcion($id, $opciones) {
        $opciones_menu = $this->gn->get_data_loged($opciones);
        if (!$opciones_menu) {
            return false;
        }
        foreach ($opciones_menu as $value) {
            if ($value['id_opcion'] == $id) {
                return $value['descripcion'];
            }
        }
    }

    function almacenar_ultimo_modulo($modulo, $accion, $type, $parametros) {
        $modulo_anterior = $this->gn->get_data_loged('modulo_actual');
        $accion_anterior = $this->gn->get_data_loged('accion_actual');
        $type_anterior = $this->gn->get_data_loged('type_actual');
        $parametros_anterior = $this->gn->get_data_loged('parametros_actual');

        $this->gn->set_data_loged('modulo_anterior', $modulo_anterior);
        $this->gn->set_data_loged('accion_anterior', $accion_anterior);
        $this->gn->set_data_loged('type_anterior', $type_anterior);
        $this->gn->set_data_loged('parametros_anterior', $parametros_anterior);


        $this->gn->set_data_loged('modulo_actual', $modulo);
        $this->gn->set_data_loged('accion_actual', $accion);
        $this->gn->set_data_loged('type_actual', $type);
        $this->gn->set_data_loged('parametros_actual', $parametros);
    }

    function cargar_librerias() {
        require_once ($this->ruta_cron . "app/lib/custom_config.php");
        
        require_once ($this->ruta_cron . PATH_APP_CONFIG);
        $this->appConfig = new App_Config();
        require_once($this->ruta_cron . 'modelo/mysqli.class.php');
        $this->conexion_base_datos = new WISMysqli($this->appConfig->_get('host'), $this->appConfig->_get('usuario'), $this->appConfig->_get('clave'), $this->appConfig->_get('base_datos'), false);
        require_once $this->ruta_cron . 'modelo/fw_queries/wis_fw_queries.class.php';
        $this->WISQueries = new WisFwQueries($this->conexion_base_datos);
        require_once $this->ruta_cron . 'modelo/app_queries/app_control_queries.class.php';
        $this->appQry = new AppControlQueries($this->conexion_base_datos);
        require_once $this->ruta_cron . PATH_LIB_CLASS.LIB_GENERALES;
        $this->gn = new Generales($this->conexion_base_datos, $this->ruta_cron);
        require_once $this->ruta_cron . PATH_LIB_CLASS. 'notificacion.class.php';
        $this->nc = new Notificacion($this->conexion_base_datos, $this->gn, $this->ruta_cron);
        require_once $this->ruta_cron . PATH_LIB_CLASS. 'validacion.class.php';
        $this->va = new Validacion($this->conexion_base_datos, $this->gn, $this->ruta_cron);
    }

    function validarSesion() {
        $usuario = $this->gn->get_data_loged('id_usuario');
        if (isset($usuario) && !empty($usuario)) {
            $this->estado_sesion = true;
            $this->interlocutor_conf = $this->cargar_configuracion_interlocutor();
        } else if (!isset($this->info_modulo->content_type) || $this->info_modulo->content_type != self::CONTENT_TYPE_HTML) {
            //TODO: Llevar a un  metodo en inicio que diga que la sesion expiró y poner un timer para recargar 
            $this->info_modulo->nombre = 'inicio';
        } else {
            $this->info_modulo->nombre = 'inicio';
        }
    }

    function cargarInfoModulo($nombre_modulo) {
        $this->info_modulo->file_path = $this->ruta_cron;
        $this->info_modulo->base = "fw";
        $this->info_modulo->pack = "base";
        switch ($nombre_modulo) {
            case 'subopcion':
                $this->info_modulo->file_path .= "fw/pack/base/subopcion/controller/subopcion.php";
                break;
            case 'inicio':
                $this->info_modulo->file_path .= "fw/pack/base/inicio/controller/inicio.php";
                break;
            default:
                $modulo = $this->WISQueries->ejecutarQuery('consultaModuloxNombre', array('nombre_modulo' => $nombre_modulo));
                $this->info_modulo->nombre = $modulo['nombre_modulo'];
                $this->info_modulo->descripcion = $modulo['descripcion'];
                $this->info_modulo->id = $modulo['id_opcion'];
                $this->info_modulo->base = $modulo['base'];
                $this->info_modulo->tipo = $modulo['tipo'];
                $this->info_modulo->id_tipo = $modulo['opcion_tipo_id'];
                $this->info_modulo->pack = $modulo['pack'];
                $this->info_modulo->file_path .= $this->info_modulo->base . "/pack/";
                if (!is_null($this->info_modulo->pack)) {
                    $this->info_modulo->file_path .= $this->info_modulo->pack . "/";
                }
                $this->info_modulo->file_path .= $this->info_modulo->nombre . "/controller/" . $this->info_modulo->nombre . ".php";
                break;
        }
        $this->cargarLibreriaxTipoContenido($this->info_modulo->id_tipo);
    }

    function cargarLibreriaxTipoContenido($id_tipo) {

        switch ($id_tipo) {
            case CONTENIDO_MAESTRO:
                require_once(PATH_PLUGIN_MAESTRO);
                break;
            case CONTENIDO_REPORTE:
                require_once(PATH_PLUGIN_REPORTE);
                break;
            case CONTENIDO_MODULO:
                require_once(PATH_PLUGIN_MODULO);
                break;
            case CONTENIDO_OPERACION:
                require_once(PATH_APP_PLUGINS . APP_PLUGIN_OPERACIONES);
                break;
            case CONTENIDO_INTERLOCUTOR:
                require_once(PATH_PLUGIN_INTERLOCUTOR);
                break;
            default:
                require_once(PATH_PLUGIN_MODULO);
                break;
        }
    }

    function limpiar_entorno() {
        ?>
        <script>
            clearTimeout(timeOut);
        </script>
        <?php

    }

}
