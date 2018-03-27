<?php

class InicioHTML {

    static function home() {
        ?>
        <p>Exito</p> 
        <?php
    }

    static function detalle($datos, $mensaje) {
        ?>
        <table>
            <tr>
                <td>
                    Campa&ntilde;a:
                </td>
                <td>
                    <?php echo utf8_encode($datos['nombre_campania']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    Grupo:
                </td>
                <td>
                    <?php echo utf8_encode($datos['grupo']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    Mensaje:
                </td>
                <td>
                    <!--<?php echo utf8_encode(str_replace("[nombre]", trim(ucwords($contacto)), $datos['mensaje_contenido'])); ?>-->
                    <?php echo utf8_encode($mensaje); ?>
                </td>
            </tr>
        </table>
        <?php
    }
    static function bienvenido() {
        ?>
        <div id="wis_bienvenido" class="wis-shadow1">
            <div class="wis_info_container">
                 <img src="fw/template/_default/view/img/banner_facebook.jpg" alt="WIS :: Bienvenido" height="100%" width="100%"> 
            </div>
            <div id="container-login-start" class="gradient-pattern-one ">
                <?php 
                    self::formulario_login(); 
                    //self::formulario_login_complemento(); 
                ?>
            </div>
        </div>
        <?php
    }
    static function formulario_login(){
        ?>
        <div id="login-form" class="wis-separador-derecho1">
            <form id="formulario_login" class="wis-separador-derecho2" name="formulario_login" action="?opcion=inicio&a=procesar_login" method="post" autocomplete="off">
                <div id="login_imagen">
                    <div id="imagen_producto">
                        <!-- <img id="img_logo_producto" src="vista/imagenes/LogoSMS.png" alt="Logo" height="80" width="100" />
                         <p>MOSS</p>-->
                    </div>
                    <div id="img_logo">
                        <img   src="fw/template/_default/view/img/logo.png" alt="Logo" height="100%" width="100%" />
                    </div>
                </div>
                <div id="contenedor_login" class="wis-shadow1" >
                    <div id="campos_login">
                        <h5>Ingreso de Usuarios</h5>
                    </div>
                    <div id="campos_login">
                        <input type="text" name="usuario" id="usuario" class="requerido" placeholder="Usuario"> 
                    </div>
                    <div id="campos_login">
                        <input type="password" name="password" id="password" placeholder="Clave" > 
                        <!-- <input type="password" name="password" id="password" class="requerido keyboardInput"  required>  -->
                    </div>
                    <div id="boton_login">
                        <div class="button_item">
                            <button id="boton_formulario_login" name="boton_formulario_login" onfocus="this.blur();" onclick="validar_login()" >Aceptar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <script type="text/javascript">
            //$('#password').keyboard(SetKeyboard());
        </script>
        <?php
    }
    static function formulario_login_complemento() {
        ?>
        <div id="login-form-complemento">
            
        </div>
        <?php
    }
}
