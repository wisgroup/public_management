<?

class ClaseHTML {

    static function submenu($clase, $dato = NULL) {
        ?>
            <h2 class="interna_titulo"><? echo strtoupper($dato->nombre); ?></h2>
            <p><? echo $dato->descripcion; ?></p>
            <div class="clearfix"></div>
        <?
    }

}
?>
