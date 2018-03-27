<?php

class UsuarioDatosHTML {

    function home($datos_usuario) { ?>
        <div id="contenedor_datos_usuario" class="datos_usuario_item"> 
            <h4>
                DATOS DE USUARIO
            </h4>
            <table>
            <?php
                foreach ($datos_usuario as $key => $opc) {
                ?>
                <tr>
                    <td><b><?php echo $key;?> </b></td><td>   </td><td> <?php echo $opc; ?></td>
                </tr>
            <?php
            } ?>
            </table>
        </div> 
   <?php } 
}
?>