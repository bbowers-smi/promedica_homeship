<?php

/** 
 * @author bbowers
 * 
 */
class Patient {
	
	private $patientid = "";
	private $p_firstname = "";
	private $p_lastname = "";
	private $p_address1 = "";
	private $p_address2 = "";
	private $p_city = "";
	private $p_state = "";
	private $p_zip = "";
	private $facility_name = "";
	private $f_address1 = "";
	private $f_address2 = "";
	private $f_city = "";
	private $f_state = "";
	private $f_zip = "";
	private $patient_billto = "";
	private $patient_status = "";
	
	/**
	 * @return the $patientid
	 */
	public function getPatientid() {
		return $this->patientid;
	}

	/**
	 * @return the $patient_billto
	 */
	public function getPatient_billto() {
		return $this->patient_billto;
	}

	/**
	 * @param string $patient_billto
	 */
	public function setPatient_billto($patient_billto) {
		$this->patient_billto = $patient_billto;
	}

	/**
	 * @return the $p_firstname
	 */
	public function getP_firstname() {
		return $this->p_firstname;
	}

	/**
	 * @return the $p_lastname
	 */
	public function getP_lastname() {
		return $this->p_lastname;
	}

	/**
	 * @return the $p_address1
	 */
	public function getP_address1() {
		return $this->p_address1;
	}

	/**
	 * @return the $p_address2
	 */
	public function getP_address2() {
		return $this->p_address2;
	}

	/**
	 * @param string $p_address2
	 */
	public function setP_address2($p_address2) {
		$this->p_address2 = $p_address2;
	}

	/**
	 * @return the $p_city
	 */
	public function getP_city() {
		return $this->p_city;
	}

	/**
	 * @return the $p_state
	 */
	public function getP_state() {
		return $this->p_state;
	}

	/**
	 * @return the $p_zip
	 */
	public function getP_zip() {
		return $this->p_zip;
	}

	/**
	 * @return the $facility_name
	 */
	public function getFacility_name() {
		return $this->facility_name;
	}

	/**
	 * @return the $f_address
	 */
	public function getF_address1() {
		return $this->f_address;
	}

	/**
	 * @return the $f_address2
	 */
	public function getF_address2() {
		return $this->f_address2;
	}

	/**
	 * @param string $f_address2
	 */
	public function setF_address2($f_address2) {
		$this->f_address2 = $f_address2;
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
	 * @param string $patientid
	 */
	public function setPatientid($patientid) {
		$this->patientid = $patientid;
	}

	/**
	 * @param string $p_firstname
	 */
	public function setP_firstname($p_firstname) {
		$this->p_firstname = $p_firstname;
	}

	/**
	 * @param string $p_lastname
	 */
	public function setP_lastname($p_lastname) {
		$this->p_lastname = $p_lastname;
	}

	/**
	 * @param string $p_address1
	 */
	public function setP_address1($p_address1) {
		$this->p_address1 = $p_address1;
	}

	/**
	 * @param string $p_city
	 */
	public function setP_city($p_city) {
		$this->p_city = $p_city;
	}

	/**
	 * @param string $p_state
	 */
	public function setP_state($p_state) {
		$this->p_state = $p_state;
	}

	/**
	 * @param string $p_zip
	 */
	public function setP_zip($p_zip) {
		$this->p_zip = $p_zip;
	}

	/**
	 * @param string $facility_name
	 */
	public function setFacility_name($facility_name) {
		$this->facility_name = $facility_name;
	}

	/**
	 * @param string $f_address
	 */
	public function setF_address1($f_address) {
		$this->f_address = $f_address;
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
	 */
	function __construct() {
	}
	/**
	 * @return the $patient_status
	 */
	public function getPatient_status() {
		return $this->patient_status;
	}

	/**
	 * @param string $patient_status
	 */
	public function setPatient_status($patient_status) {
		$this->patient_status = $patient_status;
	}

}

?>