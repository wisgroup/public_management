<?php

class DetalleHTML{

	static function form_detalle(){
		?>
		<fieldset id="contenedor_formulario">
			<legend class="clase_titulo titulo_form"><p><?php echo $titulo; ?></p></legend>
			<div id="form_dinamico_contenedor">
				<form action="<?php echo '?opcion=' . $modulo . '&a=' . $accion; ?>" enctype="multipart/form-data" name="formulario_edicion" method="post" id="formulario_edicion" class="formulario_edicion" autocomplete="off"> 
					<div id="contenedor_elemento" class="maestro_edit_form">
						<table>

						</table>
					</div> 
				</form> 
			</div>
		</fieldset>
		<script type="text/javascript">
			$("#formulario_edicion").validate({lang: 'es'});
			if (typeof inicializar == 'function') {
				inicializar();
			}
		</script>
		<?php
	}
}