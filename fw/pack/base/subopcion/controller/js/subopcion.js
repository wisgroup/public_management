var item_ancho = 145;
var item_visibles = 3;
var item_actual = 1;
var item_total = 10;
var circulo_guia = 145;
var guia_ancho = 100;
var elemento_actual = 1;

function calcular_dx(accion, elemento, clase) {
    if (clase != null) {
        item_total = $("." + clase).size();
    }
    var dx = $("#barrascroll").slider("value");
    var pmi = guia_ancho / (item_total - item_visibles);
    var elemento_actual = Math.round(dx / pmi);
    var total_dx = elemento_actual * item_ancho;
    var barra_dx = elemento_actual * pmi;
    console.log(total_dx + '::' + (item_total - item_visibles));
    if (elemento_actual >= 0 && elemento_actual < (item_total - 1)) {
        $("#" + elemento).animate({left: "-" + total_dx + "px"}, 500);
        $("#circulo_guia").animate({left: "+   " + barra_dx + "px"}, 500);
        console.log('entro');
        //$( "#barrascroll" ).slider( "value", pmi );
    }
}

function mover_lista(accion, elemento, clase) {
    item_total = item_visibles;

    // alert(item_actual);

    if (clase != null) {
        item_total = $("." + clase).size();
    }
    var tam_maximo = item_total * item_ancho;

    console.log(item_actual);
    if (accion == 1) {
        if (item_actual > 1) {
            $("#" + elemento).animate({"left": "+=" + item_ancho + "px"}, "slow");
            $("#circulo_guia").animate({"left": "-=" + item_ancho + "px"}, "slow");
            item_actual -= 1;
        }
    } else {
        if (item_actual < (item_total - item_visibles + 1)) {
            $("#" + elemento).animate({"left": "-=" + item_ancho + "px"}, "slow");
            $("#circulo_guia").animate({"left": "+=" + item_ancho + "px"}, "slow");
            item_actual += 1;
        }
    }
}

function over_submenu(accion, item, titulo, modulo, fx_aumento) {
    var width = $('#img_' + modulo + '_' + item).attr('width');
    var height = $('#img_' + modulo + '_' + item).attr('height');

    if (accion == 1) {
        //$('#img_'+modulo+'_'+item).attr("src", "vista/media/imagenes/"+modulo+"/hover/big/"+titulo);    
        width = width * 1.05;
        height = height * 1.05;
        //if(fx_aumento === 'true'){
        $('#img_' + modulo + '_' + item).animate({width: width, height: height}, {queue: false, duration: 0});
        //}
    } else {
        //$('#img_'+modulo+'_'+item).attr("src", "vista/media/imagenes/"+modulo+"/big/"+titulo);    
        width = (width / 105) * 100;
        height = (height / 105) * 100;
        //if(fx_aumento){
        $('#img_' + modulo + '_' + item).css({width: width, height: height});
        //}
    }
}


function traer_subopcion(modulo, id_argumento, id) {
    //alert("modulo: "+modulo+" id_argumento: "+id_argumento+" id: "+id);
    var complemento = "";
    item_actual = 1;
    if (typeof id_argumento !== "undefined" && typeof id_argumento !== "undefined") {
        complemento = '&' + id_argumento + '=' + id;
    }
    
    $('.menu_opcion').click(
        function () {
            clearTimeout(t);
        }
    );
    t = setTimeout(
            function () {
                //TODO: Verificar para que es este timer y habilitar 28/04/2016
                //traer_subopcion(modulo);
            }
        , 60000);

    peticion_ajax('?opcion=' + modulo + complemento + '&type=ajax', '', 'area_trabajo');
    //peticion_ajax('?opcion=migadepan&a=traer_miga_pan&type=ajax', '', '', 'traer_migapan');
}
var menu_seleccionado = '';
function validarItemMenu(modulo, id_argumento, id) {
    if (menu_seleccionado != '') {
        $('#menu_opcion_' + menu_seleccionado).removeClass('menu_opcion_selected');
    }
    menu_seleccionado = id;
    traer_subopcion(modulo, id_argumento, id);
    $('#menu_opcion_' + menu_seleccionado).addClass('menu_opcion_selected');
}
function recargar_modulo(response) {
    // window.location = response;
    peticion_ajax(response + '&type=ajax', '', 'area_trabajo');
    //peticion_ajax('?opcion=migadepan&a=traer_miga_pan&type=ajax', '', '', 'traer_migapan');
    //peticion_ajax('?opcion=inicio&a=obtener_saldo&type=ajax', '', 'titulo_saldo');
}
function pintar_miga_pan(modulo, c){
    
}