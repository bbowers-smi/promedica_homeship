<?php

/** 
 * @author bbowers
 * 
 */
class Nurses {
	
	private $nurseid = "";
	private $n_firstname = "";
	private $n_lastname = "";
	private $n_address1 = "";
	private $n_address2 = "";
	private $n_city = "";
	private $n_state = "";
	private $n_zip = "";
	private $n_phone = "";
	private $facility_name = "";
	private $f_address1 = "";
	private $f_address2 = "";
	private $f_city = "";
	private $f_state = "";
	private $f_zip = "";
	private $nurse_billto = "";
	private $nurse_status = "";
	
	function __construct() {
	}
	/**
	 * @return the $nurseid
	 */
	public function getNurseid() {
		return $this->nurseid;
	}

	/**
	 * @return the $n_firstname
	 */
	public function getN_firstname() {
		return $this->n_firstname;
	}

	/**
	 * @return the $n_lastname
	 */
	public function getN_lastname() {
		return $this->n_lastname;
	}

	/**
	 * @return the $n_address1
	 */
	public function getN_address1() {
		return $this->n_address1;
	}

	/**
	 * @return the $n_address2
	 */
	public function getN_address2() {
		return $this->n_address2;
	}

	/**
	 * @return the $n_city
	 */
	public function getN_city() {
		return $this->n_city;
	}

	/**
	 * @return the $n_state
	 */
	public function getN_state() {
		return $this->n_state;
	}

	/**
	 * @return the $n_zip
	 */
	public function getN_zip() {
		return $this->n_zip;
	}

	/**
	 * @param string $nurseid
	 */
	public function setNurseid($nurseid) {
		$this->nurseid = $nurseid;
	}

	/**
	 * @param string $n_firstname
	 */
	public function setN_firstname($n_firstname) {
		$this->n_firstname = $n_firstname;
	}

	/**
	 * @param string $n_lastname
	 */
	public function setN_lastname($n_lastname) {
		$this->n_lastname = $n_lastname;
	}

	/**
	 * @param string $n_address1
	 */
	public function setN_address1($n_address1) {
		$this->n_address1 = $n_address1;
	}

	/**
	 * @param string $n_address2
	 */
	public function setN_address2($n_address2) {
		$this->n_address2 = $n_address2;
	}

	/**
	 * @param string $n_city
	 */
	public function setN_city($n_city) {
		$this->n_city = $n_city;
	}

	/**
	 * @param string $n_state
	 */
	public function setN_state($n_state) {
		$this->n_state = $n_state;
	}

	/**
	 * @param string $n_zip
	 */
	public function setN_zip($n_zip) {
		$this->n_zip = $n_zip;
	}
	/**
	 * @return the $n_phone
	 */
	public function getN_phone() {
		return $this->n_phone;
	}

	/**
	 * @param string $n_phone
	 */
	public function setN_phone($n_phone) {
		$this->n_phone = $n_phone;
	}
	/**
	 * @return the $facility_name
	 */
	public function getFacility_name() {
		return $this->facility_name;
	}

	/**
	 * @return the $f_address1
	 */
	public function getF_address1() {
		return $this->f_address1;
	}

	/**
	 * @return the $f_address2
	 */
	public function getF_address2() {
		return $this->f_address2;
	}

	/**
	 * @return the $f_city
	 */
	public function getF_city() {
		return $this->f_city;
	}

	/**
	 * @return the $f_state
	 */
	public function getF_state() {
		return $this->f_state;
	}

	/**
	 * @return the $f_zip
	 */
	public function getF_zip() {
		return $this->f_zip;
	}

	/**
	 * @return the $nurse_billto
	 */
	public function getNurse_billto() {
		return $this->nurse_billto;
	}

	/**
	 * @return the $nurse_status
	 */
	public function getNurse_status() {
		return $this->nurse_status;
	}

	/**
	 * @param string $facility_name
	 */
	public function setFacility_name($facility_name) {
		$this->facility_name = $facility_name;
	}

	/**
	 * @param string $f_address1
	 */
	public function setF_address1($f_address1) {
		$this->f_address1 = $f_address1;
	}

	/**
	 * @param string $f_address2
	 */
	public function setF_address2($f_address2) {
		$this->f_address2 = $f_address2;
	}

	/**
	 * @param string $f_city
	 */
	public function setF_city($f_city) {
		$this->f_city = $f_city;
	}

	/**
	 * @param string $f_state
	 */
	public function setF_state($f_state) {
		$this->f_state = $f_state;
	}

	/**
	 * @param string $f_zip
	 */
	public function setF_zip($f_zip) {
		$this->f_zip = $f_zip;
	}

	/**
	 * @param string $nurse_billto
	 */
	public function setNurse_billto($nurse_billto) {
		$this->nurse_billto = $nurse_billto;
	}

	/**
	 * @param string $nurse_status
	 */
	public function setNurse_status($nurse_status) {
		$this->nurse_status = $nurse_status;
	}



	
	
}

?>