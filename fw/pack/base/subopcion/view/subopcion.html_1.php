<?php

class SubopcionHTML {

    static function home($subopciones, $clase) {
        $imagen = 0;
        ?>
        <div class="centra-contenido">
            <div id="contenedor_submenu">
                <?php
                if (!empty($subopciones)) {
                    foreach ($subopciones as $key => $value) {
                        ?>
                        <a onclick="traer_subopcion('<?php echo $value['nombre_modulo'] ?>')" onfocus="this.blur();">
				<span class="glyphicon glyphicon-arrow-left" title="Atrás" ></span>
			</a>
                        <div class = "item_subopcion" onclick="traer_subopcion('<?php echo $value['nombre_modulo'] ?>')">   
                            <img  id="img_opcion_<?php echo $value['id_opcion'] ?>"  src="app/vista/img/opcion/big/<?php echo $value['imagen'] ?>" onmouseover="over_submenu(1, '<?php echo $value['id_opcion'] ?>', '<?php echo $value['imagen'] ?>', 'opcion', 'true')"  onmouseleave="over_submenu(0,'<?php echo $value['id_opcion'] ?>','<?php echo $value['imagen'] ?>','opcion', 'true')" alt="Logo" height="90" width="85" />
                            <p class="titulo_subopcion"> <?php echo utf8_encode($value['descripcion']); ?></p> 
                        </div>
                    <?php }
                }
                ?>
            </div>
        </div>
    <?php
    }

}
