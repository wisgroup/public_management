<?php

class OperacionesHTML {

    static function formulario($clientes, $modulo = '', $encabezado = '', $titulo = '', $categorias = array()) {
        ?>  
        <div id="contenedor_formulario" class="wis-trxform wis_bloque"><!--mirar action-->
            <div class="titulo_maestro">
                <p><?php echo $titulo; ?></p>
            </div>
            <form class="form" role="form" id="form_transacciones" action="?opcion=<?php echo $modulo; ?>&a=registrar" method="post">
                <div class="wis-trx-encabezado wis-block">
                    <input type="hidden" id="cliente_id_interlocutor" name="cliente_id_interlocutor" value="">
                    <div class="row">
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
                </div>

                <div class="wis-trx-cuerpo wis-block">
                    <table id="wis-trx-product-table" class="wis-block">
                        <caption>Listado de productos</caption>
                        <thead>
                            <tr>
                                <?php foreach ($encabezado as $key => $value) {
                                    ?><th scope="col"><strong><?php echo $value; ?></strong></th><?php }
                                ?>
                                <th scope="col"><strong></strong></th>
                            </tr>
                        </thead>
                        <tbody id="wis-trx-product-table-body"></tbody>
                    </table>

                    <div class="wis-block">
                        <button type="button" onclick="mostrarFlotanteProductos('<?php echo $modulo; ?>');">+</button>
                    </div>
                    <table id="wis-trx-product-table" class="wis-block">
                        <tr>
                            <td>TOTAL</td>
                            <td>$<div id="factura_pie_total">0</div></td>
                        </tr>
                    </table>
                </div>
                <!--Botones Aceptar y Cancelar del Formulario-->
                <div id="botones_accion" class="maestro_edit_form wis-block ">
                    <table>
                        <tr>
                            <td>
                                <div id="accion_guardar" class="accion_boton ">
                                    <button onfocus="this.blur()" type="button" id="guardar" value="Guardar" class="guardar" onclick="jLoad();validarTransaccion('<?php echo $modulo; ?>');">Guardar</button>
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

    static function trx_agregar_producto($producto, $cantidad) {
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
            <td class="cantidad">
                <?php echo $producto['iva_incluido']; ?>
                <input type="hidden" name="<?php echo $producto['COD']; ?>_iva_incluido" id="<?php echo $producto['COD']; ?>_iva_incluido" value="<?php echo $producto['iva_incluido']; ?>">
            </td>
            <td class="cantidad">
                $<input name="<?php echo $producto['COD']; ?>_valor" id="<?php echo $producto['COD']; ?>_valor" class="wis_numerico_largo" type="text" value='<?php echo $producto['precio']; ?>' onchange="trxActualizarCantidad('<?php echo $producto['COD']; ?>');">
            </td>
            <td class="cantidad">
                $<input name="<?php echo $producto['COD']; ?>_valoriva" id="<?php echo $producto['COD']; ?>_valoriva" class="wis_numerico_largo wis_solo_lectura " type="text" readonly value='<?php echo $producto['iva_valor']; ?>' onchange="trxActualizarCantidad('<?php echo $producto['COD']; ?>');">
            </td>
            <td class="cantidad">
                $<b id="<?php echo $producto['COD']; ?>_subtotal"><?php echo ($producto['subtotal']); ?></b>
            </td>
            <td>
                <button type="button" onclick="trxRemoverItem('<?php echo $producto['COD']; ?>');">X</button>
            </td>
        </tr>
        <?php
    }

}
