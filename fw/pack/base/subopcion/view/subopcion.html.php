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
                        <a class = "item_subopcion" onclick="traer_subopcion('<?php echo $value['nombre_modulo'] ?>')" onfocus="this.blur();">
				<span class="<?php echo $value['icon'] ?>" title="Atrás" ></span>
                                <p class="titulo_subopcion"> <?php echo utf8_encode($value['descripcion']); ?></p> 
			</a>
                    <?php }
                }
                ?>
            </div>
        </div>
    <?php
    }

}
