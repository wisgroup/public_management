<?php

class MaestroHTML {

    static function formulario_dinamico($campos, $accion, $modulo, $db, $campos_personalizados = '', $parametros = '', $retorno = '', $titulo = 'CREACION/EDICI&Oacute;N', $form_complemento = '') {

        require_once 'controlador/php/lib/generales.class.php';
        $gn = new Generales($db, '');
        $id_interlocutor = $gn->get_data_loged('id_interlocutor');
        ?>

        <fieldset id="contenedor_formulario">
            <legend class="clase_titulo titulo_form"><p><?php echo $titulo; ?></p></legend>
            <div id="form_dinamico_contenedor">
                <form action="<?php echo '?opcion=' . $modulo . '&a=' . $accion; ?>" enctype="multipart/form-data" name="formulario_edicion" method="post" id="formulario_edicion" class="formulario_edicion" autocomplete="off"> 
                    <div id="contenedor_elemento" class="maestro_edit_form">
                        <table>
                            <?php
                            foreach ($campos as $key => $input) {
                                switch ($input['tipo']) {

                                    case 'text':
                                    case 'password':
                                    case 'email':
                                    case 'number':
                                    case 'url':
                                    case 'time':
                                    case 'radio':
                                    case 'checkbox':
                                    case 'button'
                                        ?>
                                        <tr>
                                            <td class="label_maestro"><b><?php echo parsear_label($key, $input); ?></b></td>
                                            <td class="input_maestro">
                                                <input type="<?php echo $input['tipo']; ?>" name="<?php echo $key ?>" id="<?php echo $key ?>" 
                                                       value="<?php $valor = (isset($input['valor'])) ? $input['valor'] : '';
                                        echo utf8_encode($valor); ?>"
                    <?php $complemento = (isset($input['complemento'])) ? $input['complemento'] . ' ' : '';
                    echo $complemento;
                    $checked = ($valor == 'S') ? 'checked' : '';
                    echo $checked . ' ';
                    ?> >
                                            </td>
                                        </tr> <?php
                    break;
                case 'hidden':
                    ?> <tr>
                                            <td><b></b></td>
                                            <td>
                                                <input type="<?php echo $input['tipo']; ?>" name="<?php echo $key ?>" id="<?php echo $key ?>" 
                                                       value="<?php $valor = (isset($input['valor'])) ? $input['valor'] : '';
                                   echo $valor; ?>"
                                        <?php $complemento = (isset($input['complemento'])) ? $input['complemento'] : '';
                                        echo $complemento;
                                        ?> >
                                            </td>
                                        </tr><?php
                                                break;
                                            case 'select':
                                                ?> <tr>
                                            <td class="label_maestro"><b><?php echo parsear_label($key, $input); ?></b></td>
                                            <td class="input_maestro">        
                                                <select   name="<?php echo $key ?>" id="<?php echo $key ?>" class="custom-input"
                                                    <?php
                                                    $complemento = (isset($input['complemento'])) ? $input['complemento'] : '';
                                                    echo $complemento;
                                                    ?> >

                                                    <?php if (isset($input['inicial'])) { ?>
                                                        <option value="<?php echo $input['inicial'] ?>"></option> 
                                                    <?php
                                                    }

                                                    if (isset($input['opciones']) && is_array($input['opciones'])) {

                                                        foreach ($input['opciones'] as $id_opcion => $opcion) {
                                                            if ($id_opcion == 'nit') {
                                                                ?>
                                                                <option value="<?php echo $id_opcion ?>" selected ><?php echo utf8_encode($opcion); ?></option>
                                                            <?php } else { ?>
                                                                <option value="<?php echo $id_opcion ?>" ><?php echo utf8_encode($opcion); ?></option>
                                                            <?php
                                                            }
                                                        }
                                                    } else {

                                                        $table = substr($key, 0, -3);
                                                        $campo_option = (isset($input['campo'])) ? $input['campo'] : 'nombre';

                                                        if (isset($input['condicion'])) {

                                                            $input['condicion'] .= " AND (interlocutor_id = " . $id_interlocutor . " OR interlocutor_id = 0 )";
                                                            $campos = $db->select(array('id_' . $table, $campo_option), $table, $input['condicion'], false, false, false);
                                                        } else {
                                                            $campos = $db->select(array('id_' . $table, $campo_option), $table);
                                                        }

                                                        foreach ($campos as $fila) {

                                                            foreach ($fila as $campo => $valor) {
                                                                $value = ($campo == 'id_' . $table) ? $valor : $value;
                                                                $option = ($campo == $campo_option) ? $valor : $option;
                                                            }

                                                            $selected = ($value == $input['valor']) ? "selected" : "";
                                                            ?> <option value="<?php echo $value ?>" <?php echo $selected ?> ><?php echo utf8_encode($option); ?></option> <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                    <?php
                    break;

                case 'date':
                case 'datetime':
                    ?><tr>
                                            <td class="label_maestro"><b><?php echo parsear_label($key, $input); ?></b></td>
                                            <td class="input_maestro">
                                                <input id="<?php echo 'datetimepicker_' . $key ?>" type="date" readonly name="<?php echo $key ?>" class="custom-input">  <!--condicion de solo lectura ---->
                                                <script>

                                                    $('#datetimepicker_' + '<?php echo $key ?>').datetimepicker({
                                                        dayOfWeekStart: 1,
                                                        lang: 'es'
                                                    });
                                                </script>
                                            </td>
                                        </tr>
                                        <?php
                                        break;
                                    case 'file':
                                        ?> <tr>
                                            <td class="label_maestro">
                                                <b><?php echo parsear_label($key, $input); ?></b>
                                            </td>
                                            <td class="input_maestro">
                                                <input id="archivo" type="file" name="<?php echo $key ?>" <?php
                                                $complemento = (isset($input['complemento'])) ? $input['complemento'] . ' ' : '';
                                                echo $complemento;
                                                ?>  value="Seleccionar Archivo...">
                                            </td>
                                        </tr>
                                        <?php
                                        break;

                                    case 'textarea':
                                        ?> <tr>
                                            <td class="label_maestro"><b><?php echo parsear_label($key, $input); ?></b></td>                                                
                                            <td class="input_maestro">
                                                <textarea id="<?php echo $key ?>" type="<?php echo $input['tipo']; ?>"  name="<?php echo $key ?>" value="" class="custom-input" 
                                                    <?php $complemento = (isset($input['complemento'])) ? $input['complemento'] . ' ' : '';
                                                    echo $complemento;
                                                    ?> ><?php echo $input['valor']; ?>
                                                </textarea>
                                            </td>
                                        </tr>
                                        <?php
                                        break;
                                    case 'link':
                                        ?> <tr>
                                            <td class="label_maestro"><b><?php echo parsear_label($key, $input); ?></b></td>
                                            <td class="input_maestro">
                                                <a target="_blank" onfocus="this.blur()" href="<?php echo $input['ruta'] . $input['valor'] ?>"><?php
                                        $descripcion = (isset($input['descripcion'])) ? $input['descripcion'] : $input['valor'];
                                        echo $descripcion;
                                        ?></a>
                                            </td>
                                        </tr>
                                <?php
                                break;
                            default:
                                ?> <tr>
                                            <td class="label_maestro"><b><?php echo parsear_label($key, $input); ?></b></td>
                                            <td class="input_maestro">
                                                <input id="<?php echo $key ?>" type="<?php echo $input['tipo']; ?>" name="<?php echo $key ?>" value="<?php echo $input['valor']; ?>">
                                            </td>
                                        </tr>
                    <?php
                    break;
            }
        }
        ?>
                        </table>
                    </div> 
        <?php echo $form_complemento; ?>
                    <!--Botones Aceptar y Cancelar del Formulario-->
                    <div id="botones_accion" class="maestro_edit_form">
                        <table>
                            <tr>
                                <td>
                                    <div id="accion_guardar" class="accion_boton">
                                        <button onfocus="this.blur()" type="submit" id="guardar" value="Guardar" class="guardar"></button>
                                    </div>
                                </td>
                                <td>
                                    <div id="accion_cancelar" class="accion_boton">
        <?php $direccionar = ($retorno == '') ? $modulo : $retorno; ?>
                                        <button onfocus="this.blur()" type="reset" id="cancelar" value="Cancelar" class="cancelar"  onclick="peticion_ajax('<?php echo '?opcion=' . $direccionar . '&type=ajax' ?>', '', 'area_trabajo');"></button>
                                    </div>
                                </td>
                            </tr>
                        </table>  
                    </div>
                </form> 
            </div>
        </fieldset>
        <script type="text/javascript">
            $("#formulario_edicion").validate({lang: 'es'});
            if (typeof inicializar == 'function') {
                inicializar();
            }
        </script>
        <?php
    }

    static function mostrar_alerta($mensaje, $titulo = '') {
        ?>
        <script type="text/javascript">mostrar_alerta('<?php echo $titulo; ?>', '<?php echo $mensaje; ?>');</script>
            <?php
        }

        function pintar_herramientas($modulo, $herramientas, $function = 'validar_herramienta') {
            ?> 
        <div id="menu_herramientas">
        <?php
        foreach ($herramientas as $key => $value) {
            ?>
                <div id="<?php echo $value . '_herramientas'; ?>" class="herramientas" title="<?php echo ucfirst($value); ?>" onmouseover="cambiar_imagen('<?php echo $value; ?>', 'herramientas', 1);" onmouseout="cambiar_imagen('<?php echo $value; ?>', 'herramientas', 0);" onclick="<?php echo $function; ?>('<?php echo $value ?>', '<?php echo $modulo ?>');">    
                    <div id="<?php echo 'imagen_' . $value ?>" class="imagen_herramienta">
                        <img   src=" <?php echo 'vista/img/producto/herramientas/' . $value . '.png' ?>" id="<?php echo $value ?>" name="<?php $value ?>"  widht="20px" height="20px" >
                    </div>
                </div>
            <?php }
        ?>
        </div>
        <?php
    }

    function detalle($maestro, $detalle, $entradas, $campos_detalle, $config) {
        ?>
        <div id="maestro_creacion" class="maestro_form">
            <div class="maestro_form_contenedor">
                <table>
                    <?php foreach ($entradas['campos'] as $key => $value) { ?> 
                        <tr>
                            <td>
                    <?php if (in_array($value, $config['buscar'])) { ?> 
                                    <input type="<?php echo $entradas['tipos'][$key]; ?>" id="<?php echo $value; ?>" name="<?php echo $value; ?>" onblur="buscar_registro(<?php echo $maestro; ?>, this.id, '<?php echo $entradas['condicion'][$key]; ?>');" placeholder="<?php echo ucfirst($key); ?>">
                                    <button onclick="buscar_registro('<?php echo $maestro; ?>', '<?php echo $value; ?>', '<?php echo $entradas['condicion'][$key]; ?>');
                                                                                                ">Buscar</button>
            <?php } else { ?>
                                    <input type="text" id="<?php echo $value; ?>" name="<?php echo $value; ?>">
            <?php } ?>
                            </td>
                        </tr>
        <?php } ?>
                </table>
            </div>
            <div id="maestro_detalle_individual" class="maestro_form_contenedor">
        <?php MaestroHTML::detalle_individual($campos_detalle); ?>
            </div>
        </div>
            <?php
        }

        static function detalle_individual($campos_detalle) {
            ?>
        <table>
        <?php foreach ($campos_detalle as $key => $value) { ?> 
                <tr>
                    <td><label><?php echo ucfirst($key); ?></label></td>
                    <td><?php echo $value; ?></td>
                </tr>
        <?php } ?>
        </table>
        <?php
    }

}
