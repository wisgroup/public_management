<?php

class OpcionClaseHTML {

    static function home($subopciones, $permiso, $tipo_negocio, $categorias,$modulo,$modulo1='', $titulo="Editar Permisos") {
        $padre = 0; 
        ?>
		<div id="contenedor_formulario" class="wis_bloque">
			<div class="titulo_maestro">
                <p><?php echo $titulo; ?></p>
            </div>
			<p class="titulo">Tipo de Negocio: <?php echo $modulo1; ?></p>
			<form action="?opcion=<?php echo $modulo; ?>&a=guardar_edicion" enctype="multipart/form-data" name="formulario_edicion" method="post" id="formulario_edicion" class="formulario_edicion"> 
				<input type='hidden' value='<?php echo $tipo_negocio ?>' name="tipo_negocio">
				<table style=""  cellpadding="5" class="admin_opciones opcion_submenu">
					<tr>
						<?php                    
						self::pintar_rama($padre, $subopciones, $permiso);
						?>
					</tr>
				</table>
				<div id="botones_accion" class="maestro_edit_form">
					<table>
						<tr>
							<td>
								<div id="accion_guardar" class="accion_boton">
									<button onfocus="this.blur()" type="submit" id="guardar" value="Guardar" class="guardar  btn btn-success" onclick="jLoad();">Guardar</button>
								</div>
							</td>
							<td>
								<div id="accion_cancelar" class="accion_boton">
									<button onfocus="this.blur()" type="reset" id="cancelar" value="Cancelar" class="cancelar btn btn-danger"  onclick="peticion_ajax('?opcion=<?php echo $modulo; ?>&type=ajax', '', 'area_trabajo');">Cancelar</button>
								</div>
							</td>
						</tr>
					</table>  
				</div>
			</form>
		</div>
        <?php
    }

    static function pintar_rama($padre, $subopciones, $permiso) {
        
        foreach ($subopciones[$padre] as $opcion) {
            if ($opcion['opcion_tipo_id'] == 1) {
				self::opcion_menu($opcion, $subopciones, $permiso);
            } else {
                self::opcion_item($opcion, $permiso);
            }
        }
    }

    static function opcion_menu($opcion, $opciones, $permiso) {
        ?>
        <tr class="opcion_menu">
            <td class="opcion_menu_titulo">
                <?php echo utf8_encode(ucfirst(strtolower($opcion['descripcion']))); ?>
            </td>
            <td class="opcion_submenu">
                <table class="opciones_rama">
                    <?php
                    if(isset($opciones[$opcion['id_opcion']])){
                        self::pintar_rama($opcion['id_opcion'], $opciones, $permiso);
                    }
                    ?>
                </table>
            </td>
        </tr>
        <?php
    }

    static function opcion_item($item, $permiso) {
        $checked = '';
        if (in_array($item['id_opcion'], $permiso)) {
        $checked = 'checked';
        }
        ?>
        <tr>
            <td class="opcion_item">
                <input id="opcion_<?php echo $item['id_opcion']; ?>" type="checkbox" name="opcion_<?php echo $item['id_opcion']; ?>" value="<?php echo $item['id_opcion']; ?>" <?php echo $checked ?> >
                <?php echo utf8_encode(ucfirst(strtolower($item['descripcion']))); ?>
            </td>

        </tr>
        <?php
    }

}
