<?php

class DatatableHTML {

    CONST ESTADO_ACTIVO = 1;

    static function home($encabezado, $contenido, $modulo = '', $accion = array(), $function = array(), $class = '', $titulo_pdf = '', $id_tabla = 'example', $checkbox = array(), $tabla = '') {
        if ($contenido != '' && $encabezado != '') {
            ?>  

            <div id="contenedor_datatable"  style="overflow:auto;  margin-bottom: 20px; margin-top: 2px; clear: both; margin-right: 0%; <?php echo $class; ?> ">
                <table id="<?php echo $id_tabla; ?>" class="compact cell-border"   >
                    <thead>
                        <tr>
                                <?php foreach ($encabezado as $campo) { ?>
                                <th><?php
                                    if ($campo == 'empty_no_check') {
                                        $cabecera = "";
                                    } else if ($campo == 'empty') {
                                        $cabecera = '<input type="checkbox" onclick="select_all(this, ' . "'tabla_" . $modulo . "'" . ');">';
                                    } else {
                                        $cabecera = $campo;
                                    }
                                    echo ucfirst(($cabecera));
                                    ?></th>
            <?php } ?>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                                <?php foreach ($encabezado as $campo) { ?>
                                <th><?php
                                    if (($campo == 'empty_no_check') || ($campo == 'empty')) {
                                        $cabecera = '';
                                    } else {
                                        $cabecera = $campo;
                                    }
                                    echo ucfirst(($cabecera));
                                    ?></th>
            <?php } ?>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php foreach ($contenido as $key => $campo) {
                            ?>
                            <tr>
                                    <?php foreach ($campo as $key_campo => $value) { ?> 
                                    <td><?php
                                        if ($key_campo == 'Estado') { //|| $key_campo=='Secundario'
                                            $function2 = 'select_estado';
                                            $id = $campo['empty'];
                                            $accion_seleccionada = '';
                                            if (isset($accion[$function2])) {
                                                $accion_seleccionada = $accion[$function2];
                                            }
                                            DatatableHTML::$function2($modulo, $id, $value, $accion_seleccionada, $key_campo, $tabla);
                                        } else {
                                            if (isset($function[$key_campo])) {
                                                $function2 = $function[$key_campo];
                                                /* if ($function2 == 'checkbox_grupo' || $function2 == 'radio_select_dw'){
                                                  DatatableHTML::$function2($modulo, $value, $accion);
                                                  } else{ */
                                                DatatableHTML::$function2($modulo, $value, $accion[$function2]);
                                                /* } */
                                            } else {
                                                echo utf8_encode($value);
                                            }
                                        }
                                        ?>
                                    </td><?php } ?>
                            </tr>
            <?php } ?>
                    </tbody>
                </table>
            </div>
            <script type="text/javascript">
                var titulo = '<?php print_r($titulo_pdf); ?>';

                var id_tabla = '<?php echo $id_tabla; ?>';
                $('#' + id_tabla).dataTable(SetDatatable(titulo)).columnFilter();

                function ir_a($url) {
                    peticion_ajax($url, '', 'area_trabajo');
                }

                function ir_a_detalle($url) {
                    peticion_ajax($url, '', 'detalle_maestro');
                }

                function uncheked(id, name) {
                    $("." + name).prop("checked", false);
                    $("#" + id).prop("checked", true);  // para poner la marca
                }
            </script>
            <?php
        } else {
            echo "<div id=" . '"no_result"' . "><p>No se encontraron registros.<p><div>";
        }
    }

    static function reporte($encabezado, $modulo, $titulo_pdf = '', $id_tabla = 'example', $class = '', $file = 'fw/controlador/js/lib/DataTables/scripts/server_processing.php') {
        ?>
        <div id="contenedor_datatable"  style="overflow:auto;  margin-bottom: 20px; margin-top: 2px; clear: both; margin-right: 0%; <?php echo $class; ?> ">
            <table id="<?php echo $id_tabla; ?>" class="compact cell-border">
                <thead><tr>
                            <?php foreach ($encabezado as $campo) { ?>
                            <th><?php
                                $cabecera = ($campo == 'empty') ? '' : $campo;
                                echo ucfirst(($cabecera));
                                ?></th>
        <?php } ?>
                    </tr></thead>
                <tfoot><tr>
                            <?php foreach ($encabezado as $campo) { ?>
                            <th><?php
                                $cabecera = ($campo == 'empty') ? '' : $campo;
                                echo ucfirst(($cabecera));
                                ?></th>
        <?php } ?>
                    </tr></tfoot>
            </table>
        </div>
        <script type="text/javascript">
            var titulo = '<?php print_r($titulo_pdf); ?>';
            var id_tabla = '<?php echo $id_tabla; ?>';
            var file = '<?php echo $file; ?>';
            $('#' + id_tabla).dataTable(SetDatatableReporte(titulo, file));
        </script>
        <?php
    }

    //      Funciones

    static function link_id($modulo, $id, $accion) {
        ?>
        <button  onfocus="this.blur()"class="boton_datatable" onclick="javascript:ir_a('?opcion=<?php echo $modulo; ?>&a=<?php echo $accion; ?>&id=<?php echo $id; ?>')"><?php echo (string) $id; ?></button>
        <?php
    }

    static function link_mas($modulo, $id, $accion) {
        ?>
        <button onfocus="this.blur()" class="boton_datatable" onclick="javascript:ir_a('?opcion=<?php echo $modulo; ?>&a=<?php echo $accion; ?>&id=<?php echo $id; ?>')">+</button>
        <?php
    }

    static function link_edicion($modulo, $id, $accion) {
        ?>
        <a href="javascript:ir_a('?opcion=<?php echo $modulo; ?>&a=<?php echo $accion; ?>&id=<?php echo $id; ?>')">
        <?php //echo (string)$id;  ?>
            <button onfocus="this.blur()" class="boton_datatable" >Enviar</button>
        </a>
        <?php
    }

    static function checkbox($modulo, $id, $accion) {
        ?>
        <input onfocus="this.blur()" type="checkbox" id="check_<?php echo $id; ?>" name="grid_check_<?php echo $modulo; ?>"   value="<?php echo $id; ?>">
        <?php
    }

    static function select_estado($modulo, $id, $value, $accion, $field, $tabla) {
        if ($field == 'Estado') {
            if ($value == 1) {
                ?>
                <select id="<?php echo $id; ?>_select" onchange="cambiar_estado(<?php echo "'" . $id . "'"; ?>,<?php echo "'" . $modulo . "'"; ?>,<?php echo "'" . $tabla . "'"; ?>)">
                    <option value="1" selected>Activo</option>
                    <option value="2">Bloqueado</option>             
                </select>
            <?php } elseif ($value == 2) { ?>
                <select id="<?php echo $id; ?>_select" onchange="cambiar_estado(<?php echo "'" . $id . "'"; ?>,<?php echo "'" . $modulo . "'"; ?>,<?php echo "'" . $tabla . "'"; ?>)">
                    <option value="1">Activo</option>
                    <option value="2" selected>Bloqueado</option>              
                </select>
            <?php } elseif ($value == 11) {
                ?>
                <select id="<?php echo $id; ?>_select" onchange="cambiar_estado(<?php echo "'" . $id . "'"; ?>,<?php echo "'" . $modulo . "'"; ?>,<?php echo "'" . $tabla . "'"; ?>)">
                    <option value="11" selected>libre</option>
                    <option value="12" >reservada</option> 
                    <option value="13" >ocupada</option>              
                </select>
            <?php } elseif ($value == 12) { ?>
                <select id="<?php echo $id; ?>_select" onchange="cambiar_estado(<?php echo "'" . $id . "'"; ?>,<?php echo "'" . $modulo . "'"; ?>,<?php echo "'" . $tabla . "'"; ?>)">
                    <option value="11" >libre</option>
                    <option value="12" selected>reservada</option> 
                    <option value="13" >ocupada</option>              
                </select>
            <?php } elseif ($value == 13) { ?>
                <select id="<?php echo $id; ?>_select" onchange="cambiar_estado(<?php echo "'" . $id . "'"; ?>,<?php echo "'" . $modulo . "'"; ?>,<?php echo "'" . $tabla . "'"; ?>)">
                    <option value="11" >libre</option>
                    <option value="12" >reservada</option> 
                    <option value="13" selected>ocupada</option>              
                </select>
                <?php
            }
        }/* else{
          if($value){?>
          <input class="check-destacados_s" onchange="destacar(<?php echo $id; ?>,<?php echo "'".$field."'"; ?>,<?php echo $value; ?>)" onfocus="this.blur()" type="checkbox" id="check_<?php echo $id; ?>" name="grid_check_<?php echo $modulo; ?>"   value="<?php echo $id; ?>" checked>
          <?php }else{?>
          <input class="check-destacados_s" onchange="destacar(<?php echo $id; ?>,<?php echo "'".$field."'"; ?>,<?php echo $value; ?>)" onfocus="this.blur()" type="checkbox" id="check_<?php echo $id; ?>" name="grid_check_<?php echo $modulo; ?>"   value="<?php echo $id; ?>">
          <?php }
          } */
    }

    static function checkbox_grupo($modulo, $id, $accion) {
        ?>
        <input onfocus="this.blur()" type="checkbox" id="check_<?php echo $id; ?>" name="grid_check_<?php echo $modulo; ?>"  value="<?php echo $id; ?>" <?php
        if (in_array($id, $accion)) {
            echo 'checked';
        }
        ?> >

        <?php
    }

    static function radio_edicion($modulo, $id, $accion) {
        ?>
        <input onfocus="this.blur()"  type="checkbox" name="radio_edition" value="<?php echo $id; ?>" onclick="<?php echo $accion . '(' . $id . ", '" . $modulo . "');"; ?>">
        <?php
    }

    static function radio_edicion_maestro($modulo, $id, $accion) {
        ?>
        <input onfocus="this.blur()"  type="checkbox" id="edition_<?php echo $id; ?>" class="radio_edition" name="radio_edition" value="<?php echo $id; ?>" onclick="javascript:uncheked('edition_<?php echo $id; ?>', 'radio_edition');
                ir_a_detalle('?opcion=<?php echo $modulo; ?>&a=<?php echo $accion; ?>&id=<?php echo $id; ?>')">
               <?php
           }

           static function radio_edicion_slave($modulo, $id, $accion) {
               ?>
        <input onfocus="this.blur()"  type="checkbox" name="radio_edition_slave" value="<?php echo $id; ?>" onclick="<?php echo $accion . '(' . $id . ", '" . $modulo . "');"; ?>">
        <?php
    }

    /* static function radio_select_dw($modulo, $id, $accion){ ?>
      <input onfocus="this.blur()"  type="checkbox" name="radio_edition" value="<?php echo $id; ?>" onclick="<?php echo $accion['funcion'] . '(' . $id . ", '" . $modulo . "', '" . $accion['dw_element'] . "');"; ?>">
      <?php
      } */

    static function detalle_maestro($id = 'detalle_maestro') {
        ?>
        <div id='<?php echo $id; ?>' class='detalle_maestro' ></div>
        <?php
    }

    static function mostrar_datos_item($modulo, $valor, $funcion) {
        ?>
        <a href="#" onclick="<?php echo $funcion . "('" . $modulo . "','" . $valor . "');"; ?>" class=''><b><?php echo $valor; ?></b></a>
        <?php
    }

}
