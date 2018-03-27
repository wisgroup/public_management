<?php

class stdModule {

    const CTRL_BASIC_NORORDER = '7';
    const CRTRL_NOORDER = '6';
    const STD_LOG = '5';
    const STD_CTRL_BASIC = '4';
    const ONLYCTRL_IMAGE = '3';
    const CUSCTRL_FOREING = '2';
    const CUSCTRL_IMAGE = '1';
    const STD_CTRL = '0';
    const EDIT = 'editar';
    const CREATE = 'crear';
    const DELETE = 'eliminar';
    const STATUS = 'estado';
    const ORDER = 'orden';
    const ACTIVO = 'A';
    const BLOQUEADO = 'B';
    const ELIMINADO = 'E';
    const ALL = 'crud';
    const ALL_UNORDERED = "unordered";
    const CRUD_ONLY_EDIT = 'only_edit';
    const CRUD_EDIT = 'edit';

    var $contentType = "";
    var $registros_mostrar = 10;
    var $modulo = "";
    var $clase = "";
    var $orden = "orden";
    private $controles = array(); // Row controls, std or custom
    private $custom_controles = array(); // Row controls, std or custom
    private $CRUD = array(); // Row controls, std or custom
    private $listas_foraneas = array();
    private $llaves_foraneas = array();
    private $default = '';
    private $editFooter = '';
    private $filtros = array('id' => '1', 'nombre' => '1');

    function getContenido($a) {
        $this->contentType = "";

        switch ($a) {
            case "submenu":
                if ($this->default == 'formEdicion')
                    $this->formEdicion();
                else
                    $this->submenu();
                break;
            case "formEdicion":
                $this->formEdicion();
                break;
            case "guardarItem":
                $this->guardarItem();
                break;
            case "editarItem":
                $this->editarItem();
                break;
            case "eliminarItem":
                $this->eliminarItem();
                break;
            case "publicarItem":
                $this->publicarItem();
                break;
            case "publicarPorCheck":
                $this->publicarPorCheck();
                break;
            case "editarOrden":
                $this->editarOrden();
                break;
            default:
                $this->contentType = "html";
                if ($this->default == 'formEdicion')
                    $this->formEdicion();
                else
                    $this->submenu();
        }
    }

    function setConfigs($default, $editFooter) {
        $this->default = $default;
        $this->editFooter = $editFooter;
    }

    function addControls($type, $controls = NULL, $foreing = '') {
        switch ($type) {
            case self::CUSCTRL_IMAGE:
                $this->controles = array('nombre', 'descripcion', 'imagen', 'estado', 'orden');
                break;
            case self::ONLYCTRL_IMAGE:
                $this->controles = array('nombre', 'imagen', 'estado', 'orden');
                break;
            case self::STD_CTRL_BASIC: //4
                $this->controles = array('nombre', 'estado', 'orden');
                break;
            case self::STD_LOG: //5
                $this->controles = array('fecha', 'hora', 'estado');
                break;
            case self::CRTRL_NOORDER: //6
                $this->controles = array('nombre', 'descripcion', 'estado');
                break;
            case self::CTRL_BASIC_NORORDER: //7
                $this->controles = array('nombre', 'estado');
                break;
            default: // Default to text
                $this->controles = array('nombre', 'descripcion', 'estado', 'orden');
                break;
        }
        $this->addCustomControls($controls);
    }

    function addCustomControls($controls) {
        $campo = New stdClass();
        foreach ($controls as $key => $value) {
            $campo = New stdClass();
            $campo->nombre = $key;
            $campo->tipo = $value;
            $this->custom_controles[] = $campo;
        }
    }

    function addList($nombre, $tabla, $foranea) {
        $lista = New stdClass();
        $lista->lista = $nombre;
        $lista->tabla = $tabla;
        $this->listas_foraneas[] = $lista;
        $this->llaves_foraneas[$nombre] = $foranea;
    }

    function getLists() {
        $listas = array();
        foreach ($this->listas_foraneas as $value) {
            $listas[$value->lista] = $this->datos_lista($value->tabla, 'id, nombre');
        }
        return $listas;
    }

    function getControls($tipo = 'SELECT', $edit = "") {
        $return->indices = "";
        $return->campos = "";
        switch ($tipo) {
            case 'SELECT':
                $return->valores = "id";
                break;
            case 'INSERT':
                $return->valores = "id";
                $return->campos = "NULL";
                break;
            case 'UPDATE':
                $return->valores = array();
                break;
            default:
                break;
        }
        foreach ($this->controles as $key => $value) {
            $return->indices.= $key;
            switch ($tipo) {
                case 'SELECT':
                    $return->valores.= ", " . $value;
                    break;
                case 'UPDATE':
                    $return->valores[] = $value;
                    break;
                default:
                    break;
            }
        }
        foreach ($this->custom_controles as $key => $value) {
            $return->indices.= $key;
            switch ($tipo) {
                case 'SELECT':
                    $return->valores.= ", " . $value->nombre;
                    break;
                case 'INSERT':
                    $return->campos .= ", '" . "$" . "parametros->" . $value->nombre . "' ";
                    $return->valores.= ", " . $value->nombre;
                    break;
                case 'UPDATE':
                    $return->valores[] = $value->nombre;
                    break;
                default:
                    break;
            }
        }
        if ($edit == 'EDIT') {

            foreach ($this->llaves_foraneas as $key => $value) {

                $return->indices.= $key;

                switch ($tipo) {

                    case 'SELECT':

                        $return->valores.= ", " . $value;

                        break;

                    case 'INSERT':

                        $return->campos .= ", '" . "$" . "parametros->" . $value->nombre . "' ";

                        $return->valores.= ", " . $value->nombre;

                        break;

                    case 'UPDATE':

                        $return->valores[] = $value;

                        break;

                    default:

                        break;
                }
            }
        }

        return $return;
    }

    function setCrud($type = 'crud') {



        switch ($type) {
            case self::ALL:
                $this->CRUD = array(self::EDIT, self::CREATE, self::DELETE, self::STATUS, self::ORDER);
                break;
            case self::ALL_UNORDERED:
                $this->CRUD = array(self::EDIT, self::CREATE, self::DELETE, self::STATUS);
                break;
            case self::CRUD_ONLY_EDIT :
                $this->CRUD = array(self::EDIT, self::ORDER);
                break;
            case self::CRUD_EDIT :
                $this->CRUD = array(self::EDIT);
                break;
            default: // Default to text
                $this->CRUD = array(self::EDIT, self::CREATE, self::DELETE, self::STATUS, self::ORDER);
                break;
        }
    }

    function getCrud($type = 'estado') {
        return in_array($type, $this->CRUD);
    }

    function getClassName($file) {
        $file = explode('\\', $file);
        //$file = explode('/', $file); //PRODUCCION
        $file = $file[(count($file) - 1)];
        $file = substr($file, 0, -4);
        return $file;
    }

    function submenu() {
        include_once 'classes/funciones.php';
        $fx = New Funciones();
        $inicio = 0;
        $pagina = 1;
        $select = $this->getControls();
        $parametros = $fx->getParametros('GET');
        $condicion_filtros = $fx->getCondicionFiltro($this->filtros, true);

        if (isset($_GET['pagina']) && $_GET['pagina'] != 0) {
            $inicio = (mysql_real_escape_string($_GET['pagina']) - 1) * $this->registros_mostrar;
            $pagina = $_GET['pagina'];
        }
        $sql = "SELECT $select->valores
                FROM $this->modulo
                WHERE estado != '" . self::ELIMINADO . "' $condicion_filtros
                ORDER BY $this->orden ASC
                LIMIT $inicio,$this->registros_mostrar";
//echo $sql;
        $result = mysql_query($sql) or die("Error: " . mysql_error() . ". Linea: " . __LINE__ . ", Funcion: " . __FUNCTION__);
        $select = "id";
        $tabla = $this->modulo;
        $where = "WHERE estado != '" . self::ELIMINADO . "' " . $condicion_filtros;
        $group_by = "";
        $order_by = "ORDER BY $this->orden ASC";
        $datos = array();

        while ($dato = mysql_fetch_object($result)) {
            $datos[] = $dato;
        }
        if (isset($parametros->content_type)) {
            $this->content_type = 'html';
        }

        include("classes/stdModule.html.php");
        $fx->filtros($this->filtros);
        ClaseHTML::submenu($this->clase, $this->CRUD, $this->controles, $datos);
        $fx->mostrarPaginacion($select, $tabla, $where, $group_by, $order_by, $pagina);
    }

    function formEdicion() {
        include("classes/stdModule.html.php");
        $dato = NULL;
        $listas = NULL;
        $select = $this->getControls('SELECT', 'EDIT');
        $listas = $this->getLists();

        if ($_GET["edicion"] == 1 && isset($_GET["id_item"])) {

            $id_item = mysql_real_escape_string($_GET["id_item"]);

            $sql = "SELECT $select->valores

                FROM $this->modulo

                WHERE id = '$id_item'

                LIMIT 1";



            $result = mysql_query($sql) or die("Error: " . mysql_error() . ". Linea: " . __LINE__ . ", Funcion: " . __FUNCTION__);

            $dato = mysql_fetch_object($result);
        }



        ClaseHTML::formEdicion($this->clase, $this->controles, $this->custom_controles, $this->CRUD, $this->llaves_foraneas, $listas, $id_item, $dato, $this->editFooter);
    }

    function guardarItem() {
        include_once 'classes/funciones.php';
        $fx = New Funciones();
        $campos = "id";
        $valores = "NULL";
        $parametros = $fx->getParametros();
        $select = $this->getControls('UPDATE', 'EDIT');
        if ((isset($parametros->estado) && $parametros->estado != "") || (!$this->getCrud('estado') )) {
            $parametros->estado = 'A';
        } else {
            $parametros->estado = 'B';
        }


        foreach ($select->valores as $value) {

            if ($value != 'imagen' && $value != 'orden') {

                $campos .= ", " . $value;

                $valores .= " ,'" . $parametros->$value . "' ";
            } else if ($value == 'orden') {
                $sql = "SELECT MAX(orden)+1 as orden FROM  $this->modulo";
                $result = mysql_query($sql) or die("Error: " . $sql);
                $orden = mysql_fetch_object($result);
                if ($orden->orden <= 0)
                    $orden->orden = 1;

                $campos.= ", orden";
                $valores.= ", $orden->orden";
            }else if ($value == 'imagen') {
                if ($_FILES['imagen']['name'] != "") {
                    $imagen = $_FILES['imagen'];
                    $archivo_big = $fx->generarImagenes($this->clase, 600, 200, $imagen);
                    //$archivo_big = $fx->generarImagen($this->clase, $imagen, "big", $imagen_anterior);
                    $campos.= ", imagen";
                    $valores.= ", '$archivo_big'";
                }
            }
        }

        $sql = "INSERT INTO $this->modulo
                ($campos )
                VALUES ($valores)";

        $result = mysql_query($sql) or die("Error: " . mysql_error() . ". Linea: " . __LINE__ . ", Funcion: " . __FUNCTION__);

        if ($result) {
            $msj = "Elemento guardado correctamente";
        } else {
            $msj = "Ocurri&oacute; un error al guardar. Intente Nuevamente";
        }
        $fx->setCopie('', $msj);
        header("Location:?opcion=$this->clase");
    }

    function editarItem() {
        include_once 'classes/funciones.php';
        $fx = New Funciones();
        $campos = "";
        $parametros = $fx->getParametros();
        $select = $this->getControls('UPDATE', 'EDIT');
        if ((isset($parametros->estado) && $parametros->estado != "") || (!$this->getCrud('estado') )) {
            $parametros->estado = 'A';
        } else {
            $parametros->estado = 'B';
        }

        if ($_FILES['imagen']['name'] != "") {
            $imagen = $_FILES['imagen'];
            $archivo_big = $fx->generarImagenes($this->clase, 600, 200, $imagen);
            //$archivo_big = $fx->generarImagen($this->clase, $imagen, "big", $imagen_anterior);
            $sql_imagen = ", `imagen` = '$archivo_big' ";
        }
        foreach ($select->valores as $value) {
            if ($value != 'imagen' && $value != 'orden') {
                $campos .= $value . " = '" . $parametros->$value . "', ";
            }
        }
        $campos = substr($campos, 0, -2) . " ";

        $sql = "UPDATE $this->modulo
                SET 
                $campos
                $sql_imagen
                WHERE id = '$parametros->id_item'";

        $result = mysql_query($sql) or die("Error: " . mysql_error() . ". Linea: " . __LINE__ . ", Funcion: " . __FUNCTION__);

        if ($result) {
            $msj = "Elemento editado correctamente";
        } else {
            $msj = "Ocurri&oacute; un error al editar. Intente Nuevamente";
        }
        header("Location:?opcion=$this->clase");
    }

    function eliminarItem() {
        include_once 'classes/funciones.php';
        $fx = New Funciones();
        $parametros = $fx->getParametros('GET');

        $sql = "UPDATE $this->modulo
                SET 
                `estado` = 'E', 
                `orden` = 0
                WHERE id = '$parametros->id_item'";

        $result = mysql_query($sql) or die("Error: " . mysql_error() . ". Linea: " . __LINE__ . ", Funcion: " . __FUNCTION__);

        if ($result) {
            $msj = "Elemento eliminado correctamente";
        } else {
            $msj = "Ha ocurrido un error, intente Nuevamente";
        }
        echo "<script>mostrarAlerta('$msj');submenuSecciones('$this->clase');</script>";
    }

    function publicarItem() {

        include_once 'classes/funciones.php';

        $fx = New Funciones();



        $parametros = $fx->getParametros('GET');



        if ($parametros->pb == "B") {

            $publicada = "A";

            $msj = "Item activado correctamente";
        } else {

            $publicada = "B";

            $msj = "Item desactivado correctamente";
        }



        $sql = "UPDATE $this->modulo

                SET `estado` = '$publicada'

                WHERE `id` = '$parametros->id_item'";



        $result = mysql_query($sql) or die("Error: " . mysql_error() . ". Linea: " . __LINE__ . ", Funcion: " . __FUNCTION__);



        if (!$result)
            $msj = "Ha ocurrido un error intenta Nuevamente";



        $this->contentType = "";

        echo "<script>mostrarAlerta('$msj');submenuSecciones('$this->clase');</script>";
    }

    function editarOrden() {

        include_once 'classes/funciones.php';

        $fx = New Funciones();



        $parametros = $fx->getParametros('GET');

        //$fx->debug($parametros, 1);

        if ($parametros->ordenN == $parametros->ordenA) {

            if ($parametros->ordenA <= 1) {

                $parametros->ordenN = 1;
            } else {

                $parametros->ordenN = (int) $parametros->ordenA - 1;
            }
        }



        $sql = "SELECT id

                FROM $this->modulo

                WHERE orden = '$parametros->ordenN'";





        $sqlResult = mysql_query($sql) or die("Error: " . mysql_error() . ". Linea: " . __LINE__ . ", Funcion: " . __FUNCTION__);



        if (mysql_num_rows($sqlResult) > 0) {



            $row = mysql_fetch_array($sqlResult);



            $sql = "UPDATE $this->modulo

                    SET `orden` = '" . $parametros->ordenA . "'

                    WHERE `id` = '" . $row["id"] . "'";



            $result = mysql_query($sql) or die("Error: " . mysql_error() . ". Linea: " . __LINE__ . ", Funcion: " . __FUNCTION__);
        }



        $sql = "UPDATE $this->modulo

                SET `orden` = '" . $parametros->ordenN . "'

                WHERE `id` = '$parametros->id_item'";



        $result = mysql_query($sql) or die("Error: " . mysql_error() . ". Linea: " . __LINE__ . ", Funcion: " . __FUNCTION__);



        if ($result) {

            $msj = "Se cambio el orden correctamente";
        } else {

            $msj = "Ha ocurrido un error intenta Nuevamente";
        }

        echo "<script>mostrarAlerta('$msj');submenuSecciones('$this->clase');</script>";
    }

    function datos_item($id, $tabla = '') {

        if ($tabla === '')
            $tabla = $this->modulo;

        $sql = "SELECT *

                FROM $tabla

                WHERE id = '$id'

                LIMIT 1";



        $result = mysql_query($sql) or die("Error: " . mysql_error() . ". Linea: " . __LINE__ . ", Funcion: " . __FUNCTION__);

        $item = mysql_fetch_object($result);

        return $item;
    }

    function datos_lista($tabla = '', $select = '*') {



        if ($tabla === '')
            $tabla = $this->modulo;

        $sql = "SELECT $select

                FROM $tabla

                WHERE estado = 'A'

                ";

        $result = mysql_query($sql) or die("Error: " . mysql_error() . ". Linea: " . __LINE__ . ", Funcion: " . __FUNCTION__);

        while ($row = mysql_fetch_object($result)) {

            $datos[] = $row;
        }

        return $datos;
    }

}

?>