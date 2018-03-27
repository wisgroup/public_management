<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of wis_fw_queries
 *
 * @author Soldier
 */
class AppControlQueries {
	private $queries = null;
	
	public function __construct($dataBase) {
		$this->dataBase = $dataBase;
		require_once 'queries.php';
		$this->queries = New AppQueries($this->dataBase);
	}
	function ejecutarQuery($query, $parametros){
		return $this->queries->$query($parametros);
	}
}
