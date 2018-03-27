<?php

class MigadePanHTML {

    function home($elementos, $seccion = 'inicio') {
        ?>
        <div id="contenedor_miga">
            <div class="miga_back elemento_miga" ></div>
            <?php foreach ($elementos as $indice => $value) { ?>
                <?php
                if ($indice == 0) {
                    $clase = "item_miga_activo";
                } else if ($indice == (count($elementos) - 1 )) {
                    $clase = "item_miga_inactivo";
                }
                ?>
                <div class="elemento_miga <?php echo $clase ?>" id="item_miga_<?php echo $indice; ?>" >	
                    <div id="texto_miga_<?php echo $indice; ?>" class="nombre_item" ><?php echo utf8_encode(strtoupper($value)); ?></div>
                </div>
        <?php } ?>
        </div>
    <?php
    }
}
