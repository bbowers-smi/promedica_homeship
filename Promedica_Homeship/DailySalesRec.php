<?php
class DailySalesRec {
    
    private $patientname = "";
    private $patient_addr = "";
    private $patient_order = int;
    private $order_item = "";
    private $order_item_qty = int;
    private $order_item_uom = "";
    private $order_item_price = "";
    private $item_descr = "";
    private $orderer_name = "";
    
    
    function __construct(){
        
    }
	/**
     * @return the $patientname
     */
    public function getPatientname()
    {
        return $this->patientname;
    }

	/**
     * @return the $patient_addr
     */
    public function getPatient_addr()
    {
        return $this->patient_addr;
    }

	/**
     * @return the $patient_order
     */
    public function getPatient_order()
    {
        return $this->patient_order;
    }

	/**
     * @return the $order_item
     */
    public function getOrder_item()
    {
        return $this->order_item;
    }

	/**
     * @return the $order_item_qty
     */
    public function getOrder_item_qty()
    {
        return $this->order_item_qty;
    }

	/**
     * @return the $order_item_uom
     */
    public function getOrder_item_uom()
    {
        return $this->order_item_uom;
    }

	/**
     * @return the $order_item_price
     */
    public function getOrder_item_price()
    {
        return $this->order_item_price;
    }

	/**
     * @return the $item_descr
     */
    public function getItem_descr()
    {
        return $this->item_descr;
    }

	/**
     * @return the $orderer_name
     */
    public function getOrderer_name()
    {
        return $this->orderer_name;
    }

	/**
     * @param string $patientname
     */
    public function setPatientname($patientname)
    {
        $this->patientname = $patientname;
    }

	/**
     * @param string $patient_addr
     */
    public function setPatient_addr($patient_addr)
    {
        $this->patient_addr = $patient_addr;
    }

	/**
     * @param string $patient_order
     */
    public function setPatient_order($patient_order)
    {
        $this->patient_order = $patient_order;
    }

	/**
     * @param string $order_item
     */
    public function setOrder_item($order_item)
    {
        $this->order_item = $order_item;
    }

	/**
     * @param string $order_item_qty
     */
    public function setOrder_item_qty($order_item_qty)
    {
        $this->order_item_qty = $order_item_qty;
    }

	/**
     * @param string $order_item_uom
     */
    public function setOrder_item_uom($order_item_uom)
    {
        $this->order_item_uom = $order_item_uom;
    }

	/**
     * @param string $order_item_price
     */
    public function setOrder_item_price($order_item_price)
    {
        $this->order_item_price = $order_item_price;
    }

	/**
     * @param string $item_descr
     */
    public function setItem_descr($item_descr)
    {
        $this->item_descr = $item_descr;
    }

	/**
     * @param string $orderer_name
     */
    public function setOrderer_name($orderer_name)
    {
        $this->orderer_name = $orderer_name;
    }

}