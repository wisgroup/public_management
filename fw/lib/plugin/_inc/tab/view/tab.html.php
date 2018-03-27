<?php

class TabHTML {

	static function home($labels, $tabContents, $tabStyles) {
		?>
		<style>
			/* NOTE: The styles were added inline because Prefixfree needs access to your styles and they must be inlined if they are on local disk! */
			
			*, *:before, *:after {
				margin: 0;
				padding: 0;
				box-sizing: border-box;
			}

			html, body {
				height: 100%;
			}

			body {
				font: 14px/1 'Open Sans', sans-serif;
				color: #555;
				background: #eee;
			}

			h1 {
				padding: 50px 0;
				font-weight: 400;
				text-align: center;
			}

			p {
				margin: 0 0 20px;
				line-height: 1.5;
			}

			main {
				min-width: 320px;
				max-width: 800px;
				padding: 50px;
				margin: 0 auto;
				background: #fff;
			}

			.tab_section {
                            display: none;
                            padding: 20px 0 0;
                            border-top: 1px solid #ddd;
                            float: left;
			}

			input.tab_input {
				display: none;
			}

			.tab_label{
                            border-bottom: 2px solid #999 !important;
                            background: #eee none repeat scroll 0 0;
                            display: inline-block;
                            margin: 0 0 -1px;
                            padding: 10px 5px 10px 20px !important;
                            font-weight: 600;
                            text-align: center;
                            color: #bbb;
                            border-right: 1px solid #999;
                            float: left;
                            height: 50px;
                            position: relative;
                            line-height: 30px;
			}

			.tab_label:before {
				font-family: fontawesome;
				font-weight: normal;
				margin-right: 10px;
			}

			.tab_label[for*='100']:before {
				content: '\f1cb';
			}

			.tab_label[for*='200']:before {
				content: '\f17d';
			}

			.tab_label[for*='300']:before {
				content: '\f16b';
			}

			.tab_label[for*='400']:before {
				content: '\f1a9';
			}

			.tab_label:hover {
				color: #888;
				cursor: pointer;
			}

			input:checked + label {
				background: #fff none repeat scroll 0 0;
				color: #555;
				border: 1px solid #ddd;
				border-top: 2px solid orange;
				border-bottom: 1px solid #fff;
			}
			<?php 
				echo $tabStyles;
			?>
			.tab_plugin_container p.glyphicon {
                            margin: 0 !important;
                            padding: 0 8px 0 0;
                            float: left;
                            height: 66px;
                            position: absolute;
                            left: 0;
                            top: 30%;
			}
			@media screen and (max-width: 650px) {
				label {
					font-size: 0;
				}
				label p {
					font-size: 18px;
				}

				label:before {
					margin: 0;
					font-size: 18px;
				}
			}
			@media screen and (max-width: 400px) {
				label {
					padding: 15px;
				}
			}
		</style>

		
		<div id="tab_container" class="tab_plugin_container wis_bloque">
			<?php 
				foreach ($labels as $label) {
					self::paintTabLabel($label);
				}
				foreach ($tabContents as $content) {
					self::paintTabContentHTML($content);
				}
			?>
		</div>
		<?php
	}
	
	static function paintTabLabel($tabLabel){ ?>
		<input class="tab_input" id="tab<?php echo $tabLabel['id_tab']; ?>" type="radio" name="tabs" checked >
		<label class="tab_label" for="tab<?php echo utf8_encode($tabLabel['id_tab']); ?>"><p class="<?php echo $tabLabel['icon']; ?>"></p><?php echo utf8_encode(ucfirst($tabLabel['nombre'])); ?></label>
	<?php }
	static function paintTabContent($tabContent){ ?>
		<section id="content<?php echo $tabContent['id_tab']; ?>" class="tab_section">
			<?php echo $tabContent['contenido']; ?>
		</section>
	<?php }
	static function paintTabContentHTML($tabContent){ ?>
		<section id="content<?php echo $tabContent['id_tab']; ?>" class="tab_section">
			<?php 
				require_once ($tabContent['path']);
				$metodo = $tabContent['html_metodo'];
				$tabContent['html_clase']::$metodo($tabContent['productos']);
			?>
			
			<?php //echo $tabContent['contenido']; ?>
		</section>
	<?php }
}
