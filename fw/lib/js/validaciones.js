
/*MODULO MENSAJES*/

function validar_envio_simple(){

    var destinatario = $('#destinatario').val();
    var grupo = $('#grupo_id').val();

    if (destinatario == '' && grupo == 0) {
        mostrar_alerta('','Ingrese el destinatario o seleccione un grupo');
        $('#destinatario').focus();
        return false;
    };

    if (destinatario != '' && grupo != 0) {
        mostrar_alerta('','Ingrese el destinatario o seleccione un grupo, pero no ambos');
        $('#destinatario').focus();
        return false;
    };



    var mensaje = $('#mensaje').val();
    var mensaje_guardado = $('#mensaje_id').val();


     if (mensaje == '' && mensaje_guardado == 0) {
        mostrar_alerta('','Ingrese un mensaje');
        $('#destinatario').focus();
        return false;
    };

    // var mensaje_valido = /^[0-9a-zA-Z]$/;

    // if (mensaje.match(mensaje_valido)) {

    //     alert('cumple');
    // }else{
    //     alert('No cumple');
    // };
    // return;

    $('#form_sms').submit();
    return true;
}




// $("#mensaje_id" ).change(function() {

//     alert('aasas');

//     if ($(this).val()!= 0) {
//         $('#mensaje').val($( "#mensaje_id option:selected" ).text());
//         contar_mensaje();
//     };
         
//     });



/*------------------------------------------------------------------------------*/

/*Validar Formularios*/

function validar_formulario(){
    var elementos = new Array();    
    $('.requerido').each(function(i, elem){
        elementos.push($(elem));
    }); 
    
    for(var i = 0;i < elementos.length;i = i+1) {
        if(elementos[i].val() == "" || elementos[i].val() == 0 ){
            mostrar_alerta("html","Debe ingresar un "+  elementos[i].attr('id')+" valido");
            return false;
        }   
    }
    return true;
}

function buscar_registro(maestro, elemento, condicion){
    var numeros = /^[0-9]{1,20}$/;
    var valor = $('#'+elemento).val();
    
    if($('#'+elemento).attr('type') === 'number' && ((!valor.match(numeros)) || valor === '' )){
        mostrar_alerta('html','La cantidad debe ser numérica');
        $('#'+elemento).select();
        $('#'+elemento).focus();
        return false;
    }else if($('#'+elemento).attr('type') === 'text' && (valor === '' )){
        mostrar_alerta('html','Debe introducir un valor v&aacute;lido');
        $('#'+elemento).select();
        $('#'+elemento).focus();
        return false;
    }
    peticion_ajax('?opcion='+maestro+'&a=traer_detalle&valor='+valor+'&condicion='+condicion+'&campo='+elemento,'','','buscar_registro_resultado', 'maestro_detalle_individual');
    return false;
    //peticion_ajax('?opcion='+maestro+'&a=traer_detalle&codigo_barras='+elemento.value,'','maestro_detalle_individual');
}
function buscar_registro_resultado(result, contenedor){
    var tabla = "<table>";
    var datos = result.split('&');
    for(i = 1; i < (datos.length-1); i++){
        linea = datos[i].split("=");
        tabla = tabla + "<tr>";
        tabla = tabla +"<td class='left_column'><label>"+linea[0]+"</label></td><td id='"+linea[0]+"'>"+ linea[1]+"</td>";
        tabla = tabla + "</tr>";
    }
    tabla = tabla +'<td colspan="2"><input type="number" id="nueva_cantidad" name="nueva_cantidad" placeholder="nueva cantidad">';
    tabla = tabla +'<input type="button" name="" id="" value="Guardar" title=""  onclick="guardar_inventario_item('+"'?opcion=producto_lectura&a=guardar_creacion'"+');"></td>';
    tabla = tabla + "</tr>";
    tabla = tabla + "</table>";
    $("#"+contenedor).html(tabla);
    //$('nueva_cantidad').focus();
    //background.setStyle('display', 'block');
    /*$('float_overlay').addEvent('click', function() {
        edit_inline_view();
    });*/
}
function edit_inline_view() {
    //var background = $('inline_background');
    //background.tween("right", "100", "-200");
    //tween('float_overlay', 'height', '0', 500);
    //tween('inline_background', 'right', '-3000px', 1000);
    //submenuSecciones('productos_inventario');
    //background.setStyle('display', 'none');
}
function guardar_inventario_item(url){
    var numeros = /^[0-9]{1,20}$/;
    var cantidad = $('#nueva_cantidad').val();
    if((!cantidad.match(numeros)) || cantidad === '' ){
        mostrar_alerta('html','La cantidad debe ser numérica');
        $('#nueva_cantidad').select();
        return false;
    }
    
    url = url + '&producto_id='+$('#id_producto').html();
    url = url + '&cantidad_anterior='+$('#cantidad').html();
    url = url + '&cantidad='+$('#nueva_cantidad').val();
    peticion_ajax(url, '', 'contenedor_grid');
    $('#nueva_cantidad').val('');
    $('#codigo_barras').val('');
    $('#codigo_barras').focus();
    $('#maestro_detalle_individual').html('');
}
function validar_email(){
    var email=$('#email').val();
    expr =/^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
    if ( !expr.test(email) )
        alert("Error: La dirección de correo " + email + " es incorrecta.");
}
function validarFiltro(element, seccion, subseccion){
	parametros=element.name+"="+element.value;
	peticion_ajax('?opcion=' + seccion + '&a=' + subseccion + '&type=ajax&' + parametros, '', 'area_reporte');
}