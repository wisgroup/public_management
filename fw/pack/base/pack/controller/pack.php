<?php
class Pack extends Maestro{

	public function __construct($db = "", $modulo = "", $ruta_cron = "", $modulo_info = null){
		parent::__construct($db, $modulo, $ruta_cron, $modulo_info);
		$this->modulo = $modulo;
		$this->nombre_tabla = $modulo_info->base ."_". $this->modulo;
		$this->primary_key = 'id_'.$this->modulo;
		$this->actualizacion_ajax='ajax';

		$campos_formulario[$this->primary_key] = array('tipo' => 'hidden', 'valor' => '', 'complemento' => 'required');
		$campos_formulario['nombre'] = array('tipo' => 'text', 'valor' => '', 'complemento' => "required");
		$campos_formulario['descripcion'] = array('tipo' => 'text', 'valor' => '', 'complemento' => "required");
		$campos_formulario['base'] = array(
			'tipo' => 'select',
			'valor' => '', 'complemento' => 'required',
			'opciones'=> array('fw', 'app'),
			'tag'=>'select');
		$campos_formulario['estado_id'] = array(
			'tipo' => 'select',
			'valor' => '', 'complemento' => 'required',
			'tabla' => 'fw_estado',
			'campo' => 'descripcion',
			'condicion' => 'id_estado IN (1,2)',
			'tag'=>'select');
		
		$this->campos_formulario = $campos_formulario;
	}

	function home(){

		$campos = array('m.'.$this->primary_key.' as Codigo', 'm.nombre', 'm.descripcion', 'est.descripcion AS estado');
		$tablas = $this->nombre_tabla . " m, fw_estado est";
		$condicion = 	"m.estado_id=est.id_estado 
						AND m.estado_id <>" . self::ESTADO_ELIMINADO;
						
		$this->iniciar_maestro($campos, $tablas, $condicion, $this->modulo . date('Y-m-d H.i.s'));
	}
}
