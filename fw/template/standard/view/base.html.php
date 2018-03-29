<?php

class TemplateBase {

    public $titulo_sitio;
    public $modulo;
    public $seccion;
    public $accion;
    private $contenido;
    private $db;
    private $gn;
    private $interlocutor_configuracion;
    public $saldo;

    CONST PATH_CONTROLLER = "fw/template/standard/controller/";
    CONST PATH_JS_LIB = "fw/lib/js/";
    CONST PATH_TEMPLATE = "fw/template/standard/";
    CONST PATH_FW_DEFAULT = "fw/template/_default/";
    CONST PATH_FW_PACK = "fw/pack/";

    function __construct($contenido, $conexion_base_datos, $generales, $sesion, $interlocutor_configuracion) {

        $this->contenido = $contenido;
        $this->db = $conexion_base_datos;
        $this->sesion = $sesion;
        $this->gn = $generales;
        $this->interlocutor_configuracion = $interlocutor_configuracion;
    }

    function home() {
        ?>  
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title><?php echo $this->titulo_sitio ?></title>            
                <?php
                $this->favico();
                ?>
                <!-- Perteneciente al framework -->
                <audio id="fw_audio">
                    <source src="media/sounds/bit1.mp3" type="audio/mpeg">, 
                    <source src="sonidos/sonido_notificacion.ogg" type="audio/ogg">, 
                    <source src="sonidos/sonido_notificacion.mp3" type="audio/mpeg">
                </audio>
                <link rel="stylesheet" href="<?php echo self::PATH_FW_DEFAULT; ?>view/css/wis_styles.css" >
                <link rel="stylesheet" href="<?php echo self::PATH_FW_DEFAULT; ?>view/css/generales.css" type="text/css" media="screen" />
                <link rel="stylesheet" href="<?php echo self::PATH_TEMPLATE; ?>view/css/chosen.min.css" type="text/css" media="screen" />
                <link rel="stylesheet" href="<?php echo self::PATH_TEMPLATE; ?>view/css/header.css" type="text/css" media="screen" /> 
                <link rel="stylesheet" href="<?php echo self::PATH_TEMPLATE; ?>view/css/nav.css" type="text/css" media="screen" /> 
                <link rel="stylesheet" href="<?php echo self::PATH_TEMPLATE; ?>view/css/migadepan.css" type="text/css" media="screen" /> 
                <link rel="stylesheet" href="<?php echo self::PATH_TEMPLATE; ?>view/css/footer.css" type="text/css" media="screen" />  
                <link rel="stylesheet" href="<?php echo PATH_EXT_PLUGIN; ?>js/jquery-ui/jquery-ui.css" type="text/css" media="screen" />  
                <link rel="stylesheet" href="<?php echo self::PATH_FW_DEFAULT; ?>view/css/maestro.css" type="text/css" media="screen" /> 
                <link rel="stylesheet" href="fw/template/reporte/view/css/reporte.css" type="text/css" media="screen" /> 
                <link rel="stylesheet" href="<?php echo self::PATH_FW_DEFAULT; ?>view/css/permisos_interlocutor.css" type="text/css" media="screen" /> 
                <?php if (file_exists("fw/themes/" . $this->interlocutor_configuracion['tema'] . "/tema.css")) { ?>
                    <link rel="stylesheet" href="fw/themes/<?php echo $this->interlocutor_configuracion['tema'] ?>/tema.css" type="text/css" media="screen" />  
                <?php } ?>
                <?php if (file_exists("fw/themes/" . $this->interlocutor_configuracion['tema'] . "/forms.css")) { ?>
                    <link rel="stylesheet" href="fw/themes/<?php echo $this->interlocutor_configuracion['tema'] ?>/forms.css" type="text/css" media="screen" />  
                <?php } ?>
                <link rel="stylesheet" href="fw/pack/base/inicio/view/css/inicio.css" type="text/css" media="screen" /> 
                <link rel="stylesheet" href="<?php echo self::PATH_FW_DEFAULT; ?>view/css/usuario_datos.css" type="text/css" media="screen" /> 
                <link rel="stylesheet" href="<?php echo self::PATH_FW_DEFAULT; ?>view/css/configuracion.css" type="text/css" media="screen" /> 
                <link rel="stylesheet" href="<?php echo self::PATH_FW_DEFAULT; ?>view/css/usuario_cambio_clave.css" type="text/css" media="screen" /> 
                <link rel="stylesheet" type="text/css" href="<?php echo PATH_EXT_PLUGIN; ?>js/datetimepicker/jquery.datetimepicker.css"/>
                <link rel="stylesheet" href="<?php echo PATH_EXT_PLUGIN; ?>js/jAlert/jquery.alerts.css" type="text/css" media="screen" />    
                <link rel="shortcut icon" href="fw/vista/imagenes/favicon.ico" type="image/x-icon" /> 
                <link rel="stylesheet"  href="<?php echo PATH_EXT_PLUGIN; ?>js/Bootstrap-3.3.2/css/bootstrap.css" >
                <link rel="stylesheet"  href="<?php echo self::PATH_FW_DEFAULT; ?>view/css/bootstrap_personalizado.css" >
                <link rel="stylesheet"  href="<?php echo self::PATH_FW_DEFAULT; ?>view/css/usuario_perfil.css" >
                <?php if (file_exists("fw/vista/css/$this->seccion.css")) { ?>
                    <link rel="stylesheet" href="fw/vista/css/<?php echo $this->seccion; ?>.css" type="text/css" media="screen" />  
                <?php } ?>
                <link rel="stylesheet" href="<?php echo PATH_EXT_PLUGIN; ?>js/DataTables/media/css/jquery.dataTables_themeroller.css" type="text/css" media="screen" />  
                <link rel="stylesheet" href="<?php echo PATH_EXT_PLUGIN; ?>js/TableTools/css/dataTables.tableTools.css" type="text/css" media="screen" />  
                <link rel="stylesheet" href="<?php echo PATH_EXT_PLUGIN; ?>js/DataTables/media/css/demo_table_jui.css" type="text/css" media="screen" />    
                <script type="text/javascript" src="<?php echo PATH_EXT_PLUGIN; ?>js/jquery-1.11.1.js"></script>        
                <script type="text/javascript" src="<?php echo PATH_EXT_PLUGIN; ?>js/DataTables/media/js/jquery.dataTables.js"></script>
                <script type="text/javascript" src="<?php echo PATH_EXT_PLUGIN; ?>js/DataTables/media/js/jquery.dataTables.columnFilter.js"></script>
                <script type="text/javascript" src="<?php echo PATH_EXT_PLUGIN; ?>js/TableTools/js/dataTables.tableTools.js"></script>
                <script type="text/javascript" src="<?php echo PATH_EXT_PLUGIN; ?>js/jquery.number.js"></script>
                <script type="text/javascript" src="<?php echo PATH_EXT_PLUGIN; ?>js/jquery-ui.js"></script>
                <script type="text/javascript" src="<?php echo self::PATH_JS_LIB; ?>utilities.js"></script>          
                <script type="text/javascript" src="<?php echo self::PATH_JS_LIB; ?>validaciones.js"></script>             
                <script type="text/javascript" src="<?php echo PATH_EXT_PLUGIN; ?>js/chosen/chosen.jquery.js"></script> 
                <script type="text/javascript" src="<?php echo PATH_EXT_PLUGIN; ?>js/chosen/chosen.jquery.min.js"></script> 
                <script type="text/javascript" src="<?php echo self::PATH_FW_PACK; ?>base/subopcion/controller/js/subopcion.js"></script>  
                <script type="text/javascript" src="<?php echo PATH_EXT_PLUGIN; ?>js/datetimepicker/jquery.datetimepicker.js"></script>
                <script type="text/javascript" src="<?php echo PATH_EXT_PLUGIN; ?>js/jAlert/jquery.alerts.mod.js"></script>
                <script type="text/javascript" src="<?php echo PATH_EXT_PLUGIN; ?>js/Keyboards/js/jquery.mousewheel.js"></script>                    
                <script type="text/javascript" src="<?php echo PATH_EXT_PLUGIN; ?>js/jquery-validation/dist/jquery.validate.js"></script>
                <script type="text/javascript" src="<?php echo PATH_EXT_PLUGIN; ?>js/jquery-validation/dist/localization/messages_es.js"></script>
                <script type="text/javascript" src="app/lib/js/transacciones.js"></script>
                <script src="<?php echo PATH_EXT_PLUGIN; ?>js/Bootstrap-3.3.2/js/bootstrap.min.js"></script>
                <?php try { ?>
                    <!--<script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1','packages':['corechart']}]}"></script>-->
                    <?php
                } catch (Exception $e) {
                    
                }
                ?>
                <?php if (file_exists("fw/controlador/js/$this->seccion.js")) { ?>
                    <script type="text/javascript" src="fw/controlador/js/<?php echo $this->seccion; ?>.js"></script>
                <?php } ?>
                <!-- especifico de cada app -->
                <link rel="stylesheet" href="app/pack/transaccion/operaciones/view/css/operaciones.css" type="text/css" media="screen" />
                
                <?php if (file_exists("app/vista/css/$this->seccion.css")) { ?>
                    <link rel="stylesheet" href="fw/vista/css/<?php echo $this->seccion; ?>.css" type="text/css" media="screen" />  
                <?php } ?>
                <script type="text/javascript" src="<?php echo PATH_APP_PLUGINS; ?>operaciones/controller/js/operaciones.js"></script>
                
                <script src='app/controlador/js/lib/pdfmake-master/build/pdfmake.min.js'></script>
                <script src='app/controlador/js/lib/pdfmake-master/build/vfs_fonts.js'></script>
                <?php if (file_exists("fw/pack/" . $this->modulo->tipo . "/" . $this->seccion . "/controller/js/" . $this->seccion . ".js")) { ?>
                    <script type="text/javascript" src="fw/pack/<?php echo $this->modulo->tipo; ?>/<?php echo $this->seccion; ?>/controller/js/<?php echo $this->seccion; ?>.js"></script>
                <?php } ?>
            </head>
            <body id="body" class="wis_seccion <?php
                echo $this->seccion;
                if ($this->sesion) {
                    echo " auth";
                } else {
                    echo " no_auth";
                }
            ?> ">
                <header>
                    <div class="header-theme-background">
                        <?php
                        require_once(self::PATH_CONTROLLER . "header.php");
                        $h = New Header($this->sesion, $this->interlocutor_configuracion, $this->saldo, $this->db, $this->gn);
                        ?>	
                    </div>
                </header>
                <div class="container">
                    <section id="bloque_uno">

                        <div id="wis_app_contenido" class=" container app_contenido app_estado_normal">
                            <div id="area_trabajo" class="area_trabajo container">
                                <?php echo $this->contenido; ?>
                            </div>
                            <?php
                            if ($this->sesion == true) {
                                //require_once 'controlador/php/modulos/aside_toolbar.php';
                                //$aside_toolbar_control = new AsideToolBar($this->db, $this->gn, $this->seccion);
                                //$aside_toolbar_control->home();  
                            }
                            ?>
                        </div>
                        <?php if ($this->sesion == true) { ?>
                            <div id="menu" class="container hidden-xs">
                                <nav>
                                    <?php
                                    require_once(self::PATH_CONTROLLER . "nav.php");
                                    $n = new Nav($this->db, $this->gn);
                                    ?>   
                                </nav>
                            </div>                  
                        <?php } ?>
                    </section>
                </div>
                
                <footer class="footer">
                    <?php
                    $this->base_footer();
                    ?>
                </footer> 
            </body>
            <?php
            $this->base_flotante();
            ?>
        </html>
        <?php
    }

    static function favico() {
        ?>
        <link rel="apple-touch-icon" sizes="57x57" href="media/favico/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="media/favico/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="media/favico/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="media/favico/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="media/favico/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="media/favico/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="media/favico/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="media/favico/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="media/favico/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="media/favico/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="media/favico/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="media/favico/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="media/favico/favicon-16x16.png">
        <link rel="manifest" href="media/favico/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="media/favico/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">
        <?php
    }

    static function base_footer() {
        require_once("fw/template/standard/controller/footer.php");
        $ft = new Footer();
    }

    static function base_flotante() {
        ?>
        <section id="flotante">
            <div id="flotante_body">
                <div id="flotante_cerrar" onclick="cerrar_flotante();" onfocus="this.blur();">X</div>
                <div id="flotante_contenido"></div>
            </div>
        </section>
        <?php
    }

}
