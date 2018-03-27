<?php

/**

 * Grid Control

 * 

 * LICENSE: 

 *

 * @author     Herman Adrian Torres

 * @copyright  2014

 * @version    v0.4 01/08/2014    

 */

require_once 'fw/lib/plugin/_inc/grid/grid_config.class.php';

class Grid extends GridConfig{

    public $pagina = 1;

    public $cantidad = 0;

    public $configuracion = 0;

    public $tipo = array();

    public $modulo = '';

    public $accion = 'formulario_edicion';



    public function __construct(){
		require_once 'fw/lib/plugin/_inc/grid/grid.html.php';
    }

    

    public function crear_tabla($datos, $pagina, $modulo){

        $campos = array();

        $this->pagina = $pagina;

        $this->cantidad = count($datos);

        $this->modulo = $modulo;

        

        if(!empty($datos[0])){

            foreach ($datos[0] as $key => $value) {

                $campos[] = $key;                       //Obtengo los campos de la consulta

            }

        }



        $datos_pie = $this->configurar_foot($campos);

        $datos_pagina = $this->traer_datos_pagina($datos, $datos_pie['conteo']->primero, $datos_pie['conteo']->ultimo);

        $this->pintar_tabla($campos, $datos_pagina, $datos_pie);

    }

    private function pintar_tabla($campos, $datos, $datos_pie){

        

        GridHTML::pintar_tabla($campos, $datos, $datos_pie, $this->configuracion, $this->modulo,$this->accion);

    }

    

    private function traer_datos_pagina($datos, $primero, $ultimo){

        $datos_pagina = array();

        for($i = ($primero-1); $i < $ultimo; $i++){

            $datos_pagina[$i]=$datos[$i]; 

        }

        return $datos_pagina;

    }

    function configurar_foot($campos){

        $datos_pie = array();



        $datos_pie['general'] = new stdClass();

        $datos_pie['conteo'] = new stdClass();



        $datos_pie['general']->campos = count($campos);

        $datos_pie['general']->pagina = $this->pagina;

        $datos_pie['general']->total_paginas = ceil($this->cantidad/self::ITEMS_X_PAGINA);

        $datos_pie['general']->datos_x_pagina = self::ITEMS_X_PAGINA;

        $datos_pie['conteo']->total = $this->cantidad;

        $datos_pie['conteo']->ultimo = $this->pagina * self::ITEMS_X_PAGINA;

        if($datos_pie['conteo']->ultimo > $datos_pie['conteo']->total){

            $datos_pie['conteo']->ultimo = $datos_pie['conteo']->total;

        }

        $datos_pie['conteo']->primero = ((($this->pagina-1) * self::ITEMS_X_PAGINA)+1);

        return $datos_pie;

    }

    public function configuracion($confs){

        $this->configuracion = $confs;

    }

}

?>