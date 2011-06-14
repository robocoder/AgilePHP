<?php
/**
 * @package com.makeabyte.agilephp.test.model
 */
class Tire extends DataModel {

    /** 
     * @var string The brand of tire
     */
    private $brand;
    /**
     * @var string The tire size
     */
    private $size;
    /**
     * @var string Where the tire is placed on the car
     */
    private $placement;
    /**
     * @var string The tread condition
     */
    private $tread;

    public function __construct($brand = null, $size = null, $placement = null, $tread = null) {

        $this->brand = $brand;
        $this->size = $size;
        $this->placement = $placement;
        $this->tread = $tread;
    }

    /**
     * Sets the brand of tire
     * 
     * @param string $brand The tire brand
     * @return void
     */
    public function setBrand($brand) {
        $this->brand = $brand;
    }

    /**
     * Gets the brand of tire
     * 
     * @return string The brand of tire
     */
    public function getBrand() {
        return $this->brand;
    }

    /**
     * Sets the size / dimensions of the tire
     * 
     * @param string $size The tire size
     * @return void
     */
    public function setSize($size) {
        $this->size = $size;
    }

    /**
     * Gets the tire size
     * 
     * @return string The tire size / dimensions
     */
    public function getSize() {
        return $this->size;
    }

    /**
     * Sets the tire placement on the car
     * 
     * @param string $placement The tire placement
     * @return void
     */
    public function setPlacement($placement) {
        $this->placement = $placement;
    }

    /**
     * Gets the placement of the tire on the car
     * 
     * @return string Where the tire is placed on the car
     */
    public function getPlacement() {
        return $this->placement;
    }

    /**
     * Sets the tread condition of the tire
     * 
     * @param string $tread The tread condition
     * @return void
     */
    public function setTread($tread) {
        $this->tread = $tread;
    }

    /**
     * Gets the tread condition of the tire
     * 
     * @return string The tread condition
     */
    public function getTread() {
        return $this->tread;
    }
}
?>