<?

class ClaseHTML {

    static function submenu($clase, $CRUD, $datos = NULL) {
        $i = 1;
        ?>
        <div class="cont_titulo_modulo">
            <span class="inset">Administrador de <?php echo ucfirst($clase) ?></span>
        </div>
        <div id="cont_btn_arriba">
            <?php if (in_array('crear', $CRUD)) { ?> 
                <span id="btn_anadir_evento"><a href="javascript:nuevo('<?php echo $clase; ?>','0','', '1')" class="inset" onfocus="this.blur();" >Nuevo Elemento</a></span>
            <?php } ?> 
        </div>
        <?
        if ($datos[0] != "") {
            foreach ($datos as $dato) {
                ?>  
                <article class="modulo_edicion_txt">
                    <div class="titulos">
                        <div class="edit_No_edicion">
                            <?php if (in_array('orden', $CRUD)) { ?> 
                                <form>
                                    <input class="numero" name="nuevo_orden_<? echo $i; ?>" id="nuevo_orden_<? echo $i; ?>" type="text" value="<? echo $dato->orden; ?>">
                                    <input class="btn" name="<? echo $dato->orden; ?>" type="button" value="Cambiar" onclick="javascript:editarOrden('<?php echo $clase; ?>', '<? echo $dato->id; ?>', '<? echo $dato->orden; ?>', '<? echo $i; ?>');" onfocus="this.blur();" >
                                </form>
                            <?php } ?>
                        </div>
                        <p><? echo utf8_encode($dato->nombre); ?></p>

                        <div class="editar">
                            <ul>
                                <?php if (in_array('estado', $CRUD)) { 
                                    if ($dato->estado == 'A') { ?>
                                        <li class=" btn"><a href="javascript:publicarItem('<?php echo $clase; ?>','<? echo $dato->id; ?>','<? echo $dato->estado; ?>')" onfocus="this.blur();">Desactivar<span>Desactivar</span></a></li>
                                    <? } else { ?>
                                        <li class="btn"><a href="javascript:publicarItem('<?php echo $clase; ?>','<? echo $dato->id; ?>','<? echo $dato->estado; ?>')" onfocus="this.blur();" >Activar<span>Activar</span></a></li>
                                    <? } 
                                } if (in_array('editar', $CRUD)) { ?>
                                    <li class="btn_edit btn"><a href="javascript:javascript:nuevo('<?php echo $clase; ?>','1','<? echo $dato->id; ?>', '1');" onfocus="this.blur();">Editar<span>Editar</span></a></li>
                                <?php } if (in_array('eliminar', $CRUD)) { ?>
                                    <li class="btn_del btn"><a href="javascript:eliminarItem('<?php echo $clase; ?>','<? echo $dato->id; ?>');"onfocus="this.blur();" >eliminar<span>Eliminar</span></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </article>
                <? $i++;
            }
            ?>
            <div class="clearfix"></div>
            <?
        }
    }

    static function formEdicion($clase, $controles, $customs,$CRUD, $llaves, $listas = NULL, $id = "", $dato = NULL, $edit_footer = '') {
        ?>
        <div class="cont_titulo_modulo">
            <span class="inset">
                <?
                if ($id > 0) {
                    echo "Edición";
                }
                else
                    echo "Creación";
                ?> de <?php echo $clase; ?>
            </span>
        </div>
       

        <form id="form_item" name="form_item" method="post" enctype="multipart/form-data" action="<? if ($id == "") echo "index.php?opcion=$clase&a=guardarItem";
                else echo "index.php?opcion=$clase&a=editarItem"; ?>">
                <div class="cont_campos_variables">
                    <div class="edit_column">
                        <span class="inset">Nombre</span>
                    </div>
                    <input type="text" name="nombre" id="nombre" value="<? echo utf8_encode($dato->nombre); ?>">
                    <input type="hidden" name="id_item" id="id_item" value="<? echo $id; ?>"/>
                </div>    
                <?php if (in_array('lista', $controles)) { ?> 
                    <select>
                        <option value ="0">Seleccionar...</option>
                        <? foreach ($lista as $l) { ?>
                        <option value ="<? echo $l->id; ?>"><? echo utf8_encode($l->nombre); ?></option>
                        <? } ?>
                    </select>
                <? } ?>
                <? foreach ($listas as $key => $lista) {
                    $selected = $llaves[$key];
                    ?>
                    <div class="cont_campos_variables">
                        <div class="edit_column">
                            <span class="inset"><? echo $key; ?></span>
                        </div>
                        <select id="<? echo $llaves[$key]; ?>" name="<? echo $llaves[$key]; ?>" >
                            <option value ="0">Seleccionar...</option>
                            <? foreach ($lista as $key => $l) { ?>
                                <option value ="<? echo $l->id; ?>" <? if($l->id == $dato->$selected){echo "selected='selected'"; }?>><? echo utf8_encode($l->nombre); ?></option>
                            <? } ?>
                        </select>
                    </div>
                <?
                } ?>
                <? foreach ($customs as $c) { ?>
                    <div class="cont_campos_variables">
                        <div class="edit_column">
                            <span class="inset"><? echo $c->nombre; ?></span>
                        </div>
                        <?php switch ($c->tipo) {
                                case 'check':
                                   ?>
                                   <input type="checkbox" name="<? echo $c->nombre; ?>" id="<? echo $c->nombre; ?>" 
                                        <? $n = $c->nombre;
                                        if ($dato->$n == "S") {
                                            echo "checked='checked'";
                                        }
                                    ?>>
                                    <?
                                    break;
                                default:
                                    ?>
                                    <input type="text" name="<? echo $c->nombre; ?>" id="<? echo $c->nombre; ?>" value="<? $n = $c->nombre; echo utf8_encode($dato->$n); ?>">
                                    <?
                                break;
                            }?>
                    </div>
                    <?} ?>
            <?php if (in_array('descripcion', $controles)) { ?> 
                <div id="cont_editor">
                    <div class="titulos"><p>DESCRIPCIÓN</p></div>
                    <textarea id="descripcion" name="descripcion" cols="99" rows="17"><? echo $dato->descripcion; ?></textarea>
                </div>
            <?php } if (in_array('imagen', $controles)) { ?> 
                <article class="modulo_edicion">
                    <div class="titulos">
                        <div class="No_edicion">1</div>
                        <p>Imagen (600 de ancho x 200 de alto jpg)</p>
                    </div>
                    <div class="img_edicion">
                        <img src="../media/img/<? echo $clase; ?>/big/<? echo $dato->imagen; ?>" width="100%" height="100%" alt="Inserte una imagen">
                    </div>
                    <input class="cambiar_imagen" id="imagen" name="imagen" type="file">
                    <input type="hidden" name="imagen_ant" id="imagen_ant" value="<? echo $dato->imagen; ?>"/>
                </article>
            <?php } 
            if($edit_footer == 'editFooter'){
                ClaseHTML::editFooter($clase, $controles, $customs,$CRUD, $llaves, $listas = NULL, $id = "", $dato = NULL);
             } ?>
            
            <div id="cont_enviar_editar">
                <div id="enviar_editar">
                    <div class="cont_checkbox">
                        <?php if (in_array('estado', $CRUD)) { ?> 
                            <span class="inset">Estado</span>
                            <input name="estado" id="estado" type="checkbox" <?
                                if ($dato->estado == "A") {
                                    echo "checked='checked'";
                                }
                                ?>>
                        <?php } ?>
                    </div>
                    <?
                    if ($id == "") {
                        $edicion = '0';
                    }else
                        $edicion = '1';
                    ?>
                    <input id="input_atras" class="inset admin_button" name="atras" type="button" value="Cancelar" onclick="javascript:window.location = '?opcion=<? echo $clase; ?>';">
                    <input id="input_ingresar" class="inset admin_button" name="guardar" type="button" value="Guardar" onclick="javascript:validarBasico('<? echo $edicion ?>')">
                </div>
            </div>
        </form>
        <div class="clearfix"></div>
        <?
    }
    static function editFooter($clase, $controles, $customs,$CRUD, $llaves, $listas = NULL, $id = "", $dato = NULL) {
        ?>
            <div class="cont_campos_variables">
                <div class="edit_column">
                    <span class="inset">TOTAL</span>
                </div>
                <input type="text" id="total" value="<? echo utf8_encode($dato->nombre); ?>" readonly disabled >

            </div>    
        <?
    }
    static function verDetalle($clase, $datos) {
        $lineas = 1;
        ?>
        <div class="cont_titulo_modulo">
            <span class="inset"><?php echo ucfirst($clase) ?></span>
        </div>
        <div id="all" class="datagrid" style="margin-top: 33px;">
            <table id="tabla_detalle">
                <?php foreach ($datos as $key => $value) { 
                    if($lineas%2 == 1){
                ?>
                <tr>
                <?php } ?>   
                    <td>
                        <p><?php echo $key; ?> </p>
                    </td>
                    <td>
                        <input type="text" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $value; ?>">
                    </td>
                
                <?php if($lineas%2 == 0){ ?>
                </tr>
                <?php } 
                    $lineas++;
                    }?>
            </table>
        </div>
        
        <?
    }
}
?>
