<?php
require_once 'fw/lib//plugin/_inc/fpdf181/fpdf.php';
class wis_pdf_js Extends FPDF{
    var $js;
    var $n_js;
    
    function includeJs($script){
        $this->js = $script;
    }
    
    function _putjavascript() {
        $this->_newobj();
        $this->n_js = $this->n;
        
        $this->_out('<<');
        $this->_out('/Names [(EmbeddedJS) '.($this->n+1).' 0 R]');
        $this->_out('>>');
        $this->_out('endobj');
        
        $this->_newobj();
        $this->_out('<<');
        $this->_out('/S /JavaScript');
        $this->_out('/JS '.$this->_textstring($this->js));
        $this->_out('>>');
        $this->_out('endobj');  
    }
    
    function _putresources() {
        parent::_putresources();
        if(!empty($this->js)){
            $this->_putjavascript();
        }
    }
    
    function _putcatalog() {
        parent::_putcatalog();
        if(!empty($this->js)){
            $this->_out('/Names <</Javascript'.$this->n_js.' 0 R>>');
        }
    }
}
