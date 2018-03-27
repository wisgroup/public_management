<?php

class AsideHTML {
    static function informacion_adicional($subopciones) { ?>
       <div id="comred_accordion" class="accordion_contenedor">
            <p class="clase_encabezado">DESTACADOS</p>
            <ul>
                <?php foreach ($subopciones as $key => $opc){
                    if(isset($opc['selected']) && $opc['selected'] === true){   
                        $clase = 'activo'; 
                    }else{
                        $clase = 'inactivo'; 
                    }
                   ?>
                                 
                    <li id="item_uno" class="accordion_item item_<?php echo $clase; ?>">
                        <h3 class="subtitulo_accordion">
                            <label class="accordion_vinheta"></label>
                            <span class="subtitulo"><?php echo $opc['descripcion']; ?></span>
                        </h3>
                        <div id="acc_contenedor_<?php echo $opc['titulo']; ?>" class="accordion_item_contenido <?php echo $clase; ?>">
                        
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php }
    
}
?>