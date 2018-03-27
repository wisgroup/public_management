<?php

require_once('fw/controlador/php/modulos/modulo.class.php');

class AsideToolBar extends Modulo{



    public $opciones_toolbar = '';

    public $links = '';

    public $modulo_actual = '';



    CONST CLASE_ADMINISTRADOR = 1;

    CONST CLASE_DISTRIBUIDOR = 4;

    CONST CLASE_PUNTOVENTA = 6;

    CONST PERFIL_ADMINISTRADOR = 1;

    CONST PERFIL_OPERATIVO = 2;





    

    function __construct($db, $gn, $modulo) {

        parent::__construct($db, $modulo);

        $this->db = $db;

        $this->gn = $gn;

        $this->modulo_actual = $modulo;

    }

    function home() {



        $perfil = $this->gn->get_data_loged('perfil');

        $clase = $this->gn->get_data_loged('clase');



        if ($clase==self::CLASE_ADMINISTRADOR && $perfil==self::PERFIL_ADMINISTRADOR) { //Administrador Plataforma



            $this->opciones_toolbar['operaciones'] = array('solicitud_pendiente' => 'Solicitudes');

            $this->opciones_toolbar['reportes'] = array('reporte_solicitud'=>'Solicitudes','reporte_saldo'=>'Saldo por Cliente','reporte_gasto'=>'Envios por Cliente','reporte_ultima'=>'Ultimas Solicitudes');

            $this->opciones_toolbar['administracion'] = array('interlocutor' => 'Clientes','proveedor' => 'Proveedores');



        } else if ($clase==self::CLASE_PUNTOVENTA && $perfil==self::PERFIL_OPERATIVO) {  //Punto venta Operativo



            $this->opciones_toolbar['mensajes'] = array('envio_simple' => 'Env&iacute;o de Mensajes', 'envios_programados' => 'Env&iacute;os Programados');

            $this->opciones_toolbar['campa&ntilde;as'] = array('envio_campania' => 'Envio Campa&ntilde;as');

            // $this->opciones_toolbar['administracion'] = array('contacto' => 'Destinatarios', 'grupo' => 'Grupos', 'campania' => 'Campa&ntilde;as','mensaje' => 'Mensajes');

            $this->opciones_toolbar['administracion'] = array('contacto' => 'Destinatarios','mensaje' => 'Mensajes');

            $this->opciones_toolbar['reportes'] = array('reporte_ultimos' => '&Uacute;ltimos Mensajes','reporte_campanias' => 'Campa&ntilde;as');



        }else if($clase==self::CLASE_PUNTOVENTA && $perfil==self::PERFIL_ADMINISTRADOR){ //Punto venta Administrador



            $this->opciones_toolbar['administracion'] = array('usuario' => 'Usuarios');

            $this->opciones_toolbar['reportes'] = array('reporte_ultimos' => '&Uacute;ltimos Mensajes','reporte_campanias' => 'Campa&ntilde;as','reporte_solicitud'=>'Reporte Solicitudes');

        

        }else if($clase==self::CLASE_DISTRIBUIDOR && $perfil==self::PERFIL_ADMINISTRADOR){

            $this->opciones_toolbar['operaciones'] = array('solicitud_pendiente' => 'Solicitudes Pendientes','envio_solicitud'=>'Enviar Solicitud');

            $this->opciones_toolbar['administracion'] = array('interlocutor' => 'Clientes');

            $this->opciones_toolbar['reportes'] = array('reporte_solicitud'=>'Solicitudes','reporte_saldo'=>'Saldo por Cliente','reporte_ultima'=>'Ultimas Solicitudes');



        }



        $this->links['solicitud_pendiente'] = 'solicitud';

        $this->links['envio_solicitud'] = 'solicitud_envio';



        $this->links['envio_simple'] = 'mensaje_envio';

        $this->links['envios_programados'] = 'mensaje_envio,acc,envios_programados';



        $this->links['envio_campania'] = 'campania_envio';



        $this->links['contacto'] = 'contacto';

        $this->links['grupo'] = 'grupo';

        $this->links['campania'] = 'campania';

        $this->links['mensaje'] = 'mensaje';

        $this->links['usuario'] = 'usuario';



        $this->links['interlocutor'] = 'interlocutor';

        $this->links['proveedor'] = 'proveedor';



        $this->links['reporte_ultimos'] = 'reporte_enviados';

        $this->links['reporte_campanias'] = 'reporte_campania';

        $this->links['reporte_solicitud'] = 'reporte_solicitud';



        $this->links['reporte_saldo'] = 'reporte_saldo';

        $this->links['reporte_gasto'] = 'reporte_gasto';

        $this->links['reporte_ultima'] = 'reporte_ultima';











        require_once("fw/vista/html/aside_toolbar.html.php");

        AsideToolBarHTML::ToolBar($this->opciones_toolbar, $this->modulo_actual, $this->links);

        

    }

    function cargar_contenidos(){

     $subopciones = $this->db->select(array('s.titulo'),'fw_opcion s'," s.estado_id = '3'");	

     $d = "|";

     foreach ($subopciones as $value) {

         $d .= $value['titulo'].'|';

     }

     echo $d;

 }

}

