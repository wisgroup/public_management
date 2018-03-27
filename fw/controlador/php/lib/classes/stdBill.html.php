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
                                <?php
                                if (in_array('estado', $CRUD)) {
                                    if ($dato->estado == 'A') {
                                        ?>
                                        <li class=" btn"><a href="javascript:publicarItem('<?php echo $clase; ?>','<? echo $dato->id; ?>','<? echo $dato->estado; ?>')" onfocus="this.blur();">Desactivar<span>Desactivar</span></a></li>
                                    <? } else { ?>
                                        <li class="btn"><a href="javascript:publicarItem('<?php echo $clase; ?>','<? echo $dato->id; ?>','<? echo $dato->estado; ?>')" onfocus="this.blur();" >Activar<span>Activar</span></a></li>
                                    <?
                                    }
                                } if (in_array('editar', $CRUD)) {
                                    ?>
                                    <li class="btn_edit btn"><a href="javascript:javascript:nuevo('<?php echo $clase; ?>','1','<? echo $dato->id; ?>', '1');" onfocus="this.blur();">Editar<span>Editar</span></a></li>
                                <?php } if (in_array('eliminar', $CRUD)) { ?>
                                    <li class="btn_del btn"><a href="javascript:eliminarItem('<?php echo $clase; ?>','<? echo $dato->id; ?>');"onfocus="this.blur();" >eliminar<span>Eliminar</span></a></li>
                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </article>
                <?
                $i++;
            }
            ?>
            <div class="clearfix"></div>
            <?
        }
    }

    static function formEdicion($clase, $listas = NULL, $id = "", $tipo = '4') {
        ?>
        <div class="cont_titulo_modulo">
            <span class="inset">
        <?php echo strtoupper($clase); ?>
            </span>
        </div>

        <form id="bill_form" name="bill_form" method="post" enctype="multipart/form-data" action="<?
        if ($id == "")
            echo "index.php?opcion=$clase&a=guardarItem";
        else
            echo "index.php?opcion=$clase&a=editarItem";
        ?>">

            <article id="bill_existencia" >
                <div class="bill_cabecera">
                    <h6>BUSCADOR DE PRODUCTOS</h6>
                    <div class="linea">
                        <label>Cod</label>
                        <input name="buscador_id" id="buscador_id" type="text">
                    </div>
                    <div class="linea">
                        <label>Nombre</label>
                        <input name="buscador_nombre" id="buscador_nombre" type="text" onkeypress="buscarProducto('contenedor_productos');">
                        <input name="buscar" id="buscar" class="inset buscador_button" type="button" value="buscar" onclick="buscarProducto('contenedor_productos');">
                    </div>
                </div>
                <div id="bill_lista_productos" class="">
                    <div id="lista_header" class="linea">
                        <div class="codigo">COD</div>
                        <div class="producto">PRODUCTO</div>
                        <div class="cantidad">COLOR</div>
                        <div class="cantidad">STOCK</div>
                        <div class="valor">ADD</div>
                    </div> 
                    <div id="contenedor_productos">
        <?php foreach ($listas['PRODUCTO'] as $p) { ?>
                            <div class="linea">
                                <input name="<?php echo $p->id; ?>_codigo" id="<?php echo $p->id; ?>_codigo" type="hidden" value="<?php echo $p->id; ?>">
                                <div class="codigo"><?php echo $p->id; ?></div>
                                <div id="<?php echo $p->id; ?>_cod" class="valor"><?php echo utf8_encode($p->cod); ?></div>
                                <div id="<?php echo $p->id; ?>_nombre" class="producto"><?php echo utf8_encode($p->nombre); ?></div>
                                <div id="<?php echo $p->id; ?>_cantidad" class="cantidad"><?php echo $p->cantidad; ?></div>
                                <div class="cantidad"><input name="<?php echo $p->id; ?>_add_cant" id="<?php echo $p->id; ?>_add_cant" class="add_cant" type="text" value='1'></div>
                                <div class="cantidad"><a class="add_button" onclick="addProduct('<?php echo $p->id; ?>');" title="adicionar" onfocus="this.blur();">+</a></div>
                                <input name="<?php echo $p->id; ?>_valor_compra" id="<?php echo $p->id; ?>_valor_compra" type="hidden" value="<?php echo number_format($p->valor_compra, 0, ',', '.'); ?>">
                            </div> 
        <?php } ?>
                    </div>
                </div> 
            </article>
            <article id="bill_selected">
                <div class="bill_cabecera">
                        <div class="linea">
                            <label>Tipo</label>
                            <select id="tipo_id" name="tipo_id" onchange="selectTipoTrans(this);">
                                <option value="3" <? if ($tipo == '3') echo 'selected'; ?>>INGRESO</option>
                                <option value="4" <? if ($tipo == '4') echo 'selected'; ?>>SALIDA</option>
                            </select>
                            <label>Fecha</label>
                            <input name="fecha" id="fecha" type="date" class="fecha" value="<?php echo date('Y-m-d'); ?>" >
                        </div>
                    <div id="cabecera_venta">
                        <div class="linea">
                            <label id="bill_nombre">Cliente</label>
                            <input name="acreedor" id="acreedor" type="text">
                            <label>CC/NIT</label>
                            <input name="cc" id="cc" type="text">
                        </div>
                        <div class="linea">
                            <label>Direccion</label>
                            <input name="direccion" id="direccion" type="text">
                            <label>Telefono</label>
                                <input name="telefono" id="telefono" type="text">
                        </div>
                    </div>
                    <div id="cabecera_ingreso">
                        <div class="linea">
                            <label id="bill_nombre">PROVEEDOR</label>
                            <select id="proveedor_id" name="proveedor_id" >
                                <?php foreach ($listas['PROVEEDOR'] as $v) {?>
                                    <option value="<?php echo $v->id; ?>"><?php echo utf8_encode($v->nombre); ?></option>
                                <?php } ?>    
                            </select>
                        </div>
                    </div>
                    <div class="linea">
                        <label>Observaci&oacute;n</label>
                        <textarea name="observacion" id="observacion"></textarea>
                    </div>
                </div>
                <div id="selected_header" class="linea">
                    <div class="codigo">COD</div>
                    <div class="producto">PRODUCTO</div>
                    <div class="cantidad">COLOR</div>
                    <div class="cantidad">CANT.</div>
                    <div class="valor">QUITAR</div>
                </div> 
                <div id="contenedor_seleccion">

                </div>
                <div id="selected_footer">
                    <div class="linea">
                        <div id="bill_label_total" class="valor">TOTAL&nbsp;&nbsp;<span class="valor_resaltado">$</span></div>
                        <div id="bill_total" class="valor valor_resaltado">0</div>
                    </div>
                </div> 
            </article>
            <div id="cont_enviar_editar">
                <div id="enviar_editar">
                    <?
                    if ($id == "") {
                        $edicion = '0';
                    } else{
                        $edicion = '1';
                    }
                    ?>
                    <input id="input_atras" class="inset admin_button" name="atras" type="button" value="Cancelar" onclick="javascript:window.location = '?opcion=<? echo $clase; ?>';">
                    <input id="registrar" class="inset admin_button" name="registrar" type="button" value="Registrar" onclick="javascript:validarTransaccion('<? echo $edicion ?>')">
                </div>
            </div>
        </form>
        <div class="clearfix"></div>
        <?
    }

    static function busqueda($lista) {
        if (count($lista) > 0) {
            foreach ($lista['PRODUCTOS'] as $p) {
                ?>
                <div class="linea">
                    <div class="codigo"><?php echo $p->id; ?></div>
                    <div id="<?php echo $p->id; ?>_nombre" class="producto"><?php echo utf8_encode($p->nombre); ?></div>
                    <div class="cantidad"><?php if($p->color === ""){ echo "N/A";}else{ echo $p->color;} ?></div>
                    <div id="<?php echo $p->id; ?>_cantidad" class="cantidad"><?php echo $p->cantidad; ?></div>
                    <div class="cantidad"><input name="<?php echo $p->id; ?>_add_cant" id="<?php echo $p->id; ?>_add_cant" class="add_cant" type="text" value="1"></div>
                    <div class="campo_boton"><a class="add_button" onclick="addProduct('<?php echo $p->id; ?>');" title="adicionar" onfocus="this.blur();">+</a></div>
                    <input name="<?php echo $p->id; ?>_valor_compra" id="<?php echo $p->id; ?>_valor_compra" type="hidden" value="<?php echo number_format($p->valor_compra, 0, ',', '.'); ?>">
                </div> 
                <?php
            }
        }
    }

}
?>
