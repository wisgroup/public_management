<?php

class FooterHTML {

    static function home() {
        ?>

        <div class="container hidden-xs">

            <p><b>MOSS</b>. Based on <b>WIS-Framework (<?php echo WIS_FW_DATA; ?>)</b> by WIS. Todos los derechos reservados
                <a href="http://www.wisgroup.com.co" target="_blank" onfocus="this.blur();" title="WIS:: Web Integral Services">
                    <img src="fw/template/_default/view/img/wis.png" alt="WIS" height="30" width="50">
                </a>
            </p>
            <input type="hidden" id="audiofile" value="media/sounds/bit1.mp3" />
            <audio id="myaudio"></audio>
        </div>
    <?php
    }

}
