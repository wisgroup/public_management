<?php

class Inicio extends Modulo {

    CONST USUARIO_NO_VALIDO = "007";
    CONST EXCEDE_INTENTOS = "009";
    CONST USUARIO_BLOQUEADO = "008";
    CONST NUMERO_INTENTOS_LOGIN = 3;
    CONST ESTADO_BLOQUEADO = 2;
    CONST CLASE_ADMINISTRADOR = 1;
    CONST CLASE_PUNTOVENTA = 6;
    CONST PERFIL_ADMINISTRADOR = 1;
    CONST PERFIL_OPERATIVO = 2;

    public $tipo_contenido = "html";

    function home() {
        $usuario = $this->gn->get_data_loged('id_usuario');

        require_once($this->get_view_path());
        if (!isset($usuario) || empty($usuario) || $usuario == 0) {
            InicioHTML::bienvenido();
        } else {
            header('location:?opcion=subopcion&id=' . NAV_OPCION_DEFAULT);
        }
    }

    function procesar_login() {
        $respuesta = "";
        $parametros = $this->gn->traer_parametros('POST');
        $intentos = $this->gn->get_data_loged($parametros->usuario, 'generales');
        if (!isset($intentos)) {
            $intentos = 0;
            $this->gn->set_data_loged($parametros->usuario, $intentos, 'generales');
        }
        $id_usuario_valido = $this->iu->validar_usuario($parametros->usuario, $parametros->password);
        if ($id_usuario_valido === self::USUARIO_NO_VALIDO || $id_usuario_valido === self::ESTADO_BLOQUEADO) {
            $intentos = $intentos + 1;
            $this->gn->set_data_loged($parametros->usuario, $intentos, 'generales');
            $id_usuario = $this->iu->traer_id_usuario($parametros->usuario);

            if ($id_usuario) {

                if ($id_usuario['estado_id'] == self::ESTADO_BLOQUEADO){
                    $mensaje = $this->nc->traer_mensaje_respuesta(self::USUARIO_BLOQUEADO);
                    $this->nc->set_notificacion($mensaje['descripcion']);
                    $this->gn->set_data_loged($parametros->usuario, 0, 'generales');
                    $this->registrar_log(self::LOG_INGRESO_FALLIDO, $parametros->usuario, self::USUARIO_BLOQUEADO . ' ' . $parametros->password);
                    header("location:?opcion=inicio");
                    exit();
                }
                if ($intentos >= self::NUMERO_INTENTOS_LOGIN) {

                    $this->registrar_log(self::LOG_INGRESO_FALLIDO, $parametros->usuario, $parametros->password);
                    $this->iu->bloquear_usuario($id_usuario['id_usuario']);
                    $mensaje = $this->nc->traer_mensaje_respuesta(self::EXCEDE_INTENTOS);

                    $this->nc->set_notificacion($mensaje['descripcion']);
                    // $this->gn->set_data_loged($parametros->usuario,0); 
                    header("location:?opcion=inicio");

                    exit();
                }
            }


            $this->registrar_log(self::LOG_INGRESO_FALLIDO, $parametros->usuario, $parametros->password);
            $mensaje = $this->nc->traer_mensaje_respuesta(self::USUARIO_NO_VALIDO);

            $this->nc->set_notificacion($mensaje['descripcion']);

            header("location:?opcion=inicio");

            exit();
        } else {
            $this->iniciar_sesion($id_usuario_valido);
            $this->registrar_log(self::LOG_INGRESO, $parametros->usuario);
        }
    }

    function iniciar_sesion($id_usuario) {
        $usuario = $this->iu->traer_datos_usuario(array('nickname','interlocutor_id', "CONCAT(nombre,' ',apellido) AS nombre_usuario", 'usuario_perfil_id'), "id_usuario = '$id_usuario'");
        $interlocutor = $this->iu->traer_datos_interlocutor(array('interlocutor_clase_id', 'interlocutor_tipo_negocio_id'), "id_interlocutor = " . $usuario['interlocutor_id']);

        $fecha_ingreso = date("Y/m/d");
        $hora_ingreso = date("H:i:s");

        $this->gn->set_data_loged('hora_ingreso', $hora_ingreso);
        $this->gn->set_data_loged('fecha_ingreso', $fecha_ingreso);

        $this->gn->set_data_loged('id_usuario', $id_usuario);
        $this->gn->set_data_loged('id_interlocutor', $usuario['interlocutor_id']);
        $this->gn->set_data_loged('clase', $interlocutor['interlocutor_clase_id']);
        $this->gn->set_data_loged('tipo_negocio', $interlocutor['interlocutor_tipo_negocio_id']);
        $this->gn->set_data_loged('nombre_usuario', $usuario['nombre_usuario']);
		$this->gn->set_data_loged('nickname', $usuario['nickname']);
        $this->gn->set_data_loged('perfil', $usuario['usuario_perfil_id']);
        
        $marca_blanca = $this->iu->traer_marcablanca($usuario['interlocutor_id']); //problem marca blanca
        
        $this->gn->set_data_loged('marca_blanca', $marca_blanca); //revisar lo de marca blanca 
        header("location:?opcion=inicio");
    }

    function cerrar_sesion() {
        session_destroy();
		$this->db->close();
        //mysqli_close();
        header("location:?opcion=inicio");
    }

    function traer_detalle() {
        $this->tipo_contenido = 'ajax';
        $parametros = $this->gn->traer_parametros('GET');
        $origen = $this->db->selectOne(array('c.nombre, c.grupo_id, g.origen_contactos, g.archivo'), 'campania c, grupo g', 'g.id_grupo=c.grupo_id and  c.id_campania=' . $parametros->id);
        $nombre = '';

        if ($origen['origen_contactos'] == 'FILE') {
            $group_file = file($this->ruta_cron . 'media/grupo/' . $origen['grupo_id'] . '/' . $origen['archivo']);
            $encabezado = array_shift($group_file);
            $primer_registro = array_shift($group_file);
            $fila_encabezado = explode(';', $encabezado);
            $fila_primer_registro = explode(';', $primer_registro);
            $limit = count($fila_encabezado);
            $nombre = '';

            for ($i = 0; $i < $limit; $i++) {
                $nombre = $nombre . trim($fila_encabezado[$i]) . ',' . trim($fila_primer_registro[$i]) . '|';
            }

            $nombre = trim($nombre, '|');
        } else if ($origen['origen_contactos'] == 'DB') { //'DB'
            $contacto = $this->db->selectOne(array('co.nombre,co.celular, gc.contacto_id ,c.grupo_id'), 'campania c, grupo_contacto gc, contacto co', 'gc.estado_id=' . self::ESTADO_ACTIVO . ' AND co.id_contacto = gc.contacto_id AND  gc.grupo_id=c.grupo_id AND id_campania=' . $parametros->id);
            $nombre = 'nombre,' . trim($contacto['nombre']) . '|celular,' . trim($contacto['celular']);
        }
        $datos = $this->db->selectOne(array('c.nombre as nombre_campania', 'm.nombre as mensaje', 'm.descripcion as mensaje_contenido', 'g.nombre as grupo'), 'campania c, mensaje m, grupo g', 'c.mensaje_id = m.id_mensaje AND g.id_grupo= c.grupo_id AND  id_campania=' . $parametros->id);
        $vector_contenido = explode('|', $nombre);
        $mensaje = $datos['mensaje_contenido'];

        foreach ($vector_contenido as $key => $value) {
            $cambio = explode(',', $value);
            $mensaje = preg_replace('/\[' . $cambio[0] . '\]/i', $cambio[1], $mensaje);
        }
        require_once("fw/vista/html/inicio.html.php");
        InicioHTML::detalle($datos, $mensaje);
    }

    function traer_elemento() {
        $this->tipo_contenido = 'ajax';
        $parametros = $this->gn->traer_parametros('GET');
        $datos = $this->db->selectOne(array('descripcion'), $parametros->tabla, 'id_' . $parametros->tabla . ' = ' . $parametros->id);
        echo utf8_encode($datos['descripcion']);
    }

    function calcular_unidades() {
        $this->tipo_contenido = 'ajax';
        $parametros = $this->gn->traer_parametros('GET');
        $parametros->interlocutor;
        $parametros->monto;

        $datos = $this->db->selectOne(array('precio_venta'), 'fw_interlocutor_condicion', 'interlocutor_id = ' . $parametros->interlocutor);

        $costo_mensaje = (int) $datos['precio_venta'];
        $monto = str_replace('.', '', $parametros->monto);
        $monto = str_replace(',', '.', $monto);
        $monto = (int) $monto;

        if ($costo_mensaje > $monto) {
            echo '0'; //El Valor a solicitar debe ser superior al costo por mensaje unitario
        } else if ($costo_mensaje > 0) {
            $valor = (int) ($monto / $costo_mensaje);
            echo number_format($valor, 0, '', '.');
            // echo (int) ($monto/$costo_mensaje);    
        } else {
            echo '0';  //El valor a solictar debe ser superior a 0    
        }
    }

    function validar_sms() {
        $this->tipo_contenido = 'ajax';
        $parametros = $this->gn->traer_parametros('GET');
        $parametros->sms;
        //  $ “ % ! ? & / \ ( ) < > = @ # + * _ - : ; , . 
        $patron = '/^[a-zA-Z0-9 \\\$\%\!\?\&\(\)\<\>\=\@\#\+\*\_\-\:\;\,\[\]\.\/\""]*$/';

        if (preg_match($patron, $parametros->sms)) {
            echo "0";
        } else {
            // echo "No Cumple";
            echo "Caracteres permitidos para un mensaje:<br><b>Letras:</b> a-z A-Z (sin tildes ni ñ o Ñ).<br><b>Números:</b> 0-9.<br><b>Caracteres:</b> $ “ % ! ? & / \ ( ) < > = @ # + * _ - : ; , .";
        }
    }

    function obtener_saldo() {
        $this->tipo_contenido = 'ajax';
        $interlocutor = $this->gn->get_data_loged('id_interlocutor');
        $valor = $this->gn->formato_pesos_valor('pesos,2', 'pesos');
        $saldo = $this->db->selectOne(array($valor), 'saldo', ' interlocutor_id = "' . $interlocutor . '"');

        $clase = $this->gn->get_data_loged('clase');

        if ($clase == self::CLASE_ADMINISTRADOR) {
            echo ' ';
        } else {
            echo 'Saldo: ' . $saldo['pesos'];
        }
    }

    function convert_to_csv($input_array, $output_file_name, $delimiter) {

        /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
        $f = fopen('php://memory', 'w');

        /** loop through array */
        foreach ($input_array as $line) {

            /** default php csv handler * */
            fputcsv($f, $line, $delimiter);
        }

        /** rewrind the "file" with the csv lines * */
        fseek($f, 0);

        // cerrar archivo
        fclose($f);

        /** modify header to be downloadable csv file * */
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachement; filename="' . $output_file_name . '";');

        /** Send file to browser for download */
        fpassthru($f);

        // exit();
    }

    function exportar($entries, $delimiter) {
        $fp = fopen('php://memory', 'w');

        foreach ($entries as $row) {
            fputcsv($fp, $row, $delimiter, chr(0));
        }

        // // be kind, rewind (devolver la posición del puntero del archivo)
        rewind($fp);

        // // obtener contenido del archivo como un string
        $output = stream_get_contents($fp);

        // // cerrar archivo
        fclose($fp);

        // cabeceras HTTP:
        // tipo de archivo y codificación
        header('Content-Type: text/csv; charset=utf-8');
        // forzar descarga del archivo con un nombre de archivo determinado
        header('Content-Disposition: attachment; filename=contact-' . time() . '.csv');
        // indicar tamaño del archivo
        header('Content-Length: ' . strlen($output));
        // enviar archivo
        echo $output;
        exit;
    }

    function traer_miga_pan() {
        
    }

}
