<?php

class SoporteHTML {
     static function home($categoria) {
    ?>  
        <form action="?opcion=soporte&a=soporte_guardar" method="post" name="form_soporte" id="form_soporte" class="form_soporte">
            <div >
                <input  placeholder="Asunto" name="asunto" id="asunto" type="text"  value="" >
            </div>
            <div >
                <select name="categoria_id" id="categoria_id" type="text"  value="" >
                    <?php foreach ($categoria as $key => $cat){ ?>
                    <option value="<?php echo $cat['nombre'] ?>"><?php echo $cat['nombre'] ?></option>
                    <?php } ?>
                </select>    
            </div>
            <div id="texto_mensaje" class="form_row_center">
                <textarea  placeholder="mensaje" name="mensaje" id="mensaje"rows="3" cols="30" ></textarea>
            </div>
             <div >
                 <input name="archivo" id="archivo" type="file"  value="" >
            </div>
            <div > 
                <div class="form_row_center">  
                    <button name="enviar_contacto" id="enviar_contacto" type="sumit" onclick="" onfocus="this.blur();"></button>
                </div>
            </div>
            <div >  
                <div class="form_row_center"> 
                    <button name="cancelar_contacto" id="cancelar_contacto" type="reset" onfocus="this.blur();"></button>
                </div>
            </div>
        </form>
    <?php
    }
}
?>