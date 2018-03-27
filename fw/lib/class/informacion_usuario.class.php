<?php

class InformacionUsuario {

    CONST USUARIO_NO_VALIDO = "007";
    CONST ESTADO_ACTIVO=1;
    CONST ESTADO_BLOQUEADO=2;
    CONST SUSPENDIDO=3;

    private $db;

    private $sx;

    private $ruta_cron='';



    function __construct($db,$ruta_cron='') {

        $this->ruta_cron = $ruta_cron;



        require_once $this->ruta_cron.'fw/lib/class/seguridad.class.php';

        $this->sx = new Seguridad($db);    

        $this->db = $db;

    }

    function traer_owner($id_distribuidor, $tabla = "fw_interlocutor") {

        $campos = array();

        $owner = "ass";



        $result = $this->db->selectOne(array('interlocutor_id'),$tabla,"id_interlocutor = '$id_distribuidor' ");

        if(isset($result)){

            $owner = $result['interlocutor_id'];

        } 
        return $owner;
    }

    function traer_datos_interlocutor($campos = array('interlocutor_id','id_interlocutor'),$condicion = "") {

        $interlocutor = $this->db->selectOne($campos,'fw_interlocutor',$condicion);

        return $interlocutor;

    }
    function traer_datos_usuario($campos = array('interlocutor_id','id_usuario'),$condicion){

        $usuario = $this->db->selectOne($campos,'fw_usuario',$condicion);



        return $usuario;

    }



    function actualizar_datos($campos,$tabla,$condicion){

        $resultado = $this->db->update($campos,$tabla,$condicion);

        return $resultado;

    }

    

     function validar_usuario($usuario,$clave){
        $clave_real = $this->sx->encriptar_clave($clave);
        $datos_usuario = $this->traer_datos_usuario(array('interlocutor_id,id_usuario'),"nickname = '$usuario' AND clave='$clave_real' AND estado_id='".self::ESTADO_ACTIVO."' ");
        $interlocutor = $this->db->selectOne(array('id_interlocutor'),'fw_interlocutor',"estado_id ='".self::ESTADO_ACTIVO."' AND id_interlocutor='".$datos_usuario['interlocutor_id']."'");

        if(!isset($datos_usuario) || empty($datos_usuario)){
            return self::USUARIO_NO_VALIDO;
        }
        
        if(!isset($interlocutor['id_interlocutor']) || empty($interlocutor['id_interlocutor'])){
             return self::ESTADO_BLOQUEADO;
        }
        

        return $datos_usuario['id_usuario'];

    }





    /*Nuevas Funciones 2014-10-10*/



    function traer_id_usuario($usuario){



        $datos_usuario = $this->traer_datos_usuario(array('interlocutor_id,id_usuario','estado_id'),"nickname = '$usuario'");

        

        if(!isset($datos_usuario) || empty($datos_usuario)){

            return false;

        }else{

            return $datos_usuario;

        }

        

    }



     function bloquear_usuario($id_usuario){

        

        if ($this->db->update(array('estado_id'=>self::ESTADO_BLOQUEADO),'fw_usuario',"id_usuario='".$id_usuario."'")) {

            return true;

        }else{

            return false;

        }

    }


    function traer_marcablanca($id_interlocutor){

        $marca_blanca = "";

        $result = $this->db->selectOne(array('marca_blanca'),'fw_interlocutor_condicion',"interlocutor_id = '$id_interlocutor'");
        
        if($result){
            $marca_blanca = $result['marca_blanca'];

            if ($marca_blanca == $id_interlocutor) {
                $result = $this->db->selectOne(array('id_interlocutor'),'fw_interlocutor',"id_interlocutor = '$id_interlocutor'");//realice cambio ante un error presentado cambie interlocutor_id por id_interlocutor
                $marca_blanca = $result['id_interlocutor'];
                
            }
        }
        
        return $marca_blanca;
    }

}