<?php

/**

 * Grid Control 

 * 

 * LICENSE: 

 *

 * @author     Herman Adrian Torres

 * @copyright  2014

 * @version    v0.4.1 21/08/2014    

 */

require_once 'fw/lib/plugin/_inc/grid/grid_config.class.php';

class GridHTML extends GridConfig{



    static function pintar_tabla($campos, $datos, $datos_pie, $configuracion, $modulo,$accion){ ?>

    <link rel="stylesheet" href="fw/controlador/php/lib/grid/grid.css" type="text/css" media="screen" />

    <script type="text/javascript" src="fw/controlador/php/lib/grid/grid.js"></script>

    <div id="grid_contenedor">

        <table id="grid_table">

            <?php 

                if(!empty($campos[0])){

                    GridHTML::pintar_cabecera($campos);

                    GridHTML::pintar_registros($datos,$configuracion, $modulo,$accion);

                    GridHTML::pintar_pie($datos_pie, $modulo, $accion);

                }else{

                    GridHTML::pintar_tabla_vacia($datos_pie, $modulo);

                }

            ?>

        </table>

        <script>

            var pag = '<?php echo $datos_pie['general']->pagina; ?>';

            

            var margin = (pag-1) * 18 * (-1); 

            if(pag > 4){

                $('#grid_paginas_contenedor').css('margin-left', margin);

            }

        </script>

    </div>

    <?php }

    



    static function pintar_cabecera($campos){ ?>

    <thead>

        <tr>

            <?php foreach ($campos as $campo) { ?>

            <th><?php 

                    $cabecera = ($campo=='empty') ? '' : $campo;  //Veririfa si el encabezado debe ser vacio

                    echo strtoupper($cabecera); ?></th>

            <?php } ?>

        </tr>

    </thead>

    <?php }





    static function pintar_registros($datos, $configuracion, $modulo,$accion){ ?>



    <?php foreach ($datos as $key => $campo) { ?>

    <tr class="linea_<?php echo (($key % 2) ? 'par' : 'impar');?>">

       <?php  foreach ($campo as $key_campo => $value) { ?> 

       <td>

        <?php

        if(isset($configuracion[$key_campo])){

            $function = $configuracion[$key_campo];

            GridHTML::$function($modulo, $value,$accion);

        }else{

            echo $value; 

        }

        ?>

    </td>

    <?php } ?>

</tr>

<?php  

} 

}







static function pintar_pie($datos, $modulo, $accion = ""){ ?>

<tfoot>

    <tr>

        <td colspan="<?php echo $datos['general']->campos; ?>">

            <div class="foot_col">

                Mostrando de <b><?php echo $datos['conteo']->primero.'</b> a <b>'.$datos['conteo']->ultimo.'</b>. Total:  <b>'.$datos['conteo']->total.'</b>' ; ?>

            </div>

            <?php if($datos['general']->total_paginas > 1){ ?>

            <div class="foot_col">

                <div id="grid_foot_paginador">

                    <div class="grid_paginador">

                        <?php if($datos['general']->pagina > 1){ ?>

                        <span id="paginador_izquierda" onclick="mover_pagina('IZQ','<?php echo $datos['general']->pagina; ?>', '<?php echo $datos['general']->total_paginas; ?>', '<?php echo $modulo; ?>');">-</span>

                        <?php } ?>

                        <div id="grid_paginas_mascara">

                            <ul id="grid_paginas_contenedor">

                                <?php for($i = 1; $i <= $datos['general']->total_paginas; $i++){ ?>

                                <li onclick="cambiar_pagina('<?php echo $i; ?>', '<?php echo $modulo; ?>', '<?php echo $accion; ?>');" >

                                    <div <?php if($i ==  $datos['general']->pagina){ echo 'class="pagina_seleccionada"'; } ?>><?php echo $i; ?></div>

                                </li>

                                <?php } ?>

                            </ul>

                        </div>

                        <?php if($datos['general']->pagina < $datos['general']->total_paginas){ ?>

                        <span id="paginador_derecha" onclick="mover_pagina('DER', '<?php echo $datos['general']->pagina; ?>', '<?php echo $datos['general']->total_paginas; ?>', '<?php echo $modulo; ?>');">+</span>

                        <?php } ?>    

                    </div>

                </div>

            </div>

            <div class="foot_col foot_aling">P&aacute;gina 

                <select id="page" name="page" onchange="cambiar_pagina(this.options[this.selectedIndex].value, '<?php echo $modulo; ?>');">

                    <?php for($i = 1; $i <= $datos['general']->total_paginas; $i++){?>

                    <option id="<?php echo $i; ?>" <?php if((int)$i === (int)$datos['general']->pagina){ echo 'selected'; }?>><?php echo $i; ?></option>

                    <?php } ?>

                </select>                                

                <?php    echo ' de '.$datos['general']->total_paginas; ?>

            </div>

            <?php } ?>

        </td>

        <tr>

        </tfoot>

        <?php }

        static function pintar_tabla_vacia(){ ?>

        <tbody>

            <tr>

                <td>

                    No se encontraron registros!

                </td>

            </tr>

        </tbody>

        <?php }



//      Funciones



        static function link_id($modulo, $id, $accion){ ?>

        <a href="javascript:ir_a('?opcion=<?php echo $modulo;?>&a=<?php echo $accion; ?>&id=<?php echo $id;?>')"><?php echo (string)$id;?></a>

        <?php }



        static function link_edicion($modulo, $id, $accion){ ?>

        <a href="javascript:ir_a('?opcion=<?php echo $modulo;?>&a=<?php echo $accion; ?>&id=<?php echo $id;?>')">

           <?php echo (string)$id;?>

           <div class="grid_boton">

               enviar

           </div>

       </a>

     <?php }

     

     static function checkbox($modulo, $id, $accion){ ?>

     <input type="checkbox" id="check_<?php echo $id; ?>" name="grid_check_<?php echo $modulo; ?>" value="">

     <?php }



     static function radio_edicion($modulo, $id, $accion){ ?>

     <input type="radio" name="radio_edition" value="<?php echo $id; ?>">

     <?php }
 }

 ?>