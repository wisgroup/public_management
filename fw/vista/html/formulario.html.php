<?php


class FormularioHTML {

function pintar_formulario($formulario,$argumentos,$accion,$requeridos,$valores) { ?>
    <form id="<?php echo $formulario ?>" name="<?php echo $formulario ?>" action = "<?php echo $accion ?>">
        <?php foreach ($argumentos as $elementos) { ?>
            <?php foreach ($elementos as $tipo => $args) { ?>
                <?php switch($tipo) { 
                    case 'texto': 
                        foreach ($args as $identificador => $nombre) {?>
                            <div class="elemento_formulario">
                                <label><?php echo strtoupper($nombre) ?></label>
                                <input type="text" name="<?php echo $identificador ?>" title="<?php echo $nombre ?>" value="<?php echo array_key_exists($identificador, $valores)? $valores[$identificador] : ''?>" id="<?php echo $identificador ?>" class="<?php echo 'input_'.$tipo.' '; ?><?php echo in_array($identificador, $requeridos)?'requerido':''?>" ></input> 
                            </div>
                        <?php } ?>
                    <?php break; 
                    case 'email': 
                        foreach ($args as $identificador => $nombre) {?>
                            <div class="elemento_formulario">
                                <label><?php echo strtoupper($nombre) ?></label>
                                <input type="email" name="<?php echo $identificador ?>" id="<?php echo $identificador ?>" title="<?php echo $nombre ?>" value="<?php echo array_key_exists($identificador, $valores)? $valores[$identificador] : ''?>" class="<?php echo 'input_'.$tipo.' '; ?><?php echo in_array($identificador, $requeridos)?'requerido':''?>" ></input> 
                            </div>
                        <?php } ?>
                    <?php break; 
                   case 'radio' ?>
                    <?php foreach ($args as $identificador => $nombre) {?>
                        <div class="elemento_formulario">
                            <input type="hidden" id='<?php echo $identificador ?>' name='<?php echo $identificador ?>'></input>
                            <?php foreach ($nombre as $key => $value) {?>
                                <label><?php echo $value ?></label>
                                <input type='radio' value="<?php echo $key?>" id="<?php echo $identificador ?>" name="<?php echo $key ?>">
                            <?php } ?>
                        </div>
                    <?php } break;
                    case 'numero': 
                        foreach ($args as $identificador => $nombre) {?>
                            <div class="elemento_formulario">
                                <label><?php echo strtoupper($nombre) ?></label>
                                <input type="number" name="<?php echo $identificador ?>" title="<?php echo $nombre ?>" id="<?php echo $identificador ?>" value="<?php echo array_key_exists($identificador, $valores)? $valores[$identificador] : ''?>" class="<?php echo 'input_'.$tipo.' '; ?><?php echo in_array($identificador, $requeridos)?'requerido':''?>" ></input> 
                            </div>
                        <?php } ?>
                    <?php break;  
                    case 'seleccion' ?>
                        <div class="elemento_formulario">
                            <label><?php echo strtoupper($args[0][0])?></label>
                            <select id="<?php echo $args[0][1] ?>" name="<?php echo $args[0][1] ?>" class="<?php echo 'input_'.$tipo.' '; ?><?php echo in_array($args[0][1], $requeridos)?'requerido':'' ?>">
                                <option value="0" >Seleccione un elemento...</option>
                            <?php foreach ($args[1] as $id_opcion => $opcion) { ?>
                                <option value="<?php echo $id_opcion ?>" ><?php echo $opcion ?></option>
                            <?php } ?>
                            </select>
                        </div>
                    <?php break;
                    case 'teclado' ?>
                    <?php foreach ($args as $identificador => $nombre) {?>
                        <label><?php echo $nombre?></label>
                        <input type="password" name="<?php echo $identificador ?>" id="<?php echo $identificador ?>" class="<?php echo 'input_'.$tipo.' '; ?>keyboardInput" readonly></input> 
                    <?php } ?>
                    <?php break;
                    case 'oculto': 
                        foreach ($args as $identificador => $nombre) {?>
                            <input type="hidden" name="<?php echo $identificador ?>"  id="<?php echo $identificador ?>" value="<?php echo array_key_exists($identificador, $valores)? $valores[$identificador] : ''?>" class="<?php echo 'input_'.$tipo.' '; ?><?php echo in_array($identificador, $requeridos)?'requerido':''?>" ></input> 
                        <?php } ?>
                    <?php break;  
                    case 'no_editable': 
                        foreach ($args as $identificador => $nombre) {?>
                            <label><?php echo strtoupper($nombre); ?></label>
                            <input type="text" readonly="readonly" name="<?php echo $identificador ?>"  id="<?php echo $identificador ?>" value="<?php echo array_key_exists($identificador, $valores)? $valores[$identificador] : ''?>" class="no_editable <?php echo 'input_'.$tipo.' '; ?><?php echo in_array($identificador, $requeridos)?'requerido':''?>" onfocus="this.blur();"></input> 
                        <?php } ?>
                    <?php break;  

                 } ?> 
            <?php } ?>
       <?php } ?>     
       <button id="boton_enviar_<?php echo $formulario?>" type="button" name="boton_enviar_<?php echo $formulario?>" onfocus="this.blur();"></button>
       <button id="boton_cancelar_<?php echo $formulario?>" type="reset" name="boton_cancelar_<?php echo $formulario?>" onfocus="this.blur();"></button>
    </form>     
    <script>
        $('#<?php echo $formulario ?>').submit(function() {return false;});
        inicializar();
    </script>
    <?php }


    function pintar_herramientas($modulo,$herramientas) {   ?>
        
        <div id="menu_herramientas">

            <?php
                foreach ($herramientas as $key => $value) {
                    ?>
                    <div id="<?php echo  $value.'_herramientas'; ?>" class="herramientas" onmouseover="cambiar_imagen('<?php echo $value; ?>', 'herramientas', 1);" onmouseout="cambiar_imagen('<?php echo $value; ?>', 'herramientas', 0);" onclick="validar_herramienta('<?php echo $value ?>','<?php echo $modulo ?>');">    
                        <div id="<?php echo 'imagen_'.$value ?>" class="imagen_herramienta">
                            <img   src=" <?php  echo 'vista/img/producto/herramientas/'.$value.'.png'?>" id="<?php echo $value ?>" name="<?php $value ?>" widht="20px" height="20px" >
                        </div>
                        <div id=""  class="" ><?php echo strtoupper($value); ?></div>
                    </div>
                    <?php
                 } 
             ?>
        </div>
       
    <?php }
  
    function mostrar_alerta($mensaje,$titulo=''){ ?>
        <script type="text/javascript">mostrar_alerta('<?php echo $titulo; ?>','<?php echo $mensaje;?>'); </script>
        
    <?php }




}
?>