/**
 * Grid Control 
 * 
 * LICENSE: 
 *
 * @author     Herman Adrian Torres
 * @copyright  2014
 * @version    v0.4 01/08/2014    
 */
function cambiar_pagina($pagina, $modulo, accion){
    if((accion != null) && (accion != '')){
        accion = "&a="+accion; 
    }else{
        accion = '';
    }
    peticion_ajax('?opcion='+$modulo+'&page='+$pagina+accion, '', 'grid_contenedor');
}

function mover_pagina($direccion, $pagina, $total_paginas, $modulo){
    if($direccion === 'IZQ'){
        if(parseInt($pagina) > 1){
            $pagina = parseInt($pagina) - 1;
        }
    }else{
        if(parseInt($pagina) < $total_paginas){
            $pagina = parseInt($pagina) + 1;
        }
    }
    peticion_ajax('?opcion='+$modulo+'&page='+$pagina+'&type=ajax', '', 'grid_contenedor');
}

function ir_a($url){
    peticion_ajax($url,'', 'grid_contenedor');
}
