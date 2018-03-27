<?php
class stdStaticModule {
    const TEXT = '0';
    const CONTACTO = '1';

    var $contentType = "";
    var $modulo = "";
    var $clase = "";

    function getContenido($a) {
        $this->contentType = "";
        switch ($a) {
            case "submenu":
                $this->submenu();
                break;
            default:
                $this->contentType = "html";
                $this->submenu();
        }
    }

    function getClassName($file) {
        $file = explode('\\', $file);
        $file = $file[(count($file) - 1)];
        $file = substr($file, 0, -4);
        return $file;
    }

    function submenu() {
        include_once 'php/funciones.php';
        $fx = New Funciones();
        $dato = new stdClass();

        $sql = "SELECT *
                FROM contenido 
                WHERE nombre = '$this->modulo'
                AND estado = 'A'";

        $result = mysql_query($sql) or die("Error: " . mysql_error() . " " . __LINE__ . ", " . __FUNCTION__);

        $dato = mysql_fetch_object($result);
        if(empty($dato)){
            $dato->descripcion = "El contenido de esta seccion est&aacute; en construcci&oacute;n";
        }
        include("php/html/stdStaticModule.html.php");
        ClaseHTML::submenu($this->clase, $dato);
    }
}
?>