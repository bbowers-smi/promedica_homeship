<?php

/** 
 * @author bbowers
 * 
 */
class AccountDetails {
	
	private $salesmanNumber = "";
	private $chargeFreight = "";
	private $shipViaCode = "";
	private $shippingTerms = "";
	private $fobPoint = "";
	private $dftInvLoc = "";
	private $routes = array();
	private $stops = array();
	
	/**
	 */
	function __construct() {
	}
	/**
	 * @return the $salesmanNumber
	 */
	public function getSalesmanNumber() {
		return $this->salesmanNumber;
	}

	/**
	 * @return the $chargeFreight
	 */
	public function getChargeFreight() {
		return $this->chargeFreight;
	}

	/**
	 * @return the $shipViaCode
	 */
	public function getShipViaCode() {
		return $this->shipViaCode;
	}

	/**
	 * @return the $shippingTerms
	 */
	public function getShippingTerms() {
		return $this->shippingTerms;
	}

	/**
	 * @return the $fobPoint
	 */
	public function getFobPoint() {
		return $this->fobPoint;
	}

	/**
	 * @return the $dftInvLoc
	 */
	public function getDftInvLoc() {
		return $this->dftInvLoc;
	}

	/**
	 * @return the $routes
	 */
	public function getRoutes() {
		return $this->routes;
	}

	/**
	 * @return the $stops
	 */
	public function getStops() {
		return $this->stops;
	}

	/**
	 * @param string $salesmanNumber
	 */
	public function setSalesmanNumber($salesmanNumber) {
		$this->salesmanNumber = $salesmanNumber;
	}

	/**
	 * @param string $chargeFreight
	 */
	public function setChargeFreight($chargeFreight) {
		$this->chargeFreight = $chargeFreight;
	}

	/**
	 * @param string $shipViaCode
	 */
	public function setShipViaCode($shipViaCode) {
		$this->shipViaCode = $shipViaCode;
	}

	/**
	 * @param string $shippingTerms
	 */
	public function setShippingTerms($shippingTerms) {
		$this->shippingTerms = $shippingTerms;
	}

	/**
	 * @param string $fobPoint
	 */
	public function setFobPoint($fobPoint) {
		$this->fobPoint = $fobPoint;
	}

	/**
	 * @param string $dftInvLoc
	 */
	public function setDftInvLoc($dftInvLoc) {
		$this->dftInvLoc = $dftInvLoc;
	}

	/**
	 * @param multitype: $routes
	 */
	public function setRoutes($routes) {
		$this->routes = $routes;
	}

	/**
	 * @param multitype: $stops
	 */
	public function setStops($stops) {
		$this->stops = $stops;
	}

}

?>