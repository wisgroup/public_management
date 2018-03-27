<?php
require_once 'wis_pdf_js.php';
class wis_pdf Extends wis_pdf_js{
    
    function autoprint($dialog = false){
        $param = ($dialog ? 'true' : 'false');
        $script = "print($param);";
        $this->includeJs($script);
    }
    
    function autoprintToPrinter($server, $printer, $dialog = false){
        $script = "var pp = getPrintParams();";
        if($dialog){
            $script = " pp.interactive = pp.constants.interactionsLevel.full;";
        }else{
            $script = " pp.interactive = pp.constants.interactionsLevel.automatic;";
        }
        $script = " pp.printerName = '\\\\\\\\".$server."\\\\".$printer."';";
        $script = " print(pp);";
        $this->includeJs($script);
        
    }
}
