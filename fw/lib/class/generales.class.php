<?php

class Generales {

    private $db;
    private $ruta_cron = '';
    private $session;
    private $config;

    CONST GENERALES = 'generales';
    CONST CONFIGURACION = 'configuracion';

    function __construct($db, $ruta_cron) {
        $this->db = $db;
        $this->ruta_cron = $ruta_cron;
        require_once $this->ruta_cron . PATH_APP_CONFIG;
        $this->config = new App_Config();

        require_once('modelo/mysqli.class.php');
        $this->db = new WISMysqli($this->config->_get('host'), $this->config->_get('usuario'), $this->config->_get('clave'), $this->config->_get('base_datos'), true);
        $this->session = $this->config->_get('session');
    }

    function debug($objeto, $stop = 0) {
        echo "<pre>";
        print_r($objeto);
        if ($stop === 1) {
            exit();
        }
    }

    function get_data_loged($data, $tipo = self::CONFIGURACION) {
        if (isset($_SESSION[$this->session][$tipo][$data])) {
            return $_SESSION[$this->session][$tipo][$data];
        } else {
            return 0;
        }
    }

    function set_data_loged($data, $valor, $tipo = self::CONFIGURACION) {
        $_SESSION[$this->session][$tipo][$data] = $valor;
        if ($valor == NULL) {
            unset($_SESSION[$this->session][$tipo][$data]);
        }
    }

    function set_alerta($nombre_copie, $complemento = "") {
        require_once 'fw/controlador/php/lib/copies.php';
        if ($nombre_copie != "") {
            $valor = $copies->$nombre_copie;
            $valor .= $complemento;
        } else {
            $valor = $complemento;
        }
        $_SESSION['msj_alerta'] = $valor;
    }

    function traer_parametros($metodo = "POST", $type = "Obj") {
        if ($type == "array") {
            $parametros = array();
        } else {
            $parametros = New stdClass();
        }
        if ($metodo == "GET") {
            foreach ($_GET as $k => $v) {
                $nombre_variable = $this->db->escapeString($k);
                $valor_variable = utf8_decode($this->db->escapeString($v));
                $$nombre_variable = $valor_variable;
                if ($type == "array") {
                    $parametros[$nombre_variable] = $valor_variable;
                } else {
                    $parametros->$nombre_variable = $valor_variable;
                }
            }
        } else {
            foreach ($_POST as $k => $v) {
                $nombre_variable = $this->db->escapeString($k);
                $valor_variable = utf8_decode($this->db->escapeString($v));
                $$nombre_variable = $valor_variable;
                if ($type == "array") {
                    $parametros[$nombre_variable] = $valor_variable;
                } else {
                    $parametros->$nombre_variable = $valor_variable;
                }
            }
        }
        return $parametros;
    }

    function validar_sesion() {

        $id_usuario = $this->get_data_loged('id_usuario');
        if (!isset($id_usuario) || $id_usuario == "" || $id_usuario == 0) {
            return false;
        } else {
            return true;
        }
    }

    //modificacion jhe guardar imagen
    function generar_imagenes($path, $_width, $_height, $imagen, $fixed = false, $id_usuario = null) {
        $response = new stdClass();
        $response->respuesta = false;
        $response->descripcion = "Error intentando subir archivo";


        $archivo = $imagen['tmp_name'];
        $image_info = getimagesize($archivo);

        $image_type = $image_info[2];

        $ext = explode('.', $imagen['name']);
        $ext = array_pop($ext);
        if ($id_usuario != null) {
            $filename = (string) $id_usuario . "." . "jpg";
        } else {
            $filename = uniqid(time()) . "." . $ext;
        }
        if ($path == "" && $_width == "" && $_height == "") {
            $response->respuesta = false;
            $response->descripcion = "Parametros incorrectos";
            return $response;
        }

        $this->divThumb = 5;
        $this->newWidth = $_width;
        $this->newHeight = $_height;

        if (!file_exists($path)) {
            mkdir($path, 0777);
            mkdir($path . "big/", 0777);
            mkdir($path . "thumbs/", 0777);
        }

        list($width, $height) = getimagesize($archivo);

        if ($image_type == IMAGETYPE_JPEG) {
            $original = imagecreatefromjpeg($archivo);
        } else if ($image_type == IMAGETYPE_GIF) {
            $original = imagecreatefromgif($archivo);
        } else if ($image_type == IMAGETYPE_PNG) {
            $original = imagecreatefrompng($archivo);
        }

        if ($fixed) {

            $this->newWidth = $_width;
            $this->newHeight = $_height;
        } else {

            if ($width >= $height) {
                if ($width > $this->newWidth)
                    $this->newHeight = ($height / $width) * $this->newWidth;
                else {
                    $this->newWidth = $width;
                    $this->newHeight = $height;
                }
            } else {
                if ($height > $this->newHeight)
                    $this->newWidth = ($width / $height) * $this->newHeight;
                else {
                    $this->newWidth = $width;

                    $this->newHeight = $height;
                }
            }
        }


        $big = imagecreatetruecolor($this->newWidth, $this->newHeight);
        $thumb = imagecreatetruecolor(($this->newWidth / $this->divThumb), ($this->newHeight / $this->divThumb));


        $blanco = imagecolorallocatealpha($big, 233, 235, 234, 0);

        imagefill($big, 0, 0, $blanco);
        imagefill($thumb, 0, 0, $blanco);

        imagecopyresampled($big, $original, 0, 0, 0, 0, $this->newWidth, $this->newHeight, $width, $height);
        imagecopyresampled($thumb, $original, 0, 0, 0, 0, ($this->newWidth / $this->divThumb), ($this->newHeight / $this->divThumb), $width, $height);
        imagejpeg($big, $path . "big/" . $filename, 80);
        imagejpeg($thumb, $path . "thumbs/" . $filename, 80);

        imagedestroy($big);
        imagedestroy($thumb);
        imagedestroy($original);

        if (file_exists($path . "thumbs/" . $filename) && file_exists($path . "big/" . $filename)) {
            $response->respuesta = true;
            $response->descripcion = $filename;
            return $response;
        } else {
            $response->respuesta = false;
            $response->descripcion = "Error al crear imagen";
            return $response;
        }

        return $response;
    }

//correo soporte
// function mailGeneral($mail, $nombre, $archivo_mail, $tokens) {
//         require_once("controlador/php/lib/mailer/Mailer.class.php");
//         $datos_mail = array("mailTo" => $mail, "nombreTo" => $nombre);
//         $envio = Mailer::mailingGeneral($archivo_mail, $datos_mail, $tokens);
//         if ($envio === true){
//             return true;
//         }else{
//             return $envio;
//         }
//     }

    function mailGeneral($mail, $nombre, $archivo_mail, $subject, $tokens) {
        require_once("fw/controlador/php/lib/mailer/Mailer.class.php");
        $datos_mail = array("mailTo" => $mail, "nombreTo" => $nombre);
        $envio = Mailer::mailingGeneral($archivo_mail, $datos_mail, $subject, $tokens);

        if ($envio === true) {
            return true;
        } else {
            return $envio;
        }
    }

    function subirArchivo($seccion = "", $categoria = "", $archivo = "", $uniqid = false, $tipos = '') {
        $response = new stdClass();
        $path_upload = "media/" . $seccion . "/" . $categoria . "/";

        if (!file_exists($path_upload)) {
            mkdir($path_upload, 0755);
        }

        if ($uniqid) {
            $ext = explode('.', $archivo['name']);
            $ext = array_pop($ext);
            $filename = uniqid(time()) . "." . $ext;
        } else {
            $filename = $archivo["name"];
        }

        if ($tipos != '') {
            $extensiones = explode(',', $tipos);

            if (!in_array($ext, $extensiones)) {
                $response->respuesta = 'ERROR';
                $response->descripcion = "El tipo de archivo no es permitido.";
                return $response;
            }
        }

        try {
            move_uploaded_file($archivo["tmp_name"], $path_upload . $filename);
            chmod($path_upload . "/", 0777);
        } catch (Exception $e) {
            $response->respuesta = 'ERROR';
            $response->error = $e;
            return $response;
        }

        echo $path_upload . $filename;

        if (file_exists($path_upload . $filename)) {
            $response->respuesta = 'OK';
            $response->filename = $filename;
            $response->label = $archivo['name'];
            return $response;
        } else {
            $response->respuesta = 'ERROR';
            $response->error = "Error intentando subir archivo ";
            return $response;
        }
    }

    function subir_archivo($seccion = "", $archivo = "", $uniqid = false, $bytes = 0, $tipos = '') {
        $response = new stdClass();
        $path_upload = "media/" . $seccion . "/";

        if ($bytes > 0) {
            if ($_FILES[$archivo]['size'] > $bytes) {
                $kilobyte = 1024;
                $peso_maxio = $bytes / $kilobyte;
                $response->respuesta = false;
                $response->descripcion = "El peso del archivo debe ser inferior a " . $peso_maxio . " KB";
                return $response;
            }
        }

        if ($uniqid) {
            $ext = explode('.', $_FILES[$archivo]['name']);
            $ext = array_pop($ext);
            $nombre_archivo = uniqid(time()) . "." . $ext;
        } else {
            $nombre_archivo = date("Y-m-d H:i:s") . " " . $_FILES[$archivo]['name'];
        }

        if ($tipos != '') {
            $extensiones = explode(',', $tipos);
            if (!in_array($ext, $extensiones)) {
                $response->respuesta = false;
                $response->descripcion = "El tipo de archivo no es permitido.";
                return $response;
            }
        }

        if (!file_exists($path_upload)) {
            mkdir($path_upload, 0777);
        }

        try {
            move_uploaded_file($_FILES[$archivo]["tmp_name"], $path_upload . $nombre_archivo);
        } catch (Exception $e) {
            $response->respuesta = false;
            $response->descripcion = $e;
            return $response;
        }

        if (file_exists($path_upload . "/" . $nombre_archivo)) {
            $response->respuesta = true;
            $response->descripcion = $nombre_archivo;
            return $response;
        } else {
            $response->respuesta = false;
            $response->descripcion = "Error intentando subir archivo ";
            return $response;
        }
    }

    function formato_pesos_valor($valor, $alias = '') {
        $alias_tmp = ($alias == '') ? ' ' : ' as ' . $alias;
        // $alias_tmp = ' as '.$alias;
        return "concat('$ ', replace(replace(replace(format(" . $valor . "),'.','_'),',','.'),'_',',')) " . $alias_tmp;
    }

    function formato_valor($valor, $alias = '') {
        $alias_tmp = ($alias == '') ? ' ' : ' as ' . $alias;
        // $alias_tmp = ' as '.$alias;
        return "replace(replace(replace(format(" . $valor . "),'.','_'),',','.'),'_',',') " . $alias_tmp;
    }

    function cargar_archivo($ruta = "", $archivo = "", $uniqid = false, $bytes = 0, $tipos = '', $name = '') {
        $response = new stdClass();
        $path_upload = $ruta;

        if ($bytes > 0) {
            if ($_FILES[$archivo]['size'] > $bytes) {
                $kilobyte = 1024;
                $peso_maxio = $bytes / $kilobyte;
                $response->respuesta = false;
                $response->descripcion = "El peso del archivo debe ser inferior a " . $peso_maxio . " KB";
                return $response;
            }
        }

        if ($uniqid) {
            $ext = explode('.', $_FILES[$archivo]['name']);
            $ext = array_pop($ext);
            $nombre_archivo = uniqid(time()) . "." . $ext;
        } else {
            $nombre_archivo = date("Y-m-d H:i:s") . " " . $_FILES[$archivo]['name'];
        }

        if ($name != '') {
            $ext = explode('.', $_FILES[$archivo]['name']);
            $ext = array_pop($ext);
            $nombre_archivo = $name . "." . $ext;
        }

        if ($tipos != '') {
            $extensiones = explode(',', $tipos);

            if (!in_array($ext, $extensiones)) {
                $response->respuesta = false;
                $response->descripcion = "El tipo de archivo no es permitido.";
                return $response;
            }
        }

        if (!file_exists($path_upload)) {
            mkdir($path_upload, 0777);
        }
        try {
            move_uploaded_file($_FILES[$archivo]["tmp_name"], $path_upload . $nombre_archivo);
        } catch (Exception $e) {
            $response->respuesta = false;
            $response->descripcion = $e;
            return $response;
        }

        if (file_exists($path_upload . $nombre_archivo)) {
            $response->respuesta = true;
            $response->descripcion = $nombre_archivo;
            return $response;
        } else {
            $response->respuesta = false;
            $response->descripcion = "Error intentando subir archivo: " . $path_upload . $nombre_archivo;
            return $response;
        }
    }

    function cargar_archivo_mysql($nombre_archivo) {
        $archivo_blob = addslashes(file_get_contents($_POST[$nombre_archivo]['tmp']));
    }

    function getRealIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];

        return $_SERVER['REMOTE_ADDR'];
    }

    function crearCampoEvento($evento, $funcion, $campo, $parametros) {
        $strParams = "";
        foreach ($parametros as $valor) {
            $strParams .= ", " . $valor . "";
        }
        //$strParams = substr($strParams, 0, -2);

        $item = "<a " . $evento . "=\"" . $funcion . "(";
        $item2 = "' " . $strParams . ")\">'";
        $item3 = "</a>";
        $campo_funcion = "CONCAT(" . $item . ", '" . $item2 . "', " . $campo . ", " . $item3 . ") ";
        //echo $campo_funcion;exit;
        return $campo_funcion;
    }

    function getPriorityColor($hora) {
        switch (strtotime($hora)) {
            case (""):
                return PRIORIDAD_ALTA;
            case (strtotime($hora) <= strtotime(PRIORIDAD_LIMITE_BAJA)):
                return PRIORIDAD_BAJA;
            case (strtotime($hora) > strtotime(PRIORIDAD_LIMITE_BAJA) && strtotime($hora) < strtotime(PRIORIDAD_LIMITE_MEDIA)):
                return PRIORIDAD_MEDIA;
            case (strtotime($hora) > strtotime(PRIORIDAD_LIMITE_MEDIA)):
                return PRIORIDAD_ALTA;
            default:
                return PRIORIDAD_ALTA;
        }
    }

    function traer_datos_ventas($fecha) {
        $interlocutor_id_actual = $this->get_data_loged('id_interlocutor'); //id del Interlocutor actual
        $tablas = 'transaccion t';
        
        $campos = array(
            "DATE_FORMAT(t.fecha,'%Y-%m-%d') AS fecha", 
            'FORMAT(COUNT(t.total), 0) AS cantidad',
            'SUM(t.total) AS ventas',
            'SUM(t.servicio) AS servicio'
        );
        
        $group_by = ' GROUP BY 1  DESC';
        $condicion = "t.estado_id <> " . ESTADO_ELIMINADO
				. " AND t.interlocutor_id =  ".$interlocutor_id_actual
                . " AND t.fecha > DATE_FORMAT('".$fecha."','%Y-%m-%d %H:%i:%s') " 
                . " AND t.transaccion_tipo_id=" . TRX_VENTA. $group_by;
        
        $result = $this->db->select($campos, $tablas, $condicion, false);
        return $result[0];
    }

}
