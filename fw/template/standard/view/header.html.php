<?php

class HeaderHTML {

    CONST CLASE_ADMINISTRADOR = 1;

    static function informacion_perfil() {
        ?>
        <div id="contenedor_ops_usuario" class="item_aside">
            <div id="flecha_info_perfil" class="clase_aside_flecha"></div>
            <div id="contenedor_opciones_usuario">
                <p id= "">OPCIONES DE USUARIO</p>
                <ul id="" class="">                     
                    <li id="usuario_cambio_clave">
                        <label class="vinieta_circulo"></label>Cambio de Clave
                    </li>
                    <li id="cerrar_sesion">
                        <label class="vinieta_circulo"></label>Salir   
                    </li>
                </ul>
            </div>
        </div>  
        <?php
    }

    static function home($sesion, $imagen_logo = '', $imagen_perfil, $usuario_nombre, $interlocutor_saldo, $clase, $perfil, $opciones, $tema, $marcablanca, $id_usuario) {
        ?>
            <?php if ($sesion) { ?>        
            <div class="navbar navbar-default ">
            <?php if ($sesion) { ?>
                    <div id="navbar_header" class="navbar-header">
                        <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
                            <span class="sr-only">Opciones Pricipales</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                <?php self::app_logo($marcablanca, $tema); ?>
                    </div>
                    <div  class="collapse navbar-collapse" id="navbar">
                        <ul id="user-bar" class="nav navbar-nav navbar-right alinea-bot">

                            <li class="dropdown">
                                <div class="img-user-perfil glyphicon glyphicon-user ">
                                    <?php if (file_exists('fw/themes/' . $tema['descripcion'] . '/img/big/' . $id_usuario . '.jpg')) { ?>
                                        <img id="imagen_perfil_header" width="50" height="50" src="fw/themes/<?php echo $tema['descripcion'] ?>/img/big/<?php echo $id_usuario; ?>.jpg" onerror="default_imagen('imagen_perfil_header','fw/themes/<?php echo $tema['descripcion'] ?>/img/default_perfil.png')">
                <?php } ?>
                                </div>
                                <div class="user-perfil">
                                    <p class="usuario-datos-nombre">
                                        <?php echo $usuario_nombre; ?>
                                    </p> 
                                    <p class="usuario-datos-perfil">
                <?php echo $perfil; ?>
                                    </p> 
                                </div>
                            </li>
                            <div class="visible-xs">
                                <?php foreach ($opciones as $key => $opcion) { ?>
                                    <li class="menu_opcion_header" onclick="traer_subopcion('subopcion', 'id',<?php echo $opcion['id_opcion'] ?>)" ><a><span><img width="30" height="30" src="fw/vista/img/opcion/big/<?php echo ($opcion['imagen']); ?>"></span> <?php echo ucwords(utf8_encode($opcion['descripcion'])); ?></a></li>
                                <?php } ?>
                            </div>
                        </ul>
                    </div><!--/.nav-collapse -->
                <?php
                } else {
                    self::app_login_header();
                }
                ?>
            </div>
            <script type="text/javascript">
                $(".menu_opcion_header").click(function (event) {
                    $(".navbar-collapse").collapse('hide');
                });
            </script>
            <?php } 
        }

        static function app_logo($marcablanca, $tema) {
            ?>
        <a href="javascript:;" onclick="goTo('?opcion=inicio');" onfocus="this.blur();" alt="web app <?php echo $tema['descripcion'] ?>"> 
        <?php if (file_exists('media/img/logo/big/' . $marcablanca . '.jpg')) { ?>
                <img id="imagen_logo_header" class="img-nav-logo" width="80" height="80" src="media/img/logo/big/<?php echo $marcablanca . '.jpg' ?>" onerror="default_imagen('imagen_logo_header','fw/themes/<?php echo $tema['descripcion'] ?>/img/default_logo.png');">
        <?php } ?>
        </a>
    <?php }

    static function app_login_header() {
        ?>
        <div>
            <form>
                <input type="text" id="usuario" name="usuario">
                <input type="password" id="clave" name="clave">
                <button>Iniciar</button>
            </form>
        </div>
    <?php
    }

}
