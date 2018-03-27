<?php

class Footer {
	function __construct() {
		require_once 'fw/template/standard/view/footer.html.php';
		FooterHTML::home();
	}
}