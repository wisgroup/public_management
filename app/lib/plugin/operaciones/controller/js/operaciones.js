function buscarProducto(contenedor) {
    var id = document.getElementById("buscador_id").value;
    var nombre = document.getElementById("buscador_nombre").value;
    peticion_ajax('?opcion=transaccion&a=buscar&id=' + id + '&nombre=' + nombre, '', contenedor);

}
function addProducto(id) {
    var contenedor = document.getElementById('contenedor_seleccion');
    var nombre = document.getElementById(id + '_nombre').innerHTML;
    var cantidad = document.getElementById(id + '_cantidad');
    var stock_inicial = cantidad.innerText;
    var stock = cantidad.innerText;
    var add_cant = document.getElementById(id + '_add_cant').value;
    var tipo = document.getElementById('tipo_id').value;
    var producto = '';
    var hiden = document.getElementById('hide_' + id);///////////////////captura los objetos y los asigna a variables.   
    if (isNaN(add_cant)) {
        add_cant = 1;
    }
    if (add_cant > stock) {
        alert('No puede agregar tanto producto');

    }else{
        var sw_exist = document.getElementById(id + '_producto');
        if (!(sw_exist === null)) {
            producto_cantidad = document.getElementById(id + '_producto_cantidad');
            var producto_total = document.getElementById(id + '_producto_total');
            producto_cantidad.innerHTML = parseInt(producto_cantidad.innerHTML) + parseInt(add_cant);
            cantidad.innerHTML = parseInt(cantidad.innerHTML) - parseInt(add_cant);
            hiden.value = parseInt(hiden.value) + parseInt(add_cant);

        }else{// if(add_cant >= 1){
            producto = '<div id="' + id + '_producto" class="linea"><table>';
            producto += '<td class="codigo" name="id" value="' + id + '">' + id + '</td>';
            producto += '<td class="producto">' + nombre + '</td>';
            producto += '<td id="' + id + '_producto_cantidad" class="cantidad">' + add_cant + '</td>';
            cantidad.innerHTML = parseInt(cantidad.innerHTML) - parseInt(add_cant);
            producto += '<td class="cantidad"><a class="add_button" onclick="removeProduct(' + "'" + id + "'" + ');" title="adicionar" onfocus="this.blur();">-</a></td></table>';
            stock = cantidad.innerText;
            var dif = stock_inicial - stock;
            producto += '<input id="hide_' + id + '" type="hidden" name="' + id + '" value="' + dif + '"></div>';
            contenedor.innerHTML = contenedor.innerHTML + producto;
        }
    }
}
var trx_cantidades = new Array();
var trx_valores = new Array();
var producto_actual = 0;
function TrxAddProduct(id, modulo) {
    //var cantidad = parseInt(document.getElementById(id + '_add_cant').value);
    cantidad = 1;
    cantidad_anterior = trx_cantidades[id];

    /*if(!validarStock(id)){
     return;
     }*/

    if (parseInt(cantidad_anterior) > 0) {
        cantidad = parseInt(cantidad_anterior) + parseInt(cantidad);
    }
    trx_cantidades[id] = cantidad;
    if ($("#" + id + "_codigo").length > 0) {
        //cantidad = parseInt($("#" + id + "_cantidad").val()) + parseInt(cantidad);
        $("#" + id + "_cantidad").val(cantidad);
        //$("#" + id + "_subtotal").html(parseInt($("#" + id + "_cantidad").val()) * parseInt($("#" + id + "_valor").val()));
        trxActualizarTotales();
    } else {
        peticion_ajax('?opcion=' + modulo + '&a=trx_agregar_producto&type=ajax_part&id=' + id + '&cantidad=' + cantidad, '', '', 'TrxRowAppend');
    }
    cerrar_flotante();
}
function trxMostrarCategorias(modulo, id){
    $("#supercategoria_"+supercategoria_activa).removeClass("supercategoria_activa");
    supercategoria_activa = id;
    $("#supercategoria_"+supercategoria_activa).addClass("supercategoria_activa");
    peticion_ajax('?opcion=' + modulo + '&a=trx_filtrar_categorias&type=ajax_part&id=' + id , '', 'categorias_contenedor');
}
var modulo_lectura_actual = "";
function buscar_producto(modulo, id){
    peticion_ajax("?opcion=" + modulo + "&a=buscar_producto&id=" + id, '', "operaciones_productos");
}
function productoBusqueda(elemento, modulo){
    var codigo = $("#"+elemento).val();
        
    if( $('#trx_product_query').prop('checked') ) {
        buscar_producto(modulo, codigo);
    }else{
        peticion_ajax('?opcion=' + modulo + '&a=trx_agregar_producto_x_codigo&type=ajax_part&codigo=' + codigo + '&cantidad=1'  , '', '', 'trxActualizarItems');
        modulo_lectura_actual = modulo;
        $("#"+elemento).val("");
    }
    
    /*
        peticion_ajax('?opcion=' + modulo + '&a=trx_agregar_producto_x_codigo&type=ajax_part&codigo=' + codigo + '&cantidad=1'  , '', '', 'trxActualizarItems');
        modulo_lectura_actual = modulo;
        $("#"+elemento).val("");
        //TrxAddProduct(codigo, modulo);
    */
}
function trxActualizarItems(response){
    TrxAddProduct(response, modulo_lectura_actual);
}
function TrxRowAppend(response) {
    $('#trx_detalle_items_box').append(response);
    trxActualizarTotales();
}
function trxActualizarCantidad(id, modulo) {
    var cantidad = $("#" + id + "_cantidad").val();
    producto_actual = id;
    peticion_ajax('?opcion=' + modulo + '&a=trx_validar_cantidad&type=ajax_part&id=' + id + '&cantidad='+cantidad  , '', '', 'trxValidarCantidad');
}
function trxValidarCantidad(response) {
    trx_cantidades[producto_actual] = response;
    $("#" + producto_actual + "_cantidad").val(response)
    producto_actual = 0;
    trxActualizarTotales();
}
function trxActualizarPrecio(id) {
    var precio = $("#" + id + "_precio").val();
    $("#" + id + "_valor").val(precio);
    $("#" + id + "_precio").val(number_format(precio, 0));
    trxActualizarTotales();
}
function number_format(amount, decimals) {

    amount += ''; // por si pasan un numero en vez de un string
    amount = parseFloat(amount.replace(/[^0-9\.]/g, '')); // elimino cualquier cosa que no sea numero o punto

    decimals = decimals || 0; // por si la variable no fue fue pasada

    // si no es un numero o es igual a cero retorno el mismo cero
    if (isNaN(amount) || amount === 0) 
        return parseFloat(0).toFixed(decimals);

    // si es mayor o menor que cero retorno el valor formateado como numero
    amount = '' + amount.toFixed(decimals);

    var amount_parts = amount.split('.'),
        regexp = /(\d+)(\d{3})/;

    while (regexp.test(amount_parts[0]))
        amount_parts[0] = amount_parts[0].replace(regexp, '$1' + ',' + '$2');

    return amount_parts.join('.');
}

function trxActualizarTotales() {
    //$("#" + id + "_subtotal").html(parseInt($("#" + id + "_cantidad").val()) * parseInt($("#" + id + "_valor").val()));
    //var cantidad = $("#" + id + "_cantidad").val();
    var total = 0;
    var subtotal = 0;
    
    trx_cantidades.forEach(function (cantidad, id) {
        if((cantidad > 0)&&(!(typeof $("#" + id + "_valor").val() === "undefined"))) {
            subtotal = parseInt(cantidad) * parseInt($("#" + id + "_valor").val());
            console.log(cantidad+"==> "+id+"==> "+subtotal+" ==> VAL: "+$("#" + id + "_valor").val());
            iva_incluido = $("#" + id + "_iva_incluido").val();
            if (iva_incluido == 'S') {
                valor_iva = parseInt(subtotal) * parseFloat(16 / 100);
                subtotal = parseInt(valor_iva) + parseInt(subtotal);
            } else {
                valor_iva = 0;
                subtotal = parseInt(subtotal);
            }

            $("#" + id + "_valoriva").val(valor_iva);
            $("#" + id + "_subtotal").html(number_format(subtotal, 0));
            total = parseInt(total) + parseInt(subtotal);   
        }
    });

   // $("#factura_pie_total").html(parseInt(total));
    $("#trx_subtotal").html(number_format(total,0));
    $("#trx_impuestos").html(number_format(valor_iva,0));
    $("#trx_total").html(number_format(total,0));


    $("#trx_pago_notformat").val(parseInt(total));
    
}

function cambiar(){

    var pago=$("#trx_pago").val() - $("#trx_pago_notformat").val();
    //$('#cambio').html(pago);
    $("#cambio").html($("<label>$"+(number_format(pago,0))+"</label>"));
}

function formatInt(n) {
    n.toFixed(2).replace(/./g, function (c, i, a) {
        return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
    });
    return n;
}
function addCommas(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

function trxRemoverItem(id) {
    $('#' + id + '_fila').remove();
    //trx_cantidades.splice(id, 1);
    trx_cantidades[id]= 0;
    trxActualizarTotales();
}
function removeProduct(id) {
    var valor_compra = $(id + '_valor_compra').value;
    var cantidad = document.getElementById(id + '_cantidad');
    var producto_cantidad = document.getElementById(id + '_producto_cantidad');
    var div = document.getElementById(id + "_producto");
    var tipo = $('tipo_id').value;
    var hiden = document.getElementById('hide_' + id);
    producto_cantidad.innerText = parseInt(producto_cantidad.innerText) - 1;
    if (producto_cantidad.innerText == 0) {
        div.parentNode.removeChild(div);
    }
    if (cantidad !== null) {
        cantidad.innerHTML = parseInt(cantidad.innerHTML) + 1;
    }

    hiden.value = parseInt(hiden.value) - 1;
}

function validarTransaccion(modulo) {

    if ($(".item_factura").length <= 0) {
        jClose();
        mostrar_alerta('Alerta', 'Debe adicionar por lo menos 1 producto');
        return;
    }

    //TODO: Habilitar al momento de adicionar la funcionalidad de clientes y proveedores
    /*if ($('#cliente_documento').val()=='' || $('#cliente_nombre').val()=='') {
        jClose();
        var comercio='';
        if (modulo=='ventas') {
            comercio = 'cliente';
        }else{
            comercio = 'proveedor';
        }
        mostrar_alerta('Alerta','Debe ingresar los datos del '+comercio);
        return;
    };
    */

    document.getElementById('form_transacciones').submit();
}

function imprimirElemento(id) {
    //w = window.open('http://localhost/apps/moss_v304/factura.php?id='+id);
    w = window.open('?opcion=ventas&a=imprimir_factura_pdf&id=' + id);
    //w.window.print();
    //window.location='index.php=opcion=ventas';
}

function vistaPrevia(data) {
    var mywindow = window.open('', 'my div', 'height=400,width=600');
    mywindow.document.write('<html><head><title>my div</title>');
    /*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
    mywindow.document.write('</head><body >');
    mywindow.document.write(data);
    mywindow.document.write('</body></html>');

    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10

    mywindow.print();
    mywindow.close();

    return true;
}
/*WIS FLOTANTE PRODUCTOS*/
function mostrarFlotanteProductos(modulo) {
    abrir_flotante('?opcion=' + modulo + '&a=traer_productos');
}


/*NUMERACION FACTURAS*/
function setFacturaActual() {
    var factura_minimo = $('#factura_minimo').val();
    var factura_maximo = parseInt(factura_minimo) + 1;
    $('#factura_actual').val(factura_minimo);
    $('#factura_maximo').val(factura_maximo);
}
function validarFacturaMaximo() {
    var factura_minimo = $('#factura_minimo').val();
    var factura_maximo = $('#factura_maximo').val();
}


function buscar_interlocutor(input, modulo, comercio) {
    peticion_ajax('?opcion=' + modulo + '&a=setear_campos&type=ajax_part&interlocutor=' + input.value, '', '', 'setear_interlocutor', comercio);
}

function setear_interlocutor(response, comercio) {

    var obj = JSON.parse(response);

    if (obj.respuesta == 0) {
        $(":input[class^=automatic]").each(function (index, element) {
            $(this).attr('readonly', false);
            $(this).val('');
        });

    } else {

        $(":input[class^=automatic]").each(function (index, element) {
            $(this).attr('readonly', true);
        });

        $.each(obj, function (k, v) {

            if ($("#" + comercio + "_" + k).length > 0) {
                $("#" + comercio + "_" + k).val(v);
            }
        });

    }

}
function reporteMostrarDetalle(id, modulo) {
    peticion_ajax('?opcion=' + modulo + '&a=mostrar_detalle&type=ajax_part&id=' + id, '', '', 'subarea_detalle');
}
function reporteMostrarDetallePedido(id, modulo) {
    peticion_ajax('?opcion=' + modulo + '&a=mostrar_detalle_pedido&type=ajax_part&id=' + id, '', 'subarea_detalle');
}

function mostrar_datos_item(modulo, id) {
    peticion_ajax('?opcion=' + modulo + '&a=mostrar_datos_item&type=ajax_part&id=' + id, '', 'flotante_contenido');
    $('#flotante').css('display', 'block');
}
function mostrar_datos_item_html(id) {
    $('#flotante_contenido').html();
}


/*TRX TABS*/
function openTab(evt, tabName, modulo) {
    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
    peticion_ajax("?opcion=" + modulo + "&a=select_cat&id=" + tabName, '', "aside_categoria_"+tabName);
} 

function separarCuenta(mesa){
    var items = "";
    $( ".check_factura_item" ).each(function( index ) {
        if($(this).prop( "checked" )){
            name = $( this ).attr('name') ;
            name_parts = name.split("_"); 
            id = name_parts[0];
            cantidad = $('#'+id+'_cantidad_mover_factura_item').val();
            items += id+"|"+cantidad+";"; 
        }
    });
    peticion_ajax("?opcion=facturar&a=separar_cuenta&items=" + items+"&id_mesa="+mesa, '', "subarea_trabajo");
}
function separarCuentac(mesa){
    var items = "";
    $( ".check_factura_item" ).each(function( index ) {
        if($(this).prop( "checked" )){
            name = $( this ).attr('name') ;
            name_parts = name.split("_"); 
            id = name_parts[0];
            cantidad = $('#'+id+'_cantidad_mover_factura_item').val();
            items += id+"|"+cantidad+";"; 
        }
    });
    peticion_ajax("?opcion=pedido_recoleccion_pos&a=separar_cuentac&items=" + items+"&id_mesa="+mesa, '', "contenedor_facturas");
}
function mostrar_prefactura(mesa, cuenta){
    peticion_ajax("?opcion=facturar&a=mostrar_prefactura&id_mesa=" + mesa+"&id_cuenta="+cuenta, '', "subarea_trabajo");
}
function actualizarFormatoPrecio(elemento, formato){
    var nuevo_valor = "";
    var valor = $("#"+elemento).val();
    
    if(formato == "no_format"){
        nuevo_valor = valor.replace(",", "");
        valor = nuevo_valor.replace(".", "");
        
    }else{
        valor = number_format(valor, 0);
    }
    
    $("#"+elemento).val(valor);
}