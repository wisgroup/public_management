<?php

class NavHTML {

    static function home($opciones) {
        ?>
        <div class="menu_opcion" id="menu_opcion_tools"  >
            <div id="nav_ocultar" class="menu_descripcion" onclick="menu_ocultar();">
                <span class="glyphicon glyphicon-arrow-left" title="Atrás"></span>
            </div>
            <div id="nav_mostrar" class="menu_descripcion" onclick="menu_mostrar();">
                <span class="glyphicon glyphicon-arrow-right" title="Atrás"></span>
            </div>
        </div>
        <div class="hidden-xs" >
        <?php foreach ($opciones as $key => $opcion) { ?>
                <div class="menu_opcion" id="menu_opcion_<?php echo $opcion['id_opcion'] ?>" onclick="validarItemMenu('subopcion', 'id',<?php echo $opcion['id_opcion'] ?>)"  onmouseover="over_submenu(1, '<?php echo $opcion['id_opcion'] ?>', '<?php echo $opcion['imagen'] ?>', 'opcion')"  onmouseleave="over_submenu(0,'<?php echo $opcion['id_opcion'] ?>','<?php echo $opcion['imagen'] ?>','opcion'); ">		
                    <div class="menu_descripcion">
            <?php echo utf8_encode($opcion['descripcion']); ?>
                    </div>
                </div>	
        <?php } ?>
            <div class="menu_opcion" id="menu_opcion_salir" onclick="cerrarSesionConfirmar();" >
                <div class="menu_descripcion">
                    Salir
                </div>
            </div>
            
        </div>
    <?php
    }

}
