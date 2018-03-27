<?php

//require_once('fw/controlador/php/modulos/maestro.class.php');

class UsuarioCambioClave extends Maestro {

    CONST ERROR_CLAVE_NO_COINCIDE = "016";
    CONST EXITO_CAMBIO_CLAVE = "017";
    CONST TABLA_MODULO = "fw_usuario";

    function home() {
        $this->tipo_contenido = 'ajax';

        $usuario = $this->gn->get_data_loged('id_usuario');
        $id_interlocutor = $this->gn->get_data_loged('id_interlocutor');

        $datos_usuario = $this->db->selectone(array('CONCAT(u.nombre," ",u.apellido) usuario', 'u.id_usuario'), ' fw_usuario u', "u.id_usuario =" . $usuario);
        require_once ("fw/pack/base/usuario_cambio_clave/view/usuario_cambio_clave.html.php");
        UsuarioCambioClaveHTML::home($datos_usuario, $id_interlocutor);
    }

    function guardar_clave() {
        $this->tipo_contenido = '';
        $parametros = $this->gn->traer_parametros('POST');

        $usuario = $this->gn->get_data_loged('id_usuario');
        $clave_usuario = $this->db->selectone(array('clave', 'nombre', 'apellido', 'email', 'nickname'), ' fw_usuario ', "id_usuario =" . $usuario);
        $clave_actual_encriptada = $this->sx->encriptar_clave($parametros->clave_actual);

        if ($clave_actual_encriptada == $clave_usuario['clave']) {

            $clave_nueva_encriptada = $this->sx->encriptar_clave($parametros->password);

            $campos_usuario = new stdClass();
            $campos_usuario->clave = $clave_nueva_encriptada;
            $this->primary_key='id_usuario';
            $repuesta = $this->editar_elemento($campos_usuario, $usuario, self::TABLA_MODULO);

            if (!$repuesta) {
                $mensaje = $this->nc->traer_mensaje_respuesta($this->ERROR_EDICION);
                $this->nc->set_notificacion($mensaje['descripcion']);
                header('location:?opcion=subopcion&id=1');
                exit();
            }
            //$envio = $this->gn->mailGeneral($clave_usuario['email'], $clave_usuario['nombre'], 'cambio_clave', "MOSS: Cambio de clave.", array('nombre' => $clave_usuario['nombre'] . ' ' . $clave_usuario['apellido'], 'password' => $parametros->password, 'usuario' => $clave_usuario['nickname'], 'date' => date("Y-m-d H:i:s")));
            $mensaje = $this->nc->traer_mensaje_respuesta(self::EXITO_CAMBIO_CLAVE);
            $this->nc->set_notificacion($mensaje['descripcion']);
            header('location:?opcion=subopcion&id=1');
        } else {
            $mensaje = $this->nc->traer_mensaje_respuesta(self::ERROR_CLAVE_NO_COINCIDE);
            $this->nc->set_notificacion($mensaje['descripcion']);
            header('location:?opcion=subopcion&id=1');
            exit();
        }
    }

   function terminos_condiciones() {
        $this->tipo_contenido = "ajax";
        require_once("fw/vista/html/usuario_cambio_clave.html.php");
        UsuarioCambioClaveHTML::terminos_condiciones();
    }
}
