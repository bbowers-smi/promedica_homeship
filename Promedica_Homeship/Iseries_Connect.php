<?php

/** 
 * @author bbowers
 * 
 */
class Iseries_Connect {
	
	
	function __construct() {
	
	}
	
	public function getConnection($host,$user,$password){
		
		$conn = db2_connect($host,$user,$password);
		return $conn;
	}
}

?>