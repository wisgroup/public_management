<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Class Tab {

    public $labels;
    public $tabContents;

    public function __construct() {
        $this->setContentExample();
    }

    function paintTab($labels = null, $tabContents = null) {

        if (!is_null($tabContents)) {
            $this->labels = $labels;
            $this->tabContents = $tabContents;
        }
        $tabStyles = $this->createTabStyles($this->labels);
        require_once('fw/lib/plugin/_inc/tab/view/tab.html.php');
        TabHTML::home($this->labels, $this->tabContents, $tabStyles);
    }

    function createTabStyles($labels) {
        $tabStyle = "";
        foreach ($labels as $key => $label) {
            $tabStyle .= "#tab" . $label['id_tab'] . ":checked ~ #content" . $label['id_tab'] . ", ";
        }
        $tabStyle = substr($tabStyle, 0, strlen($tabStyle) - 2);
        $tabStyle .= "{ display: block; }";
        return $tabStyle;
    }

    function setContentExample() {
        $this->labels = array('Codepen', 'Dribbble', 'Dropbox', 'Drupal');
        $this->tabContents = array(
            '<p>
                1) Bacon ipsum dolor sit amet beef venison beef ribs kielbasa. Sausage pig leberkas, t-bone sirloin shoulder bresaola. Frankfurter rump porchetta ham. Pork belly prosciutto brisket meatloaf short ribs.
			</p>
			<p>
				Brisket meatball turkey short loin boudin leberkas meatloaf chuck andouille pork loin pastrami spare ribs pancetta rump. Frankfurter corned beef beef tenderloin short loin meatloaf swine ground round venison.
			</p>',
            '<p>
				2) Bacon ipsum dolor sit amet beef venison beef ribs kielbasa. Sausage pig leberkas, t-bone sirloin shoulder bresaola. Frankfurter rump porchetta ham. Pork belly prosciutto brisket meatloaf short ribs.
			</p>
			<p>
				Brisket meatball turkey short loin boudin leberkas meatloaf chuck andouille pork loin pastrami spare ribs pancetta rump. Frankfurter corned beef beef tenderloin short loin meatloaf swine ground round venison.
			</p>',
            '<p>
				3) Bacon ipsum dolor sit amet beef venison beef ribs kielbasa. Sausage pig leberkas, t-bone sirloin shoulder bresaola. Frankfurter rump porchetta ham. Pork belly prosciutto brisket meatloaf short ribs.
			</p>
			<p>
				Brisket meatball turkey short loin boudin leberkas meatloaf chuck andouille pork loin pastrami spare ribs pancetta rump. Frankfurter corned beef beef tenderloin short loin meatloaf swine ground round venison.
			</p>',
            '<p>
				4) Bacon ipsum dolor sit amet beef venison beef ribs kielbasa. Sausage pig leberkas, t-bone sirloin shoulder bresaola. Frankfurter rump porchetta ham. Pork belly prosciutto brisket meatloaf short ribs.
			</p>
			<p>
				Brisket meatball turkey short loin boudin leberkas meatloaf chuck andouille pork loin pastrami spare ribs pancetta rump. Frankfurter corned beef beef tenderloin short loin meatloaf swine ground round venison.
			</p>');
    }

}
