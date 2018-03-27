<?php

class stdModule {

    const CRTRL_NOORDER = '4';
    const ONLYCTRL_IMAGE = '3';
    const CUSCTRL_FOREING = '2';
    const CUSCTRL_IMAGE = '1';
    const STD_CTRL = '0';
    
    const EDIT = 'editar';
    const CREATE = 'nuevo';
    const DELETE = 'eliminar';
    const STATUS = 'estado';
    const ORDER = 'orden';
    
    const ALL = 'crud';
    const CRUD_ONLY_EDIT = 'only_edit';

    var $contentType = "";
    var $registros_mostrar = 10;
    var $modulo = "";
    var $clase = "";
    private $controles = array(); // Row controls, std or custom
    private $CRUD = array(); // Row controls, std or custom
    private $listas = array();

    function getContenido($a) {
        $this->contentType = "";
       
        switch ($a) {
            case "submenu":
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
                $this->submenu();
        }
    }

    function addControls($type) {

        switch ($type) {
            case self::CUSCTRL_IMAGE:
                $this->controles = array('nombre', 'descripcion', 'imagen', 'estado', 'orden');
                break;
            case self::ONLYCTRL_IMAGE:
                $this->controles = array('nombre', 'imagen', 'estado', 'orden');
                break;
            case self::CUSCTRL_FOREING: //Controles standard + una lista
                $this->controles = array('nombre', 'lista', 'descripcion', 'estado', 'orden');
                break;
            case self::CRTRL_NOORDER: //Controles standard + una lista
                $this->controles = array('nombre', 'lista', 'descripcion', 'estado');
                break;
            default: // Default to text
                $this->controles = array('nombre', 'descripcion', 'estado', 'orden');
                break;
        }
    }
    function setCrud($type = 'ALL') {

        switch ($type) {
            case self::ALL:
                $this->CRUD = array( self::EDIT, self::CREATE, self::DELETE, self::STATUS, self::ORDER);
                break;
            case self::CRUD_ONLY_EDIT :
                $this->CRUD = array( self::EDIT, self::ORDER);
                break;
            
            default: // Default to text
                $this->CRUD = array( self::EDIT, self::CREATE, self::DELETE, self::STATUS, self::ORDER);
                break;
        }
    }
    
    function setLista($tabla, $key){
        $nueva_lista = new stdClass();
        $nueva_lista->tabla = $tabla;
        $nueva_lista->key = $key;
        $this->listas[] = $nueva_lista;
    }

    function getClassName($file) {
        $file = explode('\\', $file);
        $file = $file[(count($file) - 1)];
        $file = substr($file, 0, -4);
        return $file;
    }

    function submenu() {
        include_once 'classes/funciones.php';
        $fx = New Funciones();

        $inicio = 0;
        $select = $this->getControls();
        
        if (isset($_GET['pagina']) && $_GET['pagina'] != 0)
            $inicio = (mysql_real_escape_string($_GET['pagina']) - 1) * $this->registros_mostrar;

        $sql = "SELECT $select->valores
                FROM $this->modulo
                WHERE estado != 'E'
                ORDER BY orden ASC
                LIMIT $inicio,$this->registros_mostrar";

        $result = mysql_query($sql) or die("Error: " . mysql_error() . " " . __LINE__ . ", " . __FUNCTION__);

        $select = "id";
        $tabla = $this->modulo;
        $where = "";
        $group_by = "";
        $order_by = "ORDER BY orden ASC";

        $datos = array();

        while ($dato = mysql_fetch_object($result)) {
            $datos[] = $dato;
        }

        include("classes/stdModule.html.php");
        ClaseHTML::submenu($this->clase, $this->CRUD, $datos);
        $fx->mostrarPaginacion($select, $tabla, $where, $group_by, $order_by);
    }

    function formEdicion() {
        include("classes/stdModule.html.php");
        $dato = NULL;
        $listas = NULL;
        
        $select = $this->getControls();
        
        if ($_GET["edicion"] == 1 && isset($_GET["id_item"])) {
            $id_item = mysql_real_escape_string($_GET["id_item"]);
            $sql = "SELECT $select->valores
                FROM $this->modulo
                WHERE id = '$id_item'
                LIMIT 1";

            $result = mysql_query($sql) or die("Error: " . mysql_error() . ". Linea: " . __LINE__ . ", Funcion: " . __FUNCTION__);
            $dato = mysql_fetch_object($result);
        }
        if(isset($this->listas[0]) && $this->listas[0]->tabla !== '')
            $listas = $this->datos_lista($this->listas[0]->tabla);
        
        
        ClaseHTML::formEdicion($this->clase, $this->controles, $this->listas, $listas, $id_item, $dato);
    }

    function guardarItem() {
        include_once 'classes/funciones.php';
        $fx = New Funciones();

        $parametros = $fx->getParametros();

        if (isset($parametros->publicado) && $parametros->publicado != "") {
            $publicado = "A";
        } else {
            $publicado = "B";
        }
        if ($_FILES['imagen']['name'] != "") {
            $imagen = $_FILES['imagen'];
            $archivo_big = $fx->generarImagen($this->clase, $imagen, "big", $imagen_anterior);
        }

        $sql = "SELECT MAX(orden)+1 as orden FROM  $this->modulo";
        $result = mysql_query($sql) or die("Error: " . $sql);

        $orden = mysql_fetch_object($result);
        if ($orden->orden <= 0)
            $orden->orden = 1;

        $sql = "INSERT INTO $this->modulo
                (`nombre` ,`descripcion`,`estado`, `orden`)
                VALUES ('$parametros->nombre','$parametros->editor1','$publicado', '" . $orden->orden . "')";

        $result = mysql_query($sql) or die("Error: " . mysql_error());

        $msj = "Elemento guardado correctamente";
        $fx->setCopie('', $msj);
        header("Location:?opcion=$this->clase");
    }

    function editarItem() {
        include_once 'classes/funciones.php';
        $fx = New Funciones();

        $parametros = $fx->getParametros();
        $select = $this->getControls('UPDATE');

        if (isset($parametros->estado) && $parametros->estado != "") {
            $parametros->estado = 'A';
        } else {
            $parametros->estado = 'B';
        }

        if ($_FILES['imagen']['name'] != "") {
            $imagen = $_FILES['imagen'];
            $archivo_big = $fx->generarImagenes($this->clase, 600, 200, $imagen);
            //$archivo_big = $fx->generarImagen($this->clase, $imagen, "big", $imagen_anterior);
            $sql_imagen = "`imagen` = '$archivo_big' ";
        }
        foreach ($select->valores as $value) {
            if($value != 'imagen' && $value != 'orden'){
                $campos = $value ." = '".$parametros->$value."', ";
            }
        }
        
        $sql = "UPDATE $this->modulo
                SET 
                $campos
                $sql_imagen
                WHERE id = '$parametros->id_item'";

        $result = mysql_query($sql) or die("Error: " . mysql_error());

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
                `estado` = 'E' 
                WHERE id = '$parametros->id_item'";

        $result = mysql_query($sql) or die("Error: " . mysql_error());

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

        $result = mysql_query($sql) or die("Error: " . mysql_error());

        if (!$result)
            $msj = "Ha ocurrido un error intenta Nuevamente";

        $this->contentType = "";
        echo "<script>mostrarAlerta('$msj');submenuSecciones('$this->clase');</script>";
    }

    function editarOrden() {
        include_once 'classes/funciones.php';
        $fx = New Funciones();

        $parametros = $fx->getParametros('GET');

        if (($parametros->ordenN == 0) || ($parametros->ordenN == $parametros->ordenA)) {
            $parametros->ordenN = (int) $parametros->ordenA + 1;
        }

        $sql = "SELECT id
                FROM $this->modulo
                WHERE orden = '$parametros->ordenN'";


        $sqlResult = mysql_query($sql) or die(mysql_error());

        if (mysql_num_rows($sqlResult) > 0) {

            $row = mysql_fetch_array($sqlResult);

            $sql = "UPDATE $this->modulo
                    SET `orden` = '" . $parametros->ordenA . "'
                    WHERE `id` = '" . $row["id"] . "'";

            $result = mysql_query($sql) or die(mysql_error());
        }

        $sql = "UPDATE $this->modulo
                SET `orden` = '" . $parametros->ordenN . "'
                WHERE `id` = '$parametros->id_item'";

        $result = mysql_query($sql) or die(mysql_error());

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

        $result = mysql_query($sql) or die("Error: " . mysql_error());
        $item = mysql_fetch_object($result);
        return $item;
    }

    function datos_lista($tabla = '') {
        
        if ($tabla === '')
            $tabla = $this->modulo;
        $sql = "SELECT *
                FROM $tabla
                WHERE estado = 'A'
                ";
        $result = mysql_query($sql) or die("Error: " . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $datos[] = $row;
        }
        return $datos;
    }

}

?>