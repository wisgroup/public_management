function peticion_ajax(url, parametros, nombre_div, nombre_funcion, argumentos, load) {
    if ((nombre_div != null) && (nombre_div !== 'area_trabajo' && nombre_div !== '')) {
        url = url + "&type=ajax_part";
    }
    if (load === null) {
        jLoad();
    }
    ;
    $.ajax({
        data: parametros,
        url: url,
        type: 'post',
        beforeSend: function () {
            //$("#resultado").html("Procesando, espere por favor...");
        },
        success: function (response) {
            if (load == null) {
                jClose();
            }
            ;
            var mensaje = response.replace("ERROR", '');

            if (response.substring(0, 5) == "ERROR") {
                mostrar_alerta('html', response.substring(5, response.length));
                //alert(response.substring(5,response.length));
                return;
            }
            if (response.indexOf("ERROR") >= 0) {
                mostrar_alerta('html', mensaje);
                return;
            }
            if (nombre_funcion != null) {
                //alert(nombre_funcion);
                eval(nombre_funcion)(response, argumentos);
                return;
            }
            if (nombre_div != null) {
                $("#" + nombre_div).html(response);
            }
            //   console.log(response);
        },
        error: function () {
            if (load == null) {
                jClose();
            }
            ;

        }
    });
}
timeOut = 0;
timeOutModulo = "";
function modulo_refrescar(modulo, repetir, intervalo, cargador, area) {
    //var area = 'area_trabajo';
    if (repetir) {
        timeOutModulo = modulo;
        timeOut = setTimeout(function () {
            peticion_ajax('?opcion=' + modulo + '&a=home&type=ajax', '', area, null, null, cargador);
        }, intervalo);
    } else {
        peticion_ajax('?opcion=' + modulo + '&a=home&type=ajax', '', area, null, null, cargador);
    }
}


function borrarTimeOut(modulo) {
    if (modulo !== timeOutModulo) {
        clearTimeout(timeOut);
        timeOutModulo = '';
        timeOut = 0;
    }
}

function enviar_formulario(formulario, mensaje_formulario, alerta, area) {

    jLoad();

    $.post($('#' + formulario).attr('action'), $("#" + formulario).serialize(), function (trama) {
    }).done(function (respuesta) {
        jClose();
        var mensaje = respuesta.replace("ERROR", '');

        if (respuesta.indexOf("ERROR") >= 0) {
            $('#' + formulario).each(function () {
                this.reset();
            });
            mostrar_alerta('', mensaje);

        } else {
            $("#" + area).html(respuesta);
            // alert(respuesta);
            if (mensaje_formulario == null || mensaje_formulario == '') {
                if (alert == null || alerta == true) {
                    mostrar_alerta('Exito', 'Transaccion Exitosa');
                }
                ;

            } else {
                mostrar_alerta('', mensaje_formulario);
            }
        }
    }).fail(function () {
        jClose();

    });
}



function enviar_formulario_ajax(formulario, mensaje_formulario, alerta, area) {

    $("#" + formulario).on("submit", function (e) {
        e.preventDefault();
        var f = $(this);
        var formData = new FormData(document.getElementById(formulario));
        /*formData.append("dato", "valor");*/

        $.ajax({
            url: $('#' + formulario).attr('action'),
            type: "post",
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        })
                .done(function (res) {
                    $("#" + area).html(res);
                });
    });

}

/*function traer_subopcion(modulo,id_argumento,id){   
 var complemento = "";
 if(id_argumento!=null){
 complemento = '&'+id_argumento+'='+id;
 }
 window.location = '?opcion='+modulo+complemento;
 } */

// function traer_subopcion(modulo,id_argumento,id){ 
//     // alert("modulo: "+modulo+" id_argumento: "+id_argumento+" id: "+id);
//     var complemento = "";

//     if( typeof id_argumento !== "undefined" && typeof id_argumento !=="undefined" ){
//         complemento = '&'+id_argumento+'='+id;
//     }
//     peticion_ajax('?opcion=migadepan&a=actualizar_miga_pan&modulo='+modulo+complemento+'&type=ajax', '', '', 'recargar_modulo');
// }

// function recargar_modulo(response){
//     // window.location = response;
//      peticion_ajax(response+'&type=ajax','', 'area_trabajo');
//      peticion_ajax('?opcion=migadepan&a=traer_miga_pan&type=ajax', '', '', 'traer_migapan');

// }
// function formatear_numero(id){
//     $('#'+id).number( true, 2 );
// }



function mostrar_alerta(titulo, contenido) {


    if (titulo == '' || titulo == null) {
        titulo = 'Alerta';

        var mensaje = contenido.split(';');
        if (mensaje.length == 2) {
            contenido = mensaje[1];
            titulo = mensaje[0];
        }
        ;
        jAlert(contenido, titulo);

    } else {

        jAlert(contenido, titulo);
    }
    ;

}


function cambiar_imagen(id, seccion, over) {
    if (over === 1) {
        $("#" + id).attr("src", 'fw/vista/img/producto/' + seccion + '/over/' + id + '.png');
    } else {
        $("#" + id).attr("src", 'fw/vista/img/producto/' + seccion + '/' + id + '.png');
    }
}



function seleccionar_contactos(modulo, grupo) {
    var contactos = '';

    $("input:checkbox[name=grid_check_" + modulo + "]:checked").each(function () {
        contactos = contactos + '|' + $(this).val();
    });
    cerrar_flotante();
    peticion_ajax('?opcion=' + modulo + '&a=contactos_seleccionados&contactos=' + contactos + '&id=' + grupo, '', 'area_trabajo');
}

function mostrar_contenido_grupo() {
    peticion_ajax('?opcion=grupo&a=mostrar_contenido_grupo', '', 'contenido_archivo');
}

function guardar_archivo_grupo(modulo) {
    cerrar_flotante();
    peticion_ajax('?opcion=' + modulo + '&a=guardar_archivo', '', 'area_trabajo');
}

function validar_herramienta(herramienta, modulo) {

    var files = 0;
    var id = '';
    var estado = '';

    $("input:checkbox[name=radio_edition]:checked").each(function () {
        files = files + 1;
        id = id + ' OR campo=' + $(this).val();
    });

    if (herramienta != 'nuevo' && id == '') {
        mostrar_alerta('', 'Por favor, Seleccione algun registro');
        return;
    }

    if (herramienta == 'nuevo') {
        peticion_ajax('?opcion=' + modulo + '&a=formulario_edicion', '', 'area_trabajo');
    }

    if (herramienta == 'editar') {

        if (files == 1) {// recordar problema al editar mas de 1 file
            id = id.substring(10);

            peticion_ajax('?opcion=' + modulo + '&a=formulario_edicion&id=' + id, '', 'area_trabajo');
        } else {
            mostrar_alerta('', 'Para Editar, seleccione solo un (1) registro');
            return;
        }

    }
    if (herramienta == 'estado') {
        if (files == 1) {
            id = id.substring(3);
            peticion_ajax('?opcion=' + modulo + '&a=cambiar_estado_active&id=' + id + "&tabla=" + modulo, '', 'area_trabajo');
        } else {
            mostrar_alerta('', 'Para Cambiar Estado, seleccione solo un (1) registro');
            return;
        }

    }
    if (herramienta == 'borrar') {
        jConfirm('¿Seguro  desea eliminar?', 'Confirmar', function (r) {
            if (r) {

                id = id.substring(3);
                // alert(id);
                peticion_ajax('?opcion=' + modulo + '&a=cambiar_estado&id=' + id, '', 'area_trabajo');
            }
            ;
        });

    }
}


function validar_herramienta_slave(herramienta, modulo) {

    var files = 0;
    var id = '';

    $("input:checkbox[name=radio_edition_slave]:checked").each(function () {
        files = files + 1;
        id = id + ' OR campo=' + $(this).val();
    });

    if (herramienta != 'nuevo' && id == '') {
        mostrar_alerta('', 'Por favor, Seleccione algun registro');
        return;
    }

    if (herramienta == 'nuevo') {
        peticion_ajax('?opcion=' + modulo + '&a=formulario_edicion', '', 'detalle_maestro');
    }

    if (herramienta == 'editar') {

        if (files == 1) {
            id = id.substring(10);

            peticion_ajax('?opcion=' + modulo + '&a=formulario_edicion&id=' + id, '', 'detalle_maestro');
        } else {
            mostrar_alerta('', 'Para Editar, seleccione solo un (1) registro');
            return;
        }

    }
    if (herramienta == 'borrar') {
        jConfirm('¿Seguro  desea eliminar?', 'Confirmar', function (r) {
            if (r) {

                id = id.substring(3);
                // alert(id);
                peticion_ajax('?opcion=' + modulo + '&a=cambiar_estado&id=' + id, '', 'detalle_maestro');
            }
            ;
        });

    }
}
function cargar_aside() {
    peticion_ajax("?opcion=aside&a=cargar_contenidos&type=ajax", "", "", "llenar_aside");
}

function llenar_aside(response) {
    var contenidos = response.split('|');
    for (x = 1; x < contenidos.length; x++) {

        peticion_ajax("?opcion=" + contenidos[x] + "&type=ajax", "", "acc_contenedor_" + contenidos[x]);
    }
}

/*ACCORDION COMRED*/
var mostrar_ops_perfil = 0;
var mostrar_a = 0;
$(document).ready(function () {

    $("#comred_accordion h3").click(function () {
        $("#comred_accordion ul .accordion_item_contenido").slideUp();
        if (!$(this).next().is(":visible")) {
            $(this).next().slideDown();
        }
    });

    $("#comred_accordion li").click(function () {
        if ($(this).hasClass('item_activo')) {
            $(this).removeClass('item_activo');
            $(this).addClass('item_inactivo');
        } else {
            $('#comred_accordion ul li').removeClass('item_activo');
            $('#comred_accordion ul li').addClass('item_inactivo');
            $(this).addClass('item_activo');
        }
    });

    $("#aside_toolbar h3").click(function () {
        $("#aside_toolbar ul .accordion_item_contenido").slideUp();
        if (!$(this).next().is(":visible")) {
            $(this).next().slideDown();
        }
    });

    $("#aside_toolbar li").click(function () {
        if ($(this).hasClass('item_activo')) {
            $(this).removeClass('item_activo');
            $(this).addClass('item_inactivo');
        } else {
            $('#aside_toolbar ul li').removeClass('item_activo');
            $('#aside_toolbar ul li').addClass('item_inactivo');
            $(this).addClass('item_activo');
        }
    });

    // cargar_aside();

    $("#informacion_perfil").click(function (e) {
        if (mostrar_ops_perfil == 0) {
            $('#contenedor_opciones_usuario').css({display: 'block'});
            $("#flecha_info_perfil").css({visibility: 'visible'});
            mostrar_ops_perfil = 1;
        } else {
            $('#contenedor_opciones_usuario').css({display: 'none'});
            $("#flecha_info_perfil").css({visibility: 'hidden'});
            mostrar_ops_perfil = 0;
        }

        e.stopPropagation();
    });


    $("#body").click(function () {

        if (mostrar_ops_perfil == 1) {
            $('#contenedor_opciones_usuario').css({display: 'none'});
            $("#flecha_info_perfil").css({visibility: 'hidden'});
            mostrar_ops_perfil = 0;
        }
    });

    $("#contenedor_opciones_usuario li").click(function () {

        $('#contenedor_opciones_usuario').css({display: 'none'});
        $("#flecha_info_perfil").css({visibility: 'hidden'});
        mostrar_ops_perfil = 0;

        var id = $(this).attr('id');
        if ($(this).attr('id') == 'cerrar_sesion') {

            jConfirm('¿Seguro  desea salir?', 'Confirmar', function (r) {
                if (r) {
                    jLoad();
                    window.location = '?opcion=inicio&a=' + id
                }
                ;
            });

        } else if ($(this).attr('id') == 'usuario_cambio_clave') {
            abrir_flotante('?opcion=usuario_cambio_clave');
        } else if ($(this).attr('id') == 'terminos_condiciones') {
            abrir_flotante('?opcion=usuario_cambio_clave&a=terminos_condiciones');
        }
    });

    /*MENU OPCIONES DE USUARIO FLOTANTE: OBSOLETO */
    $("#opciones_usuario li").click(function () {

        var id = $(this).attr('id');

        if ($(this).attr('id') == 'cerrar_sesion') {
            cerrarSesionConfirmar();
        } else if ($(this).attr('id') == 'usuario_cambio_clave') {
            abrir_flotante('?opcion=usuario_cambio_clave');
        } else if ($(this).attr('id') == 'terminos_condiciones') {
            abrir_flotante('?opcion=usuario_cambio_clave&a=terminos_condiciones');
        } else if ($(this).attr('id') == 'mi_cuenta') {
            abrir_flotante('?opcion=mi_perfil');
        }
    });
    //peticion_ajax('?opcion=migadepan&a=traer_miga_pan&type=ajax', '', '', 'traer_migapan');
});

function cerrarSesionConfirmar() {
    jConfirm('¿Seguro  desea salir?', 'Confirmar', function (r) {
        if (r) {
            jLoad();
            window.location = '?opcion=inicio&a=cerrar_sesion';
        }
    });
}

// /*ventana datos complentos transaccion*/
// function abrir_ventana_transaccion($id){  
//     $('#ventana_detalles_transaccion').fadeIn(800); 
//     peticion_ajax('?opcion=ultimas_transacciones&a=ver_mas_transaccion&id='+$id,'','ventana_detalles_transaccion');
// }
// function cerrar_detalles_transaccion(){
//     $('#ventana_detalles_transaccion').fadeOut(800); 
// }

// /*Ventana datos Promocion*/

// function abrir_ventana_promocion($id){  
//     $('#ventana_detalles_promocion').fadeIn(800); 
//     peticion_ajax('?opcion=promocion&a=ver_mas_promocion&id='+$id,'','ventana_detalles_promocion');
// }

// function cerrar_detalles_promocion(){
//     $('#ventana_detalles_promocion').fadeOut(800); 
// }


/***********************************************/
/*******************MIGA DE PAN*****************/
function traer_migapan(response) {

    $datos = response.split('|');
    // alert($datos);

    if (parseInt($datos[0]) !== 0) {
        $('#texto_miga_1').html($datos[0]);
        $('#item_miga_1').click(function () {
            //peticion_ajax('?opcion=migadepan&a=actualizar_miga_pan&modulo=subopcion&id='+$datos[2]+'&type=ajax', '', '', 'recargar_modulo');
        });
    }
    if ($datos[1] === 'NONE' || parseInt($datos[1]) === 0) {
        $('#item_miga_0').css("display", "none");

    } else {
        $('#item_miga_0').css("display", "block");
        $('#item_miga_0').click(function () {
            // window.location= "?opcion="+$datos[3];
            //peticion_ajax('?opcion=migadepan&a=actualizar_miga_pan&modulo='+$datos[3]+'&type=ajax', '', '', 'recargar_modulo');
        });
    }
    $('#texto_miga_0').html($datos[1]);
}


function validarCambioClave() {

    var clavevalida = /^[a-z0-9]{6,8}$/;
    var clave = $('#clave_actual').val();
    var clavenueva = $('#password').val();
    var claveconfirmacion = $('#password2').val();


    if (clave === '') {
        mostrar_alerta('Alerta', 'Debe ingresar la contrase&ntilde;a actual');
        return true;
    } else if ((!clave.match(clavevalida))) {
        mostrar_alerta('Alerta', 'La contrase&ntilde;a actual No cumple con las especificaciones.');
        return true;

    }
    ;


    if (clavenueva === '') {
        mostrar_alerta('Alerta', 'Debe ingresar la nueva contrase&ntilde;a');
        return true;
    } else if ((!clavenueva.match(clavevalida))) {
        mostrar_alerta('Alerta', 'La nueva contrase&ntilde;a  No cumple con las especificaciones.');
        return true;
    }
    ;

    if (claveconfirmacion === '') {
        mostrar_alerta('Alerta', 'Debe confirmar la nueva contrase&ntilde;a');
        return true;
    } else if ((!claveconfirmacion.match(clavevalida))) {
        mostrar_alerta('Alerta', 'La nueva contrase&ntilde;a  No cumple con las especificaciones.');
        return true;
    }
    ;

    if (clavenueva !== claveconfirmacion) {
        mostrar_alerta('Alerta', 'Las contrase&ntilde;as no concuerdan');
        return true;
    }
    ;

    $('#cambio_clave').submit();
    return true;
}

function cambiar_herramienta(funcion, modulo, elemento) {

    if (modulo === 'administracion') {
        window.location = funcion;
    } else {
        peticion_ajax(funcion, '', 'area_trabajo');
    }

    if (elemento != null) {
        $(elemento).css('z-index', '100')
    }
    // peticion_ajax('?opcion=migadepan&a=actualizar_miga_pan&modulo='+modulo+'&id=0&type=ajax', '', '', '');
}




function mostrar_fecha(calendario, campo, other) {
    if (other !== null) {
        if (calendario.checked) {
            calendario.checked = 0;
        } else {
            calendario.checked = 1;
        }
    }
    if (calendario.checked) {
        $("#" + campo).css('display', 'block');
    } else {
        $("#" + campo).css('display', 'none');
    }
    //$("#"+campo).attr('value', $('#'+calendario).val());
}


function abrir_flotante(url) {
    $('#flotante').css('display', 'block');

    $('#flotante_contenido').html('');
    if (!$("#flotante_btn_cerrar").length) {
        $('#flotante_body').append('<div id="flotante_btn_cerrar">X</div>')
        $('#flotante_btn_cerrar').click(function () {
            cerrar_flotante();
        });
    }
    peticion_ajax(url, '', 'flotante_contenido');

}
function cerrar_flotante() {
    $('#flotante').css('display', 'none');
}

$("#form_archivo_contactos").submit(function (e) {
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
        },
        error: function (jqXHR, textStatus, errorThrown) {
        }
    });
    e.preventDefault(); //Prevent Default action. 
    e.unbind();
});
//$("#form_archivo_contactos").submit(); //Submit the form


/*AYUDAS*/
var tooltip_ayuda = 0;
var tooltip_abierto = '';
var tooltip_anterior = 0;

function mostrar_ayuda(elemento) {
    var contenido = '<div id="ayuda_div" class"ayuda_flotante" >';
    contenido = contenido + '<div id="ayuda_cerrar" onclick="cerrar_ayuda(' + "'ayuda_div'" + ');">X</div>';
    contenido = contenido + '<span>' + copies_ayuda[elemento.id] + '</span>';
    contenido = contenido + '</div>';
    //$('.ayuda_descripcion').append(contenido);
    tooltip_abierto = elemento.id;
    if (tooltip_ayuda === 1) {
        cerrar_ayuda('ayuda_div');
        tooltip_ayuda = 0;
        if (tooltip_anterior !== tooltip_abierto) {
            $(elemento).append(contenido);
            tooltip_ayuda = 1;
            tooltip_anterior = tooltip_abierto;
        }
        tooltip_anterior = tooltip_abierto;
        tooltip_abierto = '';
    } else {
        $(elemento).append(contenido);
        tooltip_ayuda = 1;
        tooltip_anterior = tooltip_abierto;
    }
}
function cerrar_ayuda(elemento) {
    $('#' + elemento).remove();
}


var texto = '';
var msj_max = 159;



function contar_mensaje() {
    var contador = $('#contador').val();
    var mensaje = $('#mensaje').val();

    if (mensaje.length > (msj_max)) {
        $('#mensaje').val(mensaje.substring(0, (msj_max - 1)));
    }
    contador = msj_max - mensaje.length;
    $('#contador').val(contador);

    peticion_ajax('?opcion=inicio&a=validar_sms&sms=' + mensaje, '', '', 'validar_sms');
}


function contar_mensaje_campania() {
    var contador = $('#contador').val();
    var mensaje = $('#detallado_mensaje_id').val();

    if (mensaje.length > (msj_max)) {
        $('#detallado_mensaje_id').val(mensaje.substring(0, (msj_max - 1)));
    }

    contador = msj_max - mensaje.length;
    $('#contador').val(contador);
    peticion_ajax('?opcion=inicio&a=validar_sms&sms=' + mensaje, '', '', 'validar_sms');
}



function contar_mensaje_textarea(elemento) {

    var contador = $('#contador').val();
    var mensaje = elemento.value;

    if (mensaje.length > (msj_max)) {
        $('#detallado_mensaje_id').val(mensaje.substring(0, (msj_max - 1)));
    }

    contador = msj_max - mensaje.length;
    $('#contador').val(contador);
    peticion_ajax('?opcion=inicio&a=validar_sms&sms=' + mensaje, '', '', 'validar_sms');
}


function validar_sms(response) {

    var validacion = parseInt(response);
    if (validacion != 0) {
        mostrar_alerta('Alerta', response);

    }
}


function traer_detalle(nombre) {
    var id = document.getElementById(nombre + '_id').value;
    peticion_ajax('?opcion=inicio&a=traer_detalle&tabla=' + nombre + '&id=' + id, '', 'datos_detalle');
}

function confirmar_grupo(confirmar, origen) {
    $('#lista_confirmacion').css('display', 'none');

    if (origen == 'FILE') {

        if (confirmar == 1) {
            peticion_ajax('?opcion=grupo&a=mostrar_contenido_archivo', '', 'contenido_archivo');
        } else {
            $('#archivo_nombre').val('');
            $('#archivo').val('');

            peticion_ajax('?opcion=grupo&a=resetear_variables_edicion', '', '');

        }


    } else if (origen == 'DB') {

        if (confirmar == 1) {
            peticion_ajax('?opcion=grupo&a=mostrar_contenido_grupo', '', 'contenido_archivo');
        } else {
            $('#archivo_nombre').val('');
            $('#archivo').val('');
            peticion_ajax('?opcion=grupo&a=resetear_variables_edicion', '', '');
        }
    } else {
        $('#archivo_nombre').val('');
        $('#archivo').val('');
        peticion_ajax('?opcion=grupo&a=resetear_variables_edicion', '', '');
    }



}
function crear_contacto() {
    //if(progreso < 100){
    peticion_ajax('?opcion=grupo&a=crear_contacto', '', '', 'progress');
    //$('#progress_bar').css('with', (progreso*3));
    //}else{

    //}
}
function set_progreso() {
    //if(progreso < 100){
    peticion_ajax('?opcion=grupo&a=set_progreso', '', '');
    //}else{
    //}
}
function progress(progreso) {
    $('#progress_bar').css('width', (progreso * 3));
    $('#progress_percent').html(progreso + '%');
    if (progreso < 100) {
        crear_contacto();
    } else {
        cerrar_detalle();
    }
}
function cerrar_detalle() {
    $('#lista_confirmacion').css('display', 'none');
    peticion_ajax('?opcion=grupo&a=mostrar_contenido_archivo', '', 'contenido_archivo');
    set_progreso();
}

function conservar_campo(modulo, elemento) {

    peticion_ajax('?opcion=' + modulo + '&a=conservar_campo&campo=' + elemento.name + '&valor=' + elemento.value, '', '');
}

function habilitar_deshabilitar() {

    if ($('#origen_contactos').val() == 'FILE') {
        $('#fila_archivo').css('visibility', 'visible');
        $('#fila_contactos').css('visibility', 'collapse');

    } else if ($('#origen_contactos').val() == 'DB') {
        $('#fila_archivo').css('visibility', 'collapse');
        $('#fila_contactos').css('visibility', 'visible');

    } else {
        $('#fila_contactos').css('visibility', 'collapse');
        $('#fila_archivo').css('visibility', 'collapse');

    }

}


function SetDatetimepicker() {
    return{
        minDate: 0,
        dayOfWeekStart: 1,
        lang: 'es',
        step: 10
                /*minTime:'08:00',
                 maxTime:'20:40'*/
    }
}

function SetKeyboard() {

    return {
        display: {
            'bksp': "Borrar",
            'accept': 'Aceptar',
            'cancel': 'Cancelar',
        },
        lockInput: true
    };
}


function SetDatatable(titulo) {

    var encabezado = '';

    if (titulo != null) {
        encabezado = titulo;

    }

    return {
        "lengthMenu": [[20, 40, 80, -1], [20, 40, 80, "All"]],
        "jQueryUI": true,
        // "autoWidth": false,
        "sDom": 'T<"H"fl>rt<"F"ip>',
        // "sDom": 'T<"clear">lfrtip',
        "oTableTools": {
            // "sRowSelect": "single",
            "sRowSelect": "none",
            "sSwfPath": "fw/controlador/js/lib/TableTools/swf/copy_csv_xls_pdf.swf",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sFileName": "Reporte.xls",
                    "sToolTip": "Guardar como EXCEL",
                    "sButtonText": '<img src="fw/controlador/js/lib/TableTools/images/xls_hover.png">'
                },
                {
                    "sExtends": "pdf",
                    "sFileName": "Reporte.pdf",
                    "sToolTip": "Guardar como PDF",
                    "sPdfOrientation": "landscape",
                    "sTitle": titulo,
                    "sButtonText": '<img src="fw/controlador/js/lib/TableTools/images/pdf_hover.png">'
                }
            ]
        },
        "oLanguage": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "_START_ al _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "sPagination": "full_numbers",
            "oPaginate": {
                "sFirst": "<|",
                "sLast": "|>",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
    };

}

function SetDatatableReporte(titulo, file) {

    var encabezado = '';

    if (titulo != null) {
        encabezado = titulo;

    }

    return {
        "processing": true,
        "serverSide": true,
        "ajax": file,
        /* "ajax": {
         "url": "fw/controlador/js/lib/DataTables/scripts/server_processing.php",
         "data": {
         'parametros': parametros
         }
         },*/
        "jQueryUI": true,
        // "autoWidth": false,
        "sDom": 'T<"H"fl>rt<"F"ip>',
        // "sDom": 'T<"clear">lfrtip',
        "oTableTools": {
            // "sRowSelect": "single",
            "sRowSelect": "none",
            "sSwfPath": "fw/controlador/js/lib/TableTools/swf/copy_csv_xls_pdf.swf",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sFileName": "Reporte.xls",
                    "sToolTip": "Guardar como EXCEL",
                    "sButtonText": '<img src="fw/controlador/js/lib/TableTools/images/xls_hover.png">'
                },
                {
                    "sExtends": "pdf",
                    "sFileName": "Reporte.pdf",
                    "sToolTip": "Guardar como PDF",
                    "sPdfOrientation": "landscape",
                    "sTitle": titulo,
                    "sButtonText": '<img src="fw/controlador/js/lib/TableTools/images/pdf_hover.png">'
                }
            ]
        },
        "oLanguage": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "_START_ al _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "sPagination": "full_numbers",
            "oPaginate": {
                "sFirst": "<|",
                "sLast": "|>",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
    };

}

var detalle_seleccionado = '';
function mostrar_seleccion(elemento, id) {
    detalle_seleccionado = id;
    var value = elemento[elemento.selectedIndex].value;

    if (value == '0' || value == '') {
        // alert('true '+id);
        $('#' + id).attr('readonly', false);
    } else {
        // alert('false '+id);
        $('#' + id).attr('readonly', true);
    }
    ;

    peticion_ajax('?opcion=inicio&a=traer_elemento&tabla=mensaje&id=' + value, '', '', 'elemento_solicitado');
    //$('#'+id).html(elemento[elemento.selectedIndex].text);
}

function mostrar_seleccion_inicio(elemento, id) {

    // alert(elemento+' '+id);
    detalle_seleccionado = id;
    var value = $('#' + elemento).val();
    // alert(value+' '+elemento+' '+id);
    if (value == '0' || value == '') {
        // alert('true '+id);
        $('#' + id).attr('readonly', false);
    } else {
        // alert('false '+id);
        $('#' + id).attr('readonly', true);
    }
    ;
    // alert('?opcion=inicio&a=traer_elemento&tabla=mensaje&id='+value);

    peticion_ajax('?opcion=inicio&a=traer_elemento&tabla=mensaje&id=' + value, '', '', 'elemento_solicitado');
    //$('#'+id).html(elemento[elemento.selectedIndex].text);
}

var nombre_contacto = "nombre,Sebastian";

function elemento_solicitado(response) {
    // alert(response);
    $('#' + detalle_seleccionado).val($.trim(response));
    contar_mensaje_campania();

    if ($('#mezclar').prop('checked') === true) {
        obtener_contacto_grupo_edicion();
    }

}



function form_flotante(modulo) {
    abrir_flotante('?opcion=' + modulo + '&a=formulario_edicion&type=ajax');
    //peticion_ajax('?opcion='+modulo+'&a=formulario_edicion&type=ajax', '', '', 'flotante_contenido');
}
function validar_edicion_maestro(url) {
    peticion_ajax(url, '', '', 'respuesta_envio_flotante');
}

function respuesta_envio_flotante(url) {
    alert('Listo');
    cerrar_flotante();
}


function obtener_contacto_grupo(selected) {
    // alert(selected.value);
    var id = selected.value;
    $('#mezclar').prop('checked', false);
    mezclar_mensaje();
    peticion_ajax('?opcion=grupo&a=obtener_contacto_grupo&id=' + id, '', '', 'setear_contacto');
}

function obtener_contacto_grupo_edicion() {
    // alert(selected.value);
    var id = $('#grupo_id').val();
    // alert(id);
    peticion_ajax('?opcion=grupo&a=obtener_contacto_grupo&id=' + id, '', '', 'setear_contacto_edicion');
}

function setear_contacto_edicion(response) {
    nombre_contacto = response;
    var mensaje = $('#detallado_mensaje_id').val();

    var datos_mezcla_general = nombre_contacto.split("|");

    $.each(datos_mezcla_general, function (ind, elem) {

        var datos_mezcla = elem.split(",");
        var expr = new RegExp("\\[" + $.trim(datos_mezcla[0]) + "\\]", "gi");
        mensaje = mensaje.replace(expr, datos_mezcla[1]);

    });


    // mensaje = mensaje.replace('['+$.trim(datos_mezcla[0])+']',datos_mezcla[1]);

    $('#mensaje_ejemplo').html(mensaje);
    contar_mensaje_campania();
}

function setear_contacto(response) {
    // alert(response);
    nombre_contacto = response;
    // $('#mezclar').prop('checked',false);
    // mezclar_mensaje();
}

function mezclar_mensaje() {

    var mensaje = $('#detallado_mensaje_id').val();
    if ($('#mezclar').prop('checked') === true) {
        $('#mezclar').val('S');


        var datos_mezcla_general = nombre_contacto.split("|");

        $.each(datos_mezcla_general, function (ind, elem) {

            var datos_mezcla = elem.split(",");
            var expr = new RegExp("\\[" + $.trim(datos_mezcla[0]) + "\\]", "gi");
            mensaje = mensaje.replace(expr, datos_mezcla[1]);

        });

        $('#mensaje_ejemplo').html(mensaje);
    } else {
        $('#mezclar').val('N');
        $('#mensaje_ejemplo').html('');
    }
}



function cerrar_envio() {
    $('#lista_confirmacion').css('display', 'none');
    //set_progreso_envio();
}



/*Funciones Compras */
function seleccionar_proveedor(id) {
    $("#nombre_proveedor").val(id.value);
    peticion_ajax('?opcion=compras&a=seleccionar_costo_proveedor&id=' + id.value, '', '', 'setear_costo_mensaje');
}

function setear_costo_mensaje(costo) {

    var precio = parseFloat(costo);

    // alert("costo"+precio);

    var costo_formato = formatear_numero_valor(precio);
    // alert(costo_formato);
    $('#precio_venta').val(costo_formato);
    $('#valor_compra').val('');
    $('#unidades_compra').val('');


    calcular_unidades();





}


function formatear_numero(id) {
    // id.id
    $('#' + id.id).number(true, 2, ',', '.');
    // $('#'+id.id).number( true, 2);
}


// var campo_precio_venta = '';

function validar_costo_proveedor(costo, clase) {
    // alert(clase);
    // campo_precio_venta = costo;
    var costo_value = costo.value;
    peticion_ajax('?opcion=interlocutor&a=validar_costo_proveedor&id=' + costo_value + '&clase=' + clase, '', '', 'validar_costo_proveedor_ajax', costo);


}

function validar_costo_proveedor_ajax(response, campo_costo) {
    // alert(campo_costo);
    var valor = formatear_numero_valor(parseFloat(response));
    var costo = parseInt(response);

    if (costo != 0) {
        mostrar_alerta('', 'El precio de venta debe ser superior a $ ' + valor);
        // $("#precio_venta").val(" ");
        limpiar_campo(campo_costo);
        // campo_precio_venta='';
    }
    ;
}

function formatear_costo_proveedor(id) {
    // id.id
    // alert(id.value);
    if (id.value == '' || id.value == 0 || id.value == '0' || id.value == '0,00') {
        limpiar_campo(id);

    } else {
        $('#' + id.id).number(true, 2, ',', '.');

    }

}



function limpiar_campo(id) {
    // id.id
    // alert(id.value);
    id.value = '';
    // $('#'+id.id).value('');
}




function calcular_pasacupo(elemento) {

    var campo = elemento.id;

    var valor = $('#valor_pasacupo').val();
    var costo = $('#precio_venta').val();
    var unidades = $('#cantidad_mensajes').val();



    if (valor != '' && costo != '' && (campo == 'valor_pasacupo' || campo == 'precio_venta')) {  // Al cambiar el valor del pasacupo o el costo de cada mensaje
        $('#cantidad_mensajes').val(parseFloat(valor) / parseFloat(costo));

    } else if (valor != '' && unidades != '' && (campo == 'valor_pasacupo' || campo == 'cantidad_mensajes')) { //Al cambiar el valor del pasacupo o las unidades de mensaje
        var costo_por_mensaje = parseFloat(valor) / parseFloat(unidades);
        costo_por_mensaje = formatear_numero_valor(costo_por_mensaje);
        $('#precio_venta').val(costo_por_mensaje);

    } else if (unidades != '' && costo != '' && ((campo == 'cantidad_mensajes' || campo == 'precio_venta'))) {
        var costo_pasacupo = parseFloat(unidades) * parseFloat(costo);
        costo_pasacupo = formatear_numero_valor(costo_pasacupo);
        $('#valor_pasacupo').val(costo_pasacupo);
    }

}


function setear_pasacupo(elemento) {
    var valor = elemento.value;
    // alert(valor);
    peticion_ajax('?opcion=pasacupo&a=traer_valores&id=' + valor, '', '', 'setear_pasacupo_ajax');
    // peticion_ajax('?opcion=compras&a=seleccionar_costo_proveedor&id='+id.value,'','', 'setear_precio_venta');

}

function setear_pasacupo_ajax(response) {
    alert(response);

    var resultado = response.split('|');

    var valor = resultado[0];
    var unidades = resultado[1];

    alert(valor + ' ' + unidades);

    var costo_pasacupo = formatear_numero_valor($.trim(valor));
    $('#valor_pasacupo').val(costo_pasacupo);


    $('#cantidad_mensajes').val(unidades);
    // var valor = elemento.value;
    // alert(valor);
    // peticion_ajax('?opcion=pasacupo&a=traer_valores&id='+valor,'','setear_pasacupo_ajax');

}

/* Convierte un numero con formato ######.## a formato #.###,##*/
function formatear_numero_valor(cadenaAnalizar, decimales) {

// alert('cadenaAnalizar '+cadenaAnalizar);
    var tmp_cadena = String(cadenaAnalizar);
    var cadena = tmp_cadena.split(".");
    var valor = '';

    if (cadena.length == 2) {
        // valor = cadena[0]+','+cadena[1];
        valor = cadena[0];
    } else {
        valor = cadena;
    }
    ;

    valor = String(valor);
    var cadena_valor = '';
    var caracter = '';
    // alert("Valor String: "+valor.length+"VAlor: "+valor);

    for (var i = 1; i < valor.length + 1; i++) {

        caracter = valor.charAt(valor.length - i);
        // alert("caracter: "+caracter);

        if (((i - 1) % 3) == 0 && (i - 1) != 0) {
            cadena_valor = '.' + cadena_valor;
        }
        ;
        cadena_valor = caracter + cadena_valor;
    }



    // alert("Valor : "+cadena_valor);


    if (cadena.length == 2) {
        cadena_valor = cadena_valor + ',' + cadena[1];
    } else {

        if (decimales == null) {
            cadena_valor = cadena_valor + ',00';
        }

    }

    // alert(cadena_valor);
    return cadena_valor;
}



function traer_municipio(departamento) {
    var depart = departamento.value;
    peticion_ajax('?opcion=interlocutor&a=traer_municipio&id=' + depart, '', 'municipio_id');

}


function validar_email() {

    var email = $("#usuario_email").val();
    var c_email = $("#confirmar_email").val();

    if (email != '' && c_email != '' && email != c_email) {
        mostrar_alerta("Error", "El email no coincide");

        $("#usuario_email").val('');
        $("#confirmar_email").val('');
    }

}


function validar_email_usuario() {

    var email = document.getElementById('email').value;
    var confirmar_email = document.getElementById('confirmar_email').value;
    if (confirmar_email == '') {
        //var c_email = $("#confirmar_email").val(); //las lineas comentadas son para el caso del campo de confirmar email
        if (email == '') { //email!='' && c_email!='' && email!=c_email
            mostrar_alerta("Error", "El campo debe llenarse");
            $("#email").val('');
            //$("#confirmar_email").val('');
        }
    } else if (email != '' && confirmar_email != email) {
        mostrar_alerta("Error", "La confirmacion de email no coincide");
        $("#confirmar_email").val('');
    }
}


function validar_nickname() {
    var nickname = $("#usuario_nickname").val();
    peticion_ajax('?opcion=interlocutor&a=validar_nickname&id=' + nickname, '', '', 'validar_nickname_ajax');
}

function validar_nickname_ajax(response) {

    var nick = parseInt(response);
    if (nick == 1) {
        mostrar_alerta('', 'Nickname ya existe');
        $("#usuario_nickname").val('');
    }
    ;
}


function validar_nickname_usuario() {
    var nickname = $("#nickname").val();
    peticion_ajax('?opcion=usuario&a=validar_nickname&id=' + nickname, '', '', 'validar_nickname_usuario_ajax');

}

function validar_nickname_usuario_ajax(response) {
    var nick = parseInt(response);
    if (nick == 1) {
        mostrar_alerta('', 'Nickname ya existe');
        $("#nickname").val('');
    }
    ;

}


var colocar_unidades = '';
var monto_solicitud = '';


function calcular_unidades(interlocutor, id, elemento) {

    // alert(interlocutor+' '+id.value+' '+elemento.value);
    monto_solicitud = id;
    colocar_unidades = elemento;
    peticion_ajax('?opcion=inicio&a=calcular_unidades&interlocutor=' + interlocutor + '&monto=' + id.value, '', '', 'mostrar_unidades');
}


function mostrar_unidades(response) {
    // alert(colocar_unidades.id);
    // alert(response);
    var unidad = parseInt(response);


    if (unidad > 0) {
        colocar_unidades.value = $.trim(response);
        colocar_unidades = '';
    } else {
        mostrar_alerta('Alerta', 'El Valor a solicitar debe ser superior al costo por mensaje unitario');
        // colocar_unidades.value= '0';
        limpiar_campo(colocar_unidades);
        colocar_unidades = '';
        // monto_solicitud.value='0';
        limpiar_campo(monto_solicitud);
        monto_solicitud = '';
    }
    ;
}


function calcular_unidades_compra() {
    var valor = $('#valor_compra').val();
    var costo_mensaje = $('#precio_venta').val();

    // alert(parseFloat(valor)*parseFloat(costo_mensaje));
    // alert(parseInt(costo_mensaje));
    if (parseInt(costo_mensaje) > 0 && valor != '') {
        // $('#unidades_compra').val(parseFloat(valor)/parseFloat(costo_mensaje));

        var unidades = parseFloat(valor) / parseFloat(costo_mensaje);
        unidades = formatear_numero_valor(unidades, 'no_decimales');
        $('#unidades_compra').val(unidades);


    } else {
        $('#unidades_compra').val(0);
    }

}


function validar_creacion_grupo() {
    var campania = $('#archivo').val();

    if (campania == '') {
        mostrar_alerta('', 'Cargue un archivo o seleccione los destinatarios del grupo.');
        // $('#archivo').focus();
        return false;
    }
    ;

    $('#formulario_edicion_grupo').submit();
    return true;
}


function traer_tipo_persona(tipo_documento) {
    var documento = tipo_documento.value;
    if (documento == 'Cedula') {
        $('#tipo_persona').val('Natural');
    } else {
        $('#tipo_persona').val('Juridica');
    }
}

function traer_tipo_documento(tipo_persona) {
    var persona = tipo_persona.value;
    if (persona == 'Natural') {
        // $('#tipo_documento').val('Cedula');
    } else {
        $('#tipo_documento').val('Nit');
    }
}



function setear_comentario(estado) {
    var estado_selectd = estado.value;

    if (parseInt(estado_selectd) == 9) { //Si la solicitud es rechazada.
        // alert('rechazada');
        $('#observacion').attr('type', 'text');

    }
    ;

    // alert(estado_selectd);
    // alert("sasasas");
}


function get_form_camapania() {


    var parametros = '';

    $("#formulario_edicion").find(':input,textarea').each(function () {
        parametros = parametros + '&' + this.id + '=' + this.value;
    });

    // alert(parametros);
    peticion_ajax('?opcion=campania&a=setear_campos' + parametros, '', '');
}


// function prueba(response)
// {
//     alert(response);
// }
function traer_clientes(interlocutor) {
    var inter = interlocutor.value;
    peticion_ajax('?opcion=reporte_gasto&a=traer_clientes&id=' + inter, '', 'cliente');
}

function validar_file(argument, peso) {

    var file = argument.files[0];
    // alert(file.size+' '+peso );

    var kilobyte = 1024;
    var peso_maxio = parseInt(peso) / kilobyte;

    if (parseInt(file.size) > peso) {
        mostrar_alerta('', 'El peso del archivo debe ser inferior a ' + peso_maxio + ' KB');
        limpiar_file();
    }
    // var input = document.getElementById('archivo');
}

function limpiar_file(input) {
    var input = $("#archivo");
    input.replaceWith(input.val('').clone(true));
}
;

function agregar_caracteristica() {
    var x = document.getElementById("lista_caracteristicas").selectedIndex;
    var y = document.getElementById("lista_caracteristicas").options;
    var input_name = 'caracteristica_valor_' + y[x].id;
    var linea_id = 'linea_' + y[x].id;

    if (y[x].id == 0) {
        return false;
    }
    //contenido = $('#form_nuevos_campos').html();
    contenido = '<tr id="' + linea_id + '"><td><label id="label_' + linea_id + '">' + y[x].text + '</label></td>';
    contenido += '<td><input type="text" id="' + input_name + '" name="' + input_name + '" ></td>';
    contenido += '<td>' + "<input type='button' onclick='quitar_caracteristica(" + '"' + linea_id + '"' + ");' value='-'>" + '</td></tr>';
    //$('#form_nuevos_campos').html(contenido);
    $('#form_nuevos_campos').append(contenido);
    y[x].remove();
    return false;
}
function quitar_caracteristica(elemento) {
    datos = elemento.split('_');
    var texto = $('#label_' + elemento).html();
    //alert(texto)
    $('#lista_caracteristicas').append($('<option>', {
        id: datos[1],
        text: texto
    }));
    $('#' + elemento).remove();
}

var factura_detalle_items = 2;
function adicionar_factura_item(campos) {
    /*var item = '<tr>';
     for (x=0;x<campos.length;x++){
     item += '<td><span class="texto"><input type="text" id="'+campos[x]+'_'+factura_detalle_items+'" name="'+campos[x]+'_'+factura_detalle_items+'"></span></td>';
     }
     item += '</tr>';*/
    peticion_ajax('?opcion=transacciones&a=factura_item&item=' + factura_detalle_items, '', '', 'insertar_factura_item');

}
function insertar_factura_item(item) {
    $('#factura_detalle_tabla').append(item);
    factura_detalle_items++;
}
var dw_element_global = '';
var dw_item_global = '';
function mostrar_dw(dw_element, item, maestro, dw_campo) {
    dw_element_global = dw_element;
    dw_item_global = item;
    abrir_flotante('?opcion=transacciones&a=traer_datos_dw&maestro=' + maestro + '&dw_campo=' + dw_campo + '&dw_element=' + dw_element);
}

function dw_elemento_seleccionado(id, modulo, dw_element) {
    peticion_ajax('?opcion=transacciones&a=dw_seleccion&id=' + id + '&modulo=' + modulo + '&dw_element=' + dw_element + '&item=' + dw_item_global, '', '', 'dw_mostrar_seleccion');
    cerrar_flotante();
}
function dw_mostrar_seleccion(response) {
    var datos = response.split('&');
    var elemento = '';

    for (x = 0; x < datos.length; x++) {
        elemento = datos[x].split('=');
        if (x > 0) {
            $('#' + dw_element_global + '_' + elemento[0] + '_' + dw_item_global).html(elemento[1]);
        } else {
            //alert(dw_element_global+'_'+elemento[0].trim()+'_'+dw_item_global);
            $('input[name=' + dw_element_global + '_' + elemento[0].trim() + '_' + dw_item_global + ']').val(elemento[1]);
        }
    }
}
function procesar_formulario(form) {
    //alert('asdas');
    //$('#'+form).submit();
    //return true;
}

function teclado_capturar_tecla(tecla) {
    var valor = $('#colaborador_clave').val();
    $('#colaborador_clave').val(valor + '' + tecla);
}
function teclado_limpiar() {
    $('#colaborador_clave').val('');
}
function teclado_enviar(modulo) {
    var clave = $('#colaborador_clave').val();
    var documento = $('#colaborador_documento').val();
    var parametros = {clave: clave, documento: documento};
    peticion_ajax('?opcion=' + modulo + '&a=registrar', parametros, 'colaborador_ingreso_respuesta', 'respuesta_ajax');
}
function respuesta_ajax(response) {
    $('#colaborador_ingreso_respuesta').html(response);
    $('#colaborador_clave').val('');
    setInterval(function () {
        $('#colaborador_ingreso_respuesta').html('')
    }, 3000);
}
function colaborador_liquidar(id) {
    var parametros = {id: id};
    peticion_ajax('?opcion=colaborador_liquidacion&a=liquidar&id=' + id, parametros, '', 'respuesta_liquidar');
}
function respuesta_liquidar(response) {
    alert(response);
}
var item_detalle_actual = 1;

function adicionar_item_detalle() {
    var fila = "<tr>";

    for (var i = 1; i <= arguments.length; i++) {
        fila += '<td><input id="' + arguments[i - 1] + '_' + item_detalle_actual + '" name="' + arguments[i - 1] + '_' + item_detalle_actual + '" type="text" value=""></td>';
    }
    fila += "</tr>";
    item_detalle_actual++;
    $('#detalle_tabla tr:last').after(fila);
}


function asignar_preguntas(herramienta, modulo) {

    var files = 0;
    var id = '';

    $("input:checkbox[name=grid_check_" + modulo + "]:checked").each(function () {
        files = files + 1;
        id = id + ',' + $(this).val();
    });

    id = id.substring(1);

    /*if(herramienta != 'guardar' && id==''){
     mostrar_alerta('','Por favor, Seleccione algun registro');
     return;
     }*/

    if (herramienta == 'guardar') {
        peticion_ajax('?opcion=' + modulo + '&a=guardar_preguntas&id=' + id, '', 'detalle_maestro');
    }
}
function importarCSV(modulo) {
    peticion_ajax('?opcion=' + modulo + '&a=leer_archivo', '', '', 'importar_siguiente_pag');
}
function importar_siguiente_pag(response) {
    var respuesta = response.split(':');
    var modulo = respuesta[0];

    $('#respuesta_import').append('<tr><td>Insertadas ' + respuesta[1] + ' de ' + respuesta[2] + '</td></tr>');
    peticion_ajax('?opcion=' + modulo + '&a=import_file', '', '', 'importar_siguiente_pag');
}
function visualizar_img(input, img, _width, _height) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#' + img)
                    .attr('src', e.target.result)
                    .width(_width).height(_height);
        };

        reader.readAsDataURL(input.files[0]);
    }
}
function default_imagen(imagen, direccion) {
    var imag = $("#" + imagen);
    imag.attr('src', direccion);

}
function cambiar_estado(id, modulo, tabla) {
    var value = $('#' + id + '_select option:selected').val();
    peticion_ajax('?opcion=' + modulo + '&a=cambiar_estado_master&valor=' + value + '&modulo=' + modulo + '&tabla=' + tabla + '&id=' + id, '', 'area_trabajo');

}
function menu_ocultar() {
    $('#wis_app_contenido').removeClass('app_estado_normal');
    $('#menu').css('width', '0px');
    $('#nav_mostrar').css('left', '30px');
    $('#nav_ocultar').css('left', '-100px');
    $('#wis_modulo_titulo').css('padding-left', '200px');
}
function menu_mostrar() {
    $('#wis_app_contenido').addClass('app_estado_normal');
    $('#menu').css('width', '200px');
    $('#nav_mostrar').css('left', '-100px');
    $('#nav_ocultar').css('right', '220px');
    $('#wis_modulo_titulo').css('padding-left', '30px');

}
function formulario_edicion(id, modulo, padre) {
    var complemento = "";
    if ($('#radio_edition_slave_' + id).length > 0) {
        complemento = "&checked=" + $('#radio_edition_slave_' + id).is(':checked');
    }

    peticion_ajax('?opcion=' + modulo + '&a=guardar&id=' + id + complemento, '', '');
}
function receta_seleccion_ingrediente(ingrediente, modulo, receta) {
    if ($('#radio_edition_slave_' + ingrediente).is(':checked')) {
        $('#cantidad_' + ingrediente).prop('disabled', false);
    } else {
        $('#cantidad_' + ingrediente).prop('disabled', true);
    }

    formulario_edicion(ingrediente, modulo, receta);
}
function guardar_inline(element, tabla, modulo) {
    peticion_ajax('?opcion=' + modulo + '&a=guardar_inline&type=ajax&id=' + element.id + '&valor=' + element.value);
}
function iniciarCalculoMovimientos() {
    peticion_ajax('?opcion=producto_movimiento_inicial&a=iniciar_calculo&type=ajax_part', '', 'contenedor_reporte_avance_operacion', 'procesarRespuestaCalculoMovimientos', null, null);
}
function procesarRespuestaCalculoMovimientos(response) {
    if (response != false) {
        $("#contenedor_reporte_avance_operacion").html(response);
        peticion_ajax('?opcion=producto_movimiento_inicial&a=iniciar_calculo&type=ajax_part', '', 'contenedor_reporte_avance_operacion', 'procesarRespuestaCalculoMovimientos', null, null);
    } else {
        return "FIN";
    }
}
function regresarModuloReporte(modulo) {
    peticion_ajax('?opcion=' + modulo + '&type=ajax_part', '', 'area_reporte', null, null, null);
}
function mostrar_datos_detalle(modulo, id_elemento) {
    peticion_ajax('?opcion=' + modulo + '&a=traer_datos_detalle&id_elemento=' + id_elemento + '&type=ajax_part', '', 'area_reporte', 'procesarDatosDetalle', null, null);
}
function procesarDatosDetalle(response) {
    alert(response);
    var pre_producto = response.split("|");
    pre_producto.forEach(function (elemento) {
        datos = elemento.split("=");
        $("#detalle_" + datos[0]).html(datos[1]);
        if (datos[0] === "id") {
            $("#" + datos[0]).val(datos[1]);
        }
    }, this);
}
function guardarLecturaInventario(modulo) {
    var id = $('#id').val();
    peticion_ajax('?opcion=' + modulo + '&a=guardar_lectura&id=' + id + '&type=ajax_part', '', '', 'procesarRespuestaPeticion', null, null);
}
function procesarRespuestaPeticion(response) {
    //mostrar_alerta('html', response);
}
var currentFile = "";

function playAudio() {
    // Check for audio element support.
    if (window.HTMLAudioElement) {
        try {
            var oAudio = document.getElementById('myaudio');
            var btn = document.getElementById('play');
            var audioURL = document.getElementById('audiofile');

            //Skip loading if current file hasn't changed.
            if (audioURL.value !== currentFile) {
                oAudio.src = audioURL.value;
                currentFile = audioURL.value;
            }

            // Tests the paused attribute and set state. 
            if (oAudio.paused) {
                oAudio.play();
                btn.textContent = "Pause";
            } else {
                oAudio.pause();
                btn.textContent = "Play";
            }
        } catch (e) {
            // Fail silently but show in F12 developer tools console
            if (window.console && console.error("Error:" + e))
                ;
        }
    }
}

function playSoundAlert() {
    $("#fw_audio")[0].play();
}

function convertUnixToTime(timestamp) {

    // Unixtimestamp
    var unixtimestamp = timestamp;

    // Convert timestamp to milliseconds
    var date = new Date(unixtimestamp * 1000);

    // Hours
    var hours = Math.trunc(timestamp/3600);

    // Minutes
    var minutes = "0" + date.getMinutes();

    // Display date time in MM-dd-yyyy h:m:s format
    var convdataTime =  hours + ':' + minutes.substr(-2)+":00";

    return convdataTime;

}