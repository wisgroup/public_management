function inicializar() {

	// $("#natural").change(function() {
	// 	$("#interlocutor_tipo_persona").val($("#natural").val());
	// 	$("#juridica").prop('checked',false);
	// });

	// $("#juridica").change(function() {
	// 	$("#interlocutor_tipo_persona").val($("#juridica").val());
	// 	$("#natural").prop('checked',false);
	// });

	$("#departamento").change(function() {
		id = $("#departamento").val();
  		peticion_ajax('?opcion=interlocutor&a=traer_municipio&id='+id,'','interlocutor_municipio_id');
	});	

	$("#boton_formulario_crear").click(function() {
		if(validar_formulario()){
			enviar_formulario('formulario_crear');	
 	  	}
	});


	$("#tipo_documento").change(function() {
		tipo = $("#tipo_documento").val();
		alert(tipo);	
		if (tipo =='Cedula') {
			$('#tipo_persona').val('Natural');
		}else{
			$('#tipo_persona').val('Juridica');
		}
		
	});	

}