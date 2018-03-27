<?php

class AsideToolBarHTML {
    static function toolbar($subopciones, $modulo, $links) { ?>
       <div id="aside_toolbar" class="accordion_contenedor">
            <p class="clase_encabezado">ATAJOS</p>
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
                            <span class="subtitulo"><?php echo ucfirst($key); ?></span>
                        </h3>
                        <ul id="acc_contenedor_<?php echo $key; ?>" class="accordion_item_contenido <?php echo $clase; ?>">
                            <?php foreach ($opc as $k => $val){ 
                                 $parametros = explode(",", $links[$k]);
                                 
                                 if ( isset($parametros[1]) &&  $parametros[1]!='' && isset($parametros[2]) && $parametros[2]!='' ) {
                                     // print_r($parametros);
                                      ?>
                            <li id="tool_<?php echo $k; ?>" class="item_tool">
                                <span   onclick="<?php echo "traer_subopcion('".$parametros[0]."','".$parametros[1]."','".$parametros[2]."');"?>"><?php echo $val; ?></span>
                            </li>
                            <?php
                                 } else {
                                    ?>
                            <li id="tool_<?php echo $k; ?>" class="item_tool">
                                <span onclick="traer_subopcion('<?php echo $links[$k]; ?>');"><?php echo $val; ?></span>
                            </li>
                            <?php
                                     
                                 }
                                 
                                 } ?>
                        </ul>
                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php }
    
}
?>