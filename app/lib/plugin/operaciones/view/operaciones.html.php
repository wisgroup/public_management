<?php

class OperacionesHTML {

    static function formulario($clientes, $modulo = '', $encabezado = '', $titulo = '', $categorias = array(), $resumen = array(), $c = array(), $medio = array(), $supercategorias = NULL) {
        ?>
        <link rel="stylesheet" href="app/lib/plugin/operaciones/view/css/operaciones_mostrador.css"  type="text/css" media="screen" /> 
        <div id="contenedor_formulario" class="wis-trxform wis_bloque">
            <section class="operaciones_area_productos"> 
                <?php
                if (!(is_null($supercategorias))) {
                    ?>
                    <ul class="operaciones_super_categorias">
                        <div id="operaciones_tools" class="trx-header-search">
                            <div class="wis-block">
                                <input id="trx_product_search" onchange="productoBusqueda('trx_product_search', '<?php echo $modulo; ?>')" on name="trx_product_search" type="text" placeholder="Código de Barras">
                                <button type="button" class="wis_boton_busqueda glyphicon glyphicon-search" onclick="productoBusqueda('trx_product_search', '<?php echo $modulo; ?>');"></button>
                            </div>
                            <div class="wis-block search_params_area">
                                <div class="search_param" >
                                    <input id="trx_product_query" name="trx_product_query" type="checkbox" placeholder="Consultar">
                                    <label>Consulta</label>
                                </div>
                            </div>
                        </div>
                        <?php foreach ($supercategorias as $categoria) { ?>
                            <li onclick="trxMostrarCategorias('<?php echo $modulo; ?>', <?php echo $categoria['id_producto_categoria']; ?>);" 
                                id="supercategoria_<?php echo $categoria['id_producto_categoria']; ?>"
                                class="<?php
                                if ($supercategorias[0]['id_producto_categoria'] == $categoria['id_producto_categoria']) {
                                    echo "supercategoria_activa";
                                }
                                ?>"
                                >
                                    <?php echo utf8_encode($categoria['nombre']); ?>
                            </li>
                        <?php }
                        ?>
                    </ul>
                    <?php
                }
                ?>
                <div id="categorias_contenedor" class="operaciones_categorias">
                    <?php self::pintar_categorias($categorias, $modulo); ?>
                </div>
                <div class="operaciones_productos" id="operaciones_productos">
                    
                </div>
            </section>
            <section  class="operaciones_area_trx"> 
                <form class="form" role="form" id="form_transacciones" action="?opcion=<?php echo $modulo; ?>&a=registrar&type=ajax_part" method="post">
                    <div class="operaciones_trx_resumen">
                        <div class="operaciones_trx_impuestos">
                            <?php self::resumenPago($medio, $modulo); ?>
                            <table id="total_lvl2" class="trx_resumen_tabla">
                                <tr>
                                    <td><b>Subtotal: $</b></td>
                                    <td><div id="trx_subtotal" class="sub" >0</div></td>
                                </tr>
                                <tr>
                                    <td><b>Impuesto: $</b></td>
                                    <td><div id="trx_impuestos" >0</div></td>
                                </tr>
                            </table>
                        </div>
                        <div class="operaciones_trx_total">
                            <span>
                                <b>$</b>
                                <div id="trx_total">0</div>
                                <input type="hidden" name="trx_pago_notformat" id="trx_pago_notformat" value="0">
                            </span>
                            <button onfocus="this.blur()" type="button" id="guardar" value="Guardar" target="_blank" class="guardar trx_aceptar" onclick="jLoad();validarTransaccion('<?php echo $modulo; ?>');">Registar</button>
                        </div>
                    </div>
                    <div class="operaciones_trx_detalle">
                        <div id="trx_detalle_items_box">


                        </div>
                        <!--<button class="btn_nav_detalle btn_up">arriba </button>
                        <button class="btn_nav_detalle btn_down">abajo </button>
                        -->
                    </div>
                </form>
                <!--<button class="btn_nav_trx_viewer"> > </button>-->
            </section>
        </div>
        <script>
            supercategoria_activa = "<?php echo $supercategorias[0]['id_producto_categoria']; ?>";
        </script>
        <?php
    }

    static function pintar_categorias($categorias, $modulo) {
        ?>
        <div class="tab">
            <?php foreach ($categorias as $categoria) { ?>
                <button class="tablinks" onclick="openTab(event, '<?php echo $categoria['id_producto_categoria']; ?>', '<?php echo $modulo; ?>')">
                    <?php echo utf8_encode($categoria['nombre']); ?>
                </button>
            <?php }
            ?>
        </div>
        <?php foreach ($categorias as $categoria) { ?>
            <div id="<?php echo $categoria['id_producto_categoria']; ?>" class="tabcontent">
                <div id="aside_categoria_<?php echo $categoria['id_producto_categoria']; ?>">

                </div>
            </div>
        <?php } ?>
        <?php
    }

    static function formulario_old($clientes, $modulo = '', $encabezado = '', $titulo = '', $categorias = array(), $resumen = array(), $c = array(), $medio = array()) {
        ?>  
        <link rel="stylesheet" href="app/lib/plugin/operaciones/view/css/operaciones.css"  type="text/css" media="screen" /> 
        <div id="contenedor_formulario" class="wis-trxform wis_bloque"><!--mirar action-->

            <form class="form" role="form" id="form_transacciones" action="?opcion=<?php echo $modulo; ?>&a=registrar&type=ajax_part" method="post">
                <input type="hidden" id="cliente_id_interlocutor" name="cliente_id_interlocutor" value="">
                <div class="wis-trx-aside wis-block">
                    <div class="tab">
                        <?php foreach ($categorias as $categoria) { ?>
                            <button class="tablinks" onclick="openTab(event, '<?php echo $categoria['id_producto_categoria']; ?>', '<?php echo $modulo; ?>')">
                                <?php echo utf8_encode($categoria['nombre']); ?>
                            </button>
                        <?php }
                        ?>
                    </div>
                    <?php foreach ($categorias as $categoria) { ?>
                        <div id="<?php echo $categoria['id_producto_categoria']; ?>" class="tabcontent">
                            <h3><?php echo $categoria['nombre']; ?></h3>
                            <div id="aside_categoria_<?php echo $categoria['id_producto_categoria']; ?>">

                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="wis-trx-complement wis-block">
                    <div class="titulo_maestro">
                        <p><?php echo $titulo; ?></p>
                    </div>
                    <div class="wis-trx-encabezado wis-block">
                        <div class="trx-resumen-block">
                            <?php self::resumenPago($medio); ?>
                        </div>
                        <div class="trx-resumen-block">
                            <table id="total_lvl2 " class="trx_resumen_tabla">
                                <tr>
                                    <td><b>Subtotal: $</b></td>
                                    <td><div id="sub" class="sub" style="color: rgb(0,0,0); font-size: 20px;">0</div></td>
                                </tr>
                                <tr>
                                    <td><b>Impuesto: $</b></td>
                                    <td><div id="imp" style="color: rgb(0,0,0); font-size: 20px;" >0</div></td>
                                </tr>

                                <tr>
                                    <td><b>Total: $</b></td>
                                    <td><div id="tot" style="color: rgb(0,0,0); font-size: 20px;">0</div></td>
                                </tr>
                            </table>
                            <input type="hidden" name="trx_pago_notformat" id="trx_pago_notformat" value="0">
                        </div>
                        <div class="trx-resumen-block">
                            <?php self::resumenCliente($c, $modulo); ?>

                        </div>
                        <?php self::clienteForm(); ?>
                    </div>

                    <div class="wis-trx-cuerpo wis-block">
                        <div class="trx-header-search">
                            <div class="wis-block">
                                <input id="trx_product_search" onchange="productoBusqueda('trx_product_search', '<?php echo $modulo; ?>')" on name="trx_product_search" type="text">
                                <button type="button" class="wis_boton_busqueda" onclick="productoBusqueda('trx_product_search', '<?php echo $modulo; ?>');">+</button>
                            </div>
                        </div>
                        <table id="wis-trx-product-table" class="wis-block">
                            <caption>Listado de productos</caption>
                            <thead>
                                <tr>
                                    <?php foreach ($encabezado as $key => $value) {
                                        ?><th scope="col"><strong><?php echo $value; ?></strong></th><?php }
                                    ?>
                                </tr>
                            </thead>
                            <tbody id="wis-trx-product-table-body">
                                <tr>
                                    <?php foreach ($encabezado as $key => $value) { ?>
                                        <td scope="col" >
                                            <div class="trx-cell"></div>
                                        </td>
                                    <?php } ?>
                                </tr>
                            </tbody>
                        </table>


                                                                        <!--  <table id="wis-trx-product-table" class="wis-block">
                                                                            <tr>
                                                                                <td>TOTAL</td>
                                                                                <td>$<div id="factura_pie_total">0</div></td>
                                                                            </tr>
                                                                        </table>-->
                    </div>
                </div>
            </form>               
        </div>
        <div class="clearfix"></div>
        <script>
            $(function () {
                var modulo = '<?php echo $modulo ?>';
                $("#cliente_num_documento").autocomplete({
                    source: '?opcion=' + modulo + '&a=traer_comercios&campo=numero',
                });
            });
        </script>
        <?php
    }

    static function trx_agregar_producto($producto, $cantidad, $modulo) {
        ?>
        <div id="<?php echo $producto['COD']; ?>_fila" class="trx_detalle_item item_factura">
            <input name="<?php echo $producto['COD']; ?>_codigo" id="<?php echo $producto['COD']; ?>_codigo" class="" type="hidden" value='<?php echo $producto['COD']; ?>'>
            <input name="<?php echo $producto['COD']; ?>_valor" id="<?php echo $producto['COD']; ?>_valor" class="wis_numerico_largo" type="hidden" value='<?php echo $producto['precio']; ?>' onchange="trxActualizarCantidad('<?php echo $producto['COD']; ?>');">
            <div class="detalle_item_nombre">
                <span>
                    <?php echo utf8_encode($producto['PRODUCTO']); ?>
                </span>
                <cite>
                    <var><abbr title="Pesos">$</abbr>
                        <input name="<?php echo $producto['COD']; ?>_precio" id="<?php echo $producto['COD']; ?>_precio" class="wis_numerico item_cantidad" type="text" value='<?php echo number_format($producto['precio']); ?>'  onchange="trxActualizarPrecio('<?php echo $producto['COD']; ?>');" onfocus="actualizarFormatoPrecio(this.id, 'no_format');" onblur="actualizarFormatoPrecio(this.id, 'format');">
                    </var>
                    <b>x</b> 
                    <input name="<?php echo $producto['COD']; ?>_cantidad" id="<?php echo $producto['COD']; ?>_cantidad" class="wis_numerico_corto item_cantidad" type="text" value='<?php echo $cantidad; ?>'  onchange="trxActualizarCantidad('<?php echo $producto['COD']; ?>', '<?php echo $modulo; ?>');">
                </cite>
            </div>
            <div class="detalle_item_costo">
                <b>$</b> <b id="<?php echo $producto['COD']; ?>_subtotal"><?php echo ($producto['subtotal']); ?></b>
            </div>
            <button type="button" class="wis_cancelar" onclick="trxRemoverItem('<?php echo $producto['COD']; ?>');">X</button>
        </div>
        <?php
    }

    static function trx_agregar_producto_old($producto, $cantidad) {
        ?>
        <tr id="<?php echo $producto['COD']; ?>_fila" class="item_factura">
            <td>
                <input name="<?php echo $producto['COD']; ?>_codigo" id="<?php echo $producto['COD']; ?>_codigo" class="" type="hidden" value='<?php echo $producto['COD']; ?>'>
                <?php echo $producto['referencia']; ?>
            </td>
            <td>
                <?php echo $producto['barcode']; ?>
            </td>
            <td><?php echo utf8_encode($producto['PRODUCTO']); ?></td>
            <td class="cantidad">
                <input name="<?php echo $producto['COD']; ?>_cantidad" id="<?php echo $producto['COD']; ?>_cantidad" class="wis_numerico_corto" type="text" value='<?php echo $cantidad; ?>'  onchange="trxActualizarCantidad('<?php echo $producto['COD']; ?>');">
            </td>
            <td class="cantidad">
                <input name="<?php echo $producto['COD']; ?>_iva" id="<?php echo $producto['COD']; ?>_iva" class="wis_numerico_corto" type="text" value='<?php echo $producto['iva']; ?>'  onchange="trxActualizarCantidad('<?php echo $producto['COD']; ?>');">%
            </td>
            <!--<td class="cantidad">
            <?php echo $producto['iva_incluido']; ?>
                <input type="hidden" name="<?php echo $producto['COD']; ?>_iva_incluido" id="<?php echo $producto['COD']; ?>_iva_incluido" value="<?php echo $producto['iva_incluido']; ?>">
            </td>-->
            <td class="cantidad">
                $<input name="<?php echo $producto['COD']; ?>_valor_format" id="<?php echo $producto['COD']; ?>_valor_format" class="wis_numerico_largo" type="text" value='<?php echo number_format($producto['precio'], 0, ",", "."); ?>' onchange="trxActualizarCantidad('<?php echo $producto['COD']; ?>');">
                <input name="<?php echo $producto['COD']; ?>_valor" id="<?php echo $producto['COD']; ?>_valor" class="wis_numerico_largo" type="hidden" value='<?php echo $producto['precio']; ?>' onchange="trxActualizarCantidad('<?php echo $producto['COD']; ?>');">
            </td>
            <!--<td class="cantidad">
                $<input name="<?php echo $producto['COD']; ?>_valoriva" id="<?php echo $producto['COD']; ?>_valoriva" class="wis_numerico_largo wis_solo_lectura " type="text" value='<?php echo $producto['iva_valor']; ?>' onchange="trxActualizarCantidad('<?php echo $producto['COD']; ?>');">
            </td>-->
            <td class="cantidad">
                $<b id="<?php echo $producto['COD']; ?>_subtotal"><?php echo ($producto['subtotal']); ?></b>
            </td>
            <td>
                <button type="button" class="wis_cancelar" onclick="trxRemoverItem('<?php echo $producto['COD']; ?>');">X</button>
            </td>
        </tr>
        <?php
    }

    static function clienteForm() {
        ?>
        <div class="row hidden">
            <div class="col-md-2">
                <label>TIPO DOCUMENTO</label>
                <div class="radio">
                    <label><input id="CC" value="cedula" type="radio" name="cliente_tipo_documento" checked>&nbsp;CC</label>
                    <label><input id="NIT" value="nit" type="radio" name="cliente_tipo_documento" >&nbsp;NIT</label>
                </div>
                <div class="form-group">
                    <label>NUMERO</label>
                    <input type="text" class="form-control" id="cliente_num_documento" name="cliente_num_documento" placeholder="Introduce el numero"
                           onchange="buscar_interlocutor(this, '<?php echo $modulo ?>', 'cliente')" required>
                </div>
            </div> 
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-6"><div class="form-group">
                            <label >NOMBRE</label>
                            <input type="text" class="automatic form-control" readonly id="cliente_nombre" name="cliente_nombre" requiered>
                        </div></div>
                    <div class="col-md-6"><div class="form-group">
                            <label>APELLIDO</label>
                            <input type="text" class="automatic form-control" readonly id="cliente_apellido" name="cliente_apellido">
                        </div></div>
                </div>
                <div class="row">
                    <div class="col-md-6"><div class="form-group">
                            <label>TELEFONO</label>
                            <input type="text" class="automatic form-control" readonly id="cliente_telefono" name="cliente_telefono">
                        </div></div>
                    <div class="col-md-6"><div class="form-group">
                            <label>CELULAR</label>
                            <input type="text" class="automatic form-control" readonly id="cliente_celular" name="cliente_celular" >
                        </div></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="cliente_apellido">EMAIL</label>
                            <input type="email" class="automatic form-control" readonly id="cliente_email" name="cliente_email" >
                        </div>
                        <div class="form-group">
                            <label >DIRECCION</label>
                            <input type="text" class="automatic form-control" readonly id="cliente_direccion" name="cliente_direccion" >
                        </div>
                    </div>
                    <div class="col-md-8"><div class="form-group">
                            <label >OBSERVACION</label>
                            <textarea class="form-control " rows="3" id="cliente_observacion" name="cliente_observacion"></textarea>
                        </div></div>
                </div>
            </div>
        </div> <!-- FIN row -->
        <?php
    }

    static function resumenTotal($datos = null) {
        ?>
        <div>



        </div>
        <?php
    }

    static function resumenPago($medio, $modulo = NULL) {
        ?>
        <div class="total_lvl2 resumen_linea">
            <table> 
                <!--<tr>
                    <th><label>Medio de Pago</label></th>
                    <td><select  id="pago_tipo" name="pago_tipo" >
                <?php foreach ($medio as $m) { ?>
                                                                        <option value="<?php echo utf8_encode($m['nombre']); ?>"><?php echo utf8_encode($m['nombre']); ?> </option>
                <?php } ?>
                        </select></td>
                </tr>
                -->
                <?php if ($modulo === "compras") { ?>   
                <tr class="trx_factura_numero">
                    <th><label>Comprobante</label></th>
                    <td>
                        <input type="text" class="input_pago" name="factura_numero" id="factura_numero" value="">
                    </td>
                    
                </tr>
                <?php } ?>   
                <tr>
                    <th><label>Efectivo</label></th>
                    <td>
                        <form name="pay">
                            <input type="number" class="input_pago" name="trx_pago" id="trx_pago" value="0" onchange="cambiar();">
                        </form>
                    </td>
                </tr>
                <tr>
                    <th><label>Cambio</label></th>
                    <td><span name="cambio" id="cambio" >0</span></td>
                </tr>
            </table>
        </div>
        <?php
    }

    static function resumenCliente($c, $modulo) {
        ?>
        <div class="total_lvl2 resumen_linea">
            <!--<p>Seleccione</p>
            <select  id="proveedor" name="proveedor" >
            <?php foreach ($c as $cliente) { ?>
                                                            <option value="<?php echo utf8_encode($cliente['nombre']); ?>"><?php echo utf8_encode($cliente['nombre']); ?> </option>
            <?php } ?>
            </select>
            -->
            <?php if ($modulo === "compras") { ?>   
                <table>
                    <tr>
                        <td>Comprobante de pago</td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" name="factura_numero" id="factura_numero">
                        </td>
                    </tr>
                </table>
            <?php } ?>
            <!--Botones Aceptar y Cancelar del Formulario-->
            <div id="botones_accion" class="maestro_edit_form wis-block ">
                <table>
                    <tr>
                        <td>
                            <div id="accion_guardar" class="accion_boton ">
                                <button onfocus="this.blur()" type="button" id="guardar" value="Guardar" target="_blank" class="guardar" onclick="jLoad();validarTransaccion('<?php echo $modulo; ?>');">Registar</button>
                            </div>
                        </td>
                        <td>
                            <div id="accion_cancelar" class="accion_boton">
                                <button onfocus="this.blur()" type="reset" id="cancelar" value="Cancelar" class="cancelar"  onclick="peticion_ajax('<?php echo '?opcion=' . 'inicio' . '&type=ajax' ?>', '', 'body');">Cancelar</button>
                            </div>
                        </td>
                    </tr>
                </table>  
            </div>

        </div>


        <?php
    }

    static function trxProductoAdicionar($producto) {
        ?>
        <tr>
            <td scope="col">
                <div class="trx-cell"></div>
            </td>
            <td scope="col">
                <div class="trx-cell"></div>
            </td>
            <td scope="col">
                <div class="trx-cell"></div>
            </td>
            <td scope="col">
                <div class="trx-cell"></div>
            </td>
            <td scope="col">
                <div class="trx-cell"></div>
            </td>
            <td scope="col">
                <div class="trx-cell">kjjkljñlkjjlñjñljñlñjñ</div>
            </td>
            <td scope="col">
                <div class="trx-cell"></div>
            </td>
            <td scope="col">
                <div class="trx-cell"></div>
            </td>
            <td scope="col">
                <div class="trx-cell"></div>
            </td>
        </tr>
        <?php
    }

    static function tab_productos($modulo, $productos = null, $configuracion = null) {
        ?>
        <ul>
            <?php foreach ($productos as $producto) { ?>
                <li 
                    <?php if((($producto['stock'] > 0)|| $producto['stock_control']=="N" || $configuracion['stock_control']== 0) || $modulo == "compras"){ ?>
                        onclick="TrxAddProduct(<?php echo $producto['id_producto']; ?>, '<?php echo $modulo; ?>');"
                    <?php }else{ ?>
                        class="<?php echo $modulo; ?> producto_agotado"
                    <?php } ?>
                >
                    <p><?php echo utf8_encode($producto['nombre']); ?> </p>
                    <h7>$<?php echo number_format($producto['precio']); ?> 
                        <?php if($producto['stock_control']=="S" ){ ?>
                        <b>(<?php echo $producto['stock'];  ?>)</b>
                        <?php } ?>
                    </h7>
                </li>
            <?php } ?>
        </ul>
        <?php
    }

    static function producto($modulo, $producto = null) {
        ?>
        <ul>
            <li onclick="TrxAddProduct(<?php echo $producto['id_producto']; ?>, '<?php echo $modulo; ?>');">
                <p><?php echo utf8_encode($producto['nombre']); ?> </p>
            <h7>$<?php echo number_format($producto['precio']); ?> </h7>
        </li>
        </ul>
        <?php
    }
    

}
