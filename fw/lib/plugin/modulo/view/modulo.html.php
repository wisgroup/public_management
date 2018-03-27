<?php

class ModuloHTML {
	
    static function titulo() {
        ?>
        <div class="container">
            <div id="miga_de_pan"> 
                <?php
                if ($this->sesion == true) {
                    require_once("fw/controlador/php/modulos/migadepan.php");
                    $mp = new MigadePan();
                    $mp->home($this->seccion, $this->accion);
                }
                ?>
            </div>
        </div> 
    <?php
    }
	static function pintar_titulo($modulo, $url_anterior) {
        ?>
        <h2 class='wis_modulo_titulo'>
			<a onclick="peticion_ajax('<?php echo $url_anterior; ?>&type=ajax', '', 'area_trabajo');" onfocus="this.blur();">
				<span class="glyphicon glyphicon-arrow-left" title="Atrás" ></span>
			</a>
			<?php if($modulo['titulo'] != 'subopcion'){ ?>
				<a onclick="traer_subopcion('subopcion', 'id',<?php echo $modulo['opcion_id']; ?>);">
					<span class="glyphicon glyphicon-menu-hamburger" title="Ir al Menú" ></span>
				</a>
				<a href="?opcion=<?php echo $modulo['titulo']; ?>&id=<?php echo $modulo['opcion_id']; ?>&type=html" target="_blank" onfocus="this.blur();">
					<span class="glyphicon glyphicon-new-window" title="Abrir en ventana nueva" ></span>
				</a>
			<?php
			}
			echo ucwords(utf8_encode($modulo['descripcion']));
			?>
		</h2>
    <?php
    }
	static function borrar_timeout($modulo) { ?>
			<script>
				borrarTimeOut('<?php echo $modulo; ?>');
			</script>
	<?php 
	}

}
