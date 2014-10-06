<?php

/** 
 * @author Brian Bowers
 * 
 */
class Iseries_DB {
	  
	private $_user = "";
	private $_password = "";
	
	function __construct($user,$password) {
		$this->_user = $user;
		$this->_password = $password;
	}
	
	public function getConnection(){
		
		$conn = db2_connect('10.1.2.2',$this->_user,$this->_password);
		return $conn;
	}
	
	
}

?>