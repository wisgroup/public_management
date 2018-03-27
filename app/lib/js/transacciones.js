/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function facturaClickServicioCheck(cuenta, checkid, $subtotal){
    var servicio_defecto = $subtotal * 0.1;
    if ($("#"+cuenta+"_"+checkid).prop('checked') === true) {
        $("#"+cuenta+"_"+"factura_servicio").val(Math.round(servicio_defecto));
        $("#"+cuenta+"_"+"factura_servicio_valor").val(servicio_defecto);
    }else{
        $("#"+cuenta+"_"+"factura_servicio").val(Math.round(0));
        $("#"+cuenta+"_"+"factura_servicio_valor").val(0);
    }
    factura_validar_servicio(cuenta, checkid);
}
function factura_validar_servicio(cuenta, checkid) {
    var valor_servicio = $("#"+cuenta+"_"+"factura_servicio").val();
    var valor_domicilio = $("#"+cuenta+"_"+"factura_domicilio").val();
    var descuento = $("#"+cuenta+"_"+"factura_descuento").val();
    var impuesto = $("#"+cuenta+"_"+"factura_impuesto").val();
    var porcentaje_descuento = 0;
    var descuento_valor = 0;
    total = $("#"+cuenta+"_"+"factura_subtotal").val();
    
    //TODO: Esta linea suple el impuesto, debe ser quitada en caso de configurar el calculo de impuestos
    impuesto = 0;
    
    if(parseInt(descuento) == 1){
        porcentaje_descuento = 5/100;
        descuento_valor = porcentaje_descuento * total;
    }
    
    if ($("#"+cuenta+"_"+checkid).prop('checked') === true) {
        valor_servicio = $("#"+cuenta+"_"+"factura_servicio_valor").val();
        servicio = 'true';
    } else {
        valor_servicio = 0;
        servicio = 'false';
        //$("#factura_servicio").prop('disabled', true);
    }
    
    total = Math.round(parseFloat(total) + parseFloat(valor_servicio)+ parseFloat(valor_domicilio)+parseFloat(impuesto) - parseFloat(descuento_valor));
    
    total = new Intl.NumberFormat().format(total);   
    
    var link_prefactura = $("#"+cuenta+"_url_prefactura").val();
    link_prefactura = link_prefactura+"&servicio="+valor_servicio;
    
    $("#"+cuenta+"_boton_prefactura1").attr('href', link_prefactura);
    $("#"+cuenta+"_boton_prefactura2").attr('href', link_prefactura);
    
    $("#"+cuenta+"_"+"numerico-total-ver-pedido").html("$"+total);
    peticion_ajax('?opcion=facturar&a=factura_mostrar_totales&valor_servicio='+valor_servicio+'&servicio=' + servicio +'&valor_domicilio=' + valor_domicilio + '&id_descuento='+descuento+'&type=ajax', '', 'factura_resumen_totales', null, null, null);
}
/*
 * 
 * if(check.checked){
 subtotal = $('#factura_subtotal').val();
 subtotal_servicio = parseInt(subtotal)*0.1;
 $('#factura_servicio').html("$"+subtotal_servicio);
 total = parseInt(subtotal)*1.1;
 $('#factura_total').html("$"+total);
 }else{
 subtotal = $('#factura_subtotal').val();
 $('#factura_servicio').html("$0");
 $('#factura_total').html("$"+subtotal);
 }
 * 
 */
function facturaClickDomicilioCheck(cuenta, checkid, $subtotal){
    var domicilio_defecto = 800;
    if ($("#"+cuenta+"_"+checkid).prop('checked') === true) {
        $("#"+cuenta+"_"+"factura_domicilio").val(Math.round(domicilio_defecto));
        $("#"+cuenta+"_"+"factura_domicilio_valor").val(domicilio_defecto);
    }else{
        $("#"+cuenta+"_"+"factura_domicilio").val(Math.round(0));
        $("#"+cuenta+"_"+"factura_domicilio_valor").val(0);
    }
    factura_validar_servicio(cuenta, "incluir_servicio");
}

function cajaAbrir() {
    base = $('#base_nueva').val();
    peticion_ajax('?opcion=caja_balance&a=abrir_caja&base=' + base + '&type=ajax', '', 'area_trabajo', null, null, null);
}
function cajaCerrar() {
    
    efectivo = $('#efectivo_cierre').val();
    $('#form_caja_actualizar').removeClass('hidden');
    $('#form_nueva_caja').addClass('hidden');
    peticion_ajax('?opcion=caja_balance&a=cerrar_caja&efectivo_cierre='+efectivo+'&type=ajax_part', '', 'area_trabajo', 'cierre_complemento', null, null);
}
function cierre_complemento(response){
    $('#id_caja').val(response);
}
function actualizarCaja() {
    efectivo = $('#efectivo_cierre').val();
    caja = $('#id_caja').val();
    peticion_ajax('?opcion=caja_balance&a=actualizar_caja&efectivo_cierre='+efectivo+'&id_caja='+caja+'&type=ajax', '', 'area_trabajo', null, null, null);
}
function facturaGenerarFactura(form) {
	//window.location='?opcion=subopcion&id=17&type=html';
    //window.open('?opcion=facturar&a=imprimir');
}
function facturaGenerarPreFactura(form) {
    
}
/*$("#pedido_producto_form").submit(function (e) {
        var formObj = $(this);
        var formURL = formObj.attr("action");
        var formData = new FormData(this);
        alert('aasdas');
        $.ajax({
            url: formURL,
            type: 'POST',
            data: formData,
            mimeType: "multipart/form-data",
            contentType: false,
            cache: false,
            processData: false,
            success: function (data, textStatus, jqXHR) {
                //window.open('?opcion=factura&a=imprimir');
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
        e.preventDefault(); //Prevent Default action. 
        e.unbind();
        //window.open('?opcion=factura&a=imprimir');
    });*/

function format_money(elemento){
	var nStr = $("#"+elemento.id).val();
        nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
        $("#"+elemento.id).val(x1 + x2)
}
function mostrarInformeDiario(modulo, fecha){
    peticion_ajax('?opcion='+modulo+'&a=mostrar_informe&fecha='+fecha+'&type=ajax', '', 'area_reporte', null, null, null);   
}
/*
$(document).ready(function () {
    jQuery(".chosen_observaciones").chosen();
});
*/
