
<?php

class FormHTML {

    static function formulario_dinamico($campos, $accion, $modulo, $db, $detalle = '', $campos_personalizados = '', $parametros = '', $retorno = '', $titulo = 'CREACION/EDICI&Oacute;N', $form_complemento = '', $ajax = 'ajax', $area = 'area_trabajo') {
        require_once(PATH_LIB_CLASS . "generales.class.php");
        $gn = new Generales($db, '');
        $cadena_nombre_campos = "";
        $id_interlocutor = $gn->get_data_loged('id_interlocutor');
        ?>        
        <div id="contenedor_formulario" class="wis_bloque">
            <div class="titulo_maestro">
                <p><?php echo $titulo; ?></p>
            </div>
            <div id="form_dinamico_contenedor">
                <form action="<?php echo '?opcion=' . strtolower($modulo) . '&a=' . $accion; ?>" enctype="multipart/form-data" name="formulario_edicion" method="post" id="formulario_edicion" class="formulario_edicion" autocomplete="off"> 
                    <div id="contenedor_elemento" class="maestro_edit_form">
                        <table class="form_lista_campos">
                            <?php
                            $i = 1;
                            $mitad = round(count($campos) / 2);
                            foreach ($campos as $nombre => $input) {
                                $tipo_tag = (isset($input['tag'])) ? $input['tag'] : 'generico';
                                FormHTML::item($tipo_tag, $nombre, $input, $db, $i);
                                if ((!isset($input['fila']) or $input['fila'] != 'unico') and ( isset($input['tipo']) && $input['tipo'] != 'hidden')) {
                                    $i++;
                                }
                                if ($i == ($mitad )) {
                                    ?>
                                </table>
                                <table class="form_lista_campos">
                                <?php }
                            }
                            ?>                           
                        </table>
                    </div>
                            <?php if ($detalle !== '') { ?>
                        <div class="maestro_edit_form tabla_detalle">
                            <table id="detalle_tabla">
                                    <?php foreach ($detalle as $nombre_detalle => $datos_detalle) { ?>
                                    <div class="titulo_detalle">
                                        <?php echo $datos_detalle['datos']['titulo']; ?>
                                    </div>
                                    <tr>
                                        <?php
                                        foreach ($datos_detalle['campos'] as $nombre_campo => $alias_campo) {
                                            $cadena_nombre_campos .= "'" . $nombre_campo . "', ";
                                            ?>
                                            <th><?php echo ucfirst($alias_campo); ?></th>
                                        <?php } ?>
                                    </tr>   
                                    <tr>
                                    <?php foreach ($datos_detalle['campos'] as $nombre_campo => $valor_campo) { ?>
                                            <td><input id="<?php echo $nombre_campo . '_0'; ?>" name="<?php echo $nombre_campo . '_0'; ?>" type="text" value=""></td>
                                    <?php } ?>
                                    </tr>
            <?php }
            $cadena_nombre_campos = substr($cadena_nombre_campos, 0, -2);
            ?> 
                            </table>
                            <input id="add_item_detalle" onclick="adicionar_item_detalle(<?php echo $cadena_nombre_campos; ?>);" type="button" value="+ Adicionar item" />
                        </div>
        <?php }
        echo $form_complemento;
        ?>
                    <!--Botones Aceptar y Cancelar del Formulario-->
                    <div id="botones_accion" class="maestro_edit_form">
                        <table>
                            <tr>
                                <td>
                                    <div id="accion_guardar" class="accion_boton">
                                        <button onfocus="this.blur()" type="submit" id="guardar" value="Guardar" class="guardar " onclick="//jLoad();">Guardar</button>
                                    </div>
                                </td>
                                <td>
                                    <div id="accion_cancelar" class="accion_boton">
        <?php $direccionar = ($retorno == '') ? $modulo : $retorno; ?>
                                        <button onfocus="this.blur()" type="reset" id="cancelar" value="Cancelar" class="cancelar "  onclick="peticion_ajax('<?php echo '?opcion=' . strtolower($direccionar) . '&type=ajax' ?>', '', '<?php echo $area; ?>');">Cancelar</button>
                                    </div>
                                </td>
                            </tr>
                        </table>  
                    </div>                        
                </form> 
            </div>
        </div>
        <?php
    }

    static function item($tipo_tag, $nombre, $input, $db, $fila) {
        $columnas = '1';
        if (isset($input['tipo']) && $input['tipo'] != 'hidden') {
            ?>
            <tr>
                <td class="label_maestro">
                    <b><?php echo parsear_label($nombre, $input); ?></b>
                </td>
                <td colspan="<?php echo $columnas; ?>" class="input_maestro ">
            <?php
        }
        FormHTML::$tipo_tag($nombre, $input, $db);
        if (isset($input['info'])) {
            ?>
                    <p class="info_campo"><?php echo $input['info'] ?></p>                  
        <?php } ?>
            </td>
        </tr>
               <?php
           }

           static function generico($key, $input, $db) {
               ?>
        <input type="<?php echo $input['tipo']; ?>" name="<?php echo $key ?>" id="<?php echo $key ?>" 
               value="<?php
               $valor = (isset($input['valor'])) ? $input['valor'] : '';
               echo utf8_encode($valor);
               ?>"
               <?php
               $complemento = (isset($input['complemento'])) ? $input['complemento'] . ' ' : '';
               echo $complemento;
               $checked = ($valor == 'S') ? 'checked' : '';
               echo $checked . ' ';
               ?> >
        <?php
    }

    static function accordeon($key, $input, $db) {
        ?>
        <button class="accordion">Section 1</button>
        <div class="panel">
            <p>Lorem ipsum...</p>
        </div>

    <?php
    }

    static function select($key, $input, $db) {

        require_once (PATH_LIB_CLASS . LIB_GENERALES);
        $gn = new Generales($db, '');
        $id_interlocutor = $gn->get_data_loged('id_interlocutor');
        $option = "";
        ?>
        <select name="<?php echo $key ?>" id="<?php echo $key ?>" class="custom-input" 
            <?php
            $complemento = (isset($input['complemento'])) ? $input['complemento'] : '';
            echo $complemento;
            ?> >
            <?php if (isset($input['inicial'])) { ?>
                <option value="<?php echo $input['inicial'] ?>"></option>
                <?php
            }
            if (isset($input['ninguno'])) { ?>
                <option value="<?php echo $input['ninguno'] ?>"><?php echo $input['label'] ?></option>
                <?php
            }
            if (isset($input['opciones']) && is_array($input['opciones'])) {
                foreach ($input['opciones'] as $id_opcion => $opcion) {
                    $selected = "";
                    if ($id_opcion == $input['valor']) {
                        $selected = "selected";
                    }
                    ?>
                    <option value="<?php echo $id_opcion ?>"<?php echo $selected; ?> ><?php echo utf8_encode($opcion); ?></option>
                    <?php
                }
            } else {
                if (isset($input['tabla'])) {
                    $table = $input['tabla'];
                } else {
                    $table = substr($key, 0, -3);
                }

                if (isset($input['value'])) {
                    $llave = $input['value'];
                } else {
                    $llave = 'id_' . substr($key, 0, -3);
                }
                $campo_option = (isset($input['campo'])) ? $input['campo'] : 'nombre';

                if (isset($input['condicion'])) {
                    $campos = $db->select(array($llave . ' as select_value', $campo_option . ' as select_option'), $table, $input['condicion']);
                } else {
                    $campos = $db->select(array($llave . ' as select_value', $campo_option . ' as select_option'), $table);
                }

                if (is_array($campos)) {

                    foreach ($campos as $fila) {
                        $value = $fila['select_value'];
                        $option = $fila['select_option'];

                        $selected = ($value == $input['valor']) ? "selected" : "";
                        ?> <option value="<?php echo $value ?>" <?php echo $selected ?> ><?php echo utf8_encode(ucfirst(strtolower($option))); ?></option> <?php
                }
            }
        }
            ?>
        </select>
        <?php
    }

    static function textarea($key, $input, $db) {
        ?>
        <textarea rows="4" cols="40" name="<?php echo $key ?>" ><?php echo utf8_encode($input['valor']); ?></textarea>
        <?php
    }

    static function datetime($key, $input, $db) {
        ?>
        <div class="fecha">
            <img id="calendario_<?php echo $key; ?>" src="media/img/calendario3.png" width="20px" height="25px" onclick="$('#<?php echo $key; ?>').datetimepicker('show');" >
            <input type="text" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $input['valor']; ?>" required readonly>
        </div>

        <script type="text/javascript">

            var id = '<?php echo $key; ?>';

            $("#" + id).datetimepicker({
                lang: 'es',
                format: 'Y-m-d H:i:s'
            });

        </script>
        <?php
    }

    static function date($key, $input, $db) {
        ?>
        <div class="fecha">
            <img id="calendario_<?php echo $key; ?>" src="fw/vista/img/calendario3.png" width="20px" height="25px" onclick="$('#<?php echo $key; ?>').datetimepicker('show');" >
            <input type="text" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $input['valor']; ?>" required readonly>
        </div>

        <script type="text/javascript">

            var id = '<?php echo $key; ?>';

            $("#" + id).datetimepicker({
                lang: 'es',
                timepicker: false,
                format: 'Y-m-d',
                closeOnDateSelect: true
            });

        </script>
            <?php
        }

        static function img($key, $input, $db) {
            $width = (isset($input['width'])) ? $input['width'] . 'px' : "50px";
            $height = (isset($input['height'])) ? $input['height'] . 'px' : "70px";
            $valor = (isset($input['valor'])) ? $input['valor'] : '';
            ?>
        <div class="img_maestro">
        <?php if (file_exists($input['ruta'] . $valor)) { ?>
                <img id="<?php echo $key; ?>" src="<?php echo $input['ruta'] . $valor ?>" width="auto" height="<?php echo $height ?>"
            <?php
            $complemento = (isset($input['complemento'])) ? $input['complemento'] : '';
            echo $complemento;
            ?>  >
            <?php } ?>
        </div>
                <?php
            }

            static function pintar_herramientas($modulo, $herramientas, $function = 'validar_herramienta') {
                ?> 
        <div id="menu_herramientas">
                <?php
                foreach ($herramientas as $key => $value) {
                    $function = (in_array($value, array('nuevo', 'reenviar', 'editar', 'borrar', 'csv', 'csv2', 'crear_remision'))) ? $function : $function . '_' . $modulo;
                    ?>
                <div id="<?php echo $value . '_herramientas'; ?>" class="herramientas" title="<?php echo ucfirst($value); ?>" onmouseover="cambiar_imagen('<?php echo $value; ?>', 'herramientas', 1);" onmouseout="cambiar_imagen('<?php echo $value; ?>', 'herramientas', 0);" onclick="<?php echo $function; ?>('<?php echo $value ?>', '<?php echo $modulo ?>');">    
                    <?php
                    switch ($value) {
                        case 'nuevo':
                            $tool = 'plus';
                            break;
                        case 'editar':
                            $tool = 'pencil';
                            break;
                        case 'borrar':
                            $tool = 'remove';
                            break;
                        default:
                            $tool = 'plus';
                            break;
                    }
                    ?>
                    <span class="glyphicon glyphicon-<?php echo $tool; ?>" title="Crear Elemento" id="<?php echo $value ?>" name="<?php $value ?>"></span>
            <?php echo strtoupper($value); ?>
                </div>
        <?php }
        ?>
        </div>
        <?php
    }

    static function mostrar_alerta($mensaje, $titulo = '') {
        ?>
        <script type="text/javascript">mostrar_alerta('<?php echo $titulo; ?>', '<?php echo $mensaje; ?>');</script>
        <?php
    }

}
