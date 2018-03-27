<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class WisGetOrder extends Modulo {
    public function __construct($db = "", $modulo = "", $ruta_cron = "", $modulo_info = null, $WISQueries = null, $appQry = null) {
        parent::__construct($db, $modulo, $ruta_cron, $modulo_info, $WISQueries, $appQry);
    }
}

