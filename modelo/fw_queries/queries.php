<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of queries
 *
 * @author Soldier
 */
class Queries {
	/* @var WISMysqli */
	public $dataBase = null;
	
	function __construct(WISMysqli $dataBase) {
		$this->dataBase = $dataBase;
	}
	
	function consultaModuloxNombre(array $parametros){
		$campos= array(
			'op.id_opcion',
			'op.descripcion',
			'op.nombre_modulo', 
			'op.opcion_tipo_id',
			'op.base','opt.nombre AS tipo',
			'p.nombre AS pack'
			);
			$tablas="fw_opcion op, fw_opcion_tipo opt, fw_pack p";
			$condicion = " op.opcion_tipo_id = opt.id_opcion_tipo "
				. " AND op.pack_id = p.id_pack "
				. " AND op.nombre_modulo = '".$parametros['nombre_modulo']."' "
				. " AND op.estado_id = ".ESTADO_ACTIVO;
		
		$result = $this->dataBase->selectOne($campos, $tablas, $condicion, false, false);
		return $result;
	}
	function consultaInterlocutorxIDUsuario(array $parametros){
		$campos = array(
			"UPPER(CONCAT(u.nombre,' ',u.apellido)) as nombre_usuario",
			"UPPER(p.nombre) as perfil",
			"i.interlocutor_clase_id",
			"UPPER(CONCAT(i.nombre,' ',i.apellido)) as nombre_interlocutor",
			'ic.imagen_perfil',
			'ic.titulo_sitio',
			'ic.imagen_logo',
			't.descripcion as tema',
			'ic.marca_blanca');
			$tablas="fw_interlocutor i,fw_interlocutor_condicion ic, fw_tema t, fw_usuario u, fw_usuario_perfil p";
			$condicion= " t.id_tema = ic.tema_id "
					. " AND i.id_interlocutor = ic.interlocutor_id "
					. " AND u.interlocutor_id = i.id_interlocutor "
					. " AND u.usuario_perfil_id = p.id_usuario_perfil "
					. " AND ic.interlocutor_id ='".$parametros['interlocutor']."' "
					. " AND u.id_usuario=".$parametros['usuario'];
			
		return $this->dataBase->selectOne($campos, $tablas, $condicion, false, false);
	}
        function consultaOpcionesUsuario(array $parametros){
		$campos = array(
                    's.id_opcion',
                    's.descripcion',
                    's.nombre_modulo',
                    's.imagen',
                    'i.icon'
                    ); 
                $tablas = 'fw_opcion s, fw_opcion_clase op, fw_icons i'; 
                $condiciones = "s.opcion_id = ".$parametros['id'] 
                . " AND s.estado_id = " . ESTADO_ACTIVO . " "
                . " AND op.estado_id = " . ESTADO_ACTIVO . " "
                . " AND s.id_opcion = op.opcion_id "
                . " AND s.icon_id = i.id_icon "
                . " AND op.interlocutor_clase_id = ".$parametros['clase'] 
                . " AND op.usuario_perfil_id=".$parametros['perfil'] 
                . " AND op.tipo_negocio_id=".$parametros['tipo_negocio'];
                $order = 'orden';
        
			
		return $this->dataBase->select($campos, $tablas, $condiciones, $order, false);
	}
        function consultaOpcionesFW(array $parametros){
		$campos = array(
                    's.id_opcion',
                    's.descripcion',
                    's.nombre_modulo',
                    's.imagen',
                    'i.icon'
                    ); 
                $tablas = 'fw_opcion s, fw_icons i'; 
                $condiciones = "s.opcion_id = ".$parametros['id_opcion']
                . " AND s.estado_id = " . ESTADO_ACTIVO . " "
                . " AND s.icon_id = i.id_icon ";
                $order = 'orden';
			
		return $this->dataBase->select($campos, $tablas, $condiciones, $order, false);
	}
}
