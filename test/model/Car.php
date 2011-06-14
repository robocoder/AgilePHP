<?php
/**
 * @package com.makeabyte.agilephp.test.model
 */
class Car extends DataModel {

    /**
     * @var int $year The year the car was manufactured
     */
    private $year;
    /**
     * @var string $make The make of the car
     */
    private $make;
    /**
     * @var string $model The model of car
     */
    private $model;
    /**
     * @var string $color The color of the car
     */
    private $color;
    /**
     * @var boolean $isNew True if the car is new, false otherwise
     */
    private $isNew;
    /**
     * @var boolean $isCheap True if the car is cheap, false otherwise
     */
    private $isCheap;
    /**
     * @var string $leasedUtil The date when the lease is up
     */
    private $leasedUntil;
    /**
     * @var Owner $owner The owner of the car
     */
    private $owner;
    /**
     * @var array<Tire> $tires List of tires that belong to the car
     */
    private $tires;

    public function __construct($year = null, $make = null, $model = null, $color = null,
             $isNew = null, $isCheap = null, $leasedUtil = null, Owner $owner = null, array $tires = array()) {

        $this->year = $year;
        $this->make = $make;
        $this->model = $model;
        $this->color = $color;
        $this->isNew = $isNew;
        $this->isCheap = $isCheap;
        $this->leasedUtil = $leasedUtil;
        $this->owner = $owner;
        $this->tires = $tires;
    }

    /**
     * Sets the year the car was manufactured
     * 
     * @param int $year The year of the car
     * @return void
     */
    public function setYear($year) {
        $this->year = $year;
    }

    /**
     * Get the year the car was manufactured
     * 
     * @return int The manufactuer date
     */
    public function getYear() {
        return $this->year;
    }

    /**
     * The make of the car
     * 
     * @param string $make The make of the car
     * @return void
     */
    public function setMake($make) {
        $this->make = $make;
    }

    /**
     * Gets the make of the car
     * 
     * @return string The make of the car
     */
    public function getMake() {
        return $this->make;
    }

    /**
     * Sets the model of the car
     * 
     * @param string $model The car model
     * @return void
     */
    public function setModel($model) {
        $this->model = $model;
    }

    /**
     * Gets the model of car
     * 
     * @return string The car model
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * The color of the car
     * 
     * @param string $color The car color
     * @return void
     */
    public function setColor($color) {
        $this->color = $color;
    }

    /**
     * Gets the color of the car
     * 
     * @return string The color of the car
     */
    public function getColor() {
        return $this->color;
    }

    /**
     * Flag used to indicate whether the car is new or used
     * 
     * @param boolean $isNew True if the car is new, false if used
     * @return void
     */
    public function setIsNew($isNew) {
        $this->isNew = $isNew;
    }

    /**
     * Whether or not the car is new
     * 
     * @return boolean True if the car is new, false otherwise
     */
    public function getIsNew() {
        return $this->isNew;
    }

    /**
     * Sets a boolean flag used to indicate whether the car is cheap or not
     * 
     * @param boolean $isCheap True if the car is cheap, false otherwise
     * @return void
     */
    public function setIsCheap($isCheap) {
        $this->isCheap = $isCheap;
    }

    /**
     * Whether or not the car is cheap
     * 
     * @return boolean True of the car is cheap, false otherwise
     */
    public function getIsCheap() {
        return $this->isCheap;
    }

    /**
     * Sets the date the lease is up
     * 
     * @param string $leasedUtil The date when the lease is up
     * @return void
     */
    public function setLeasedUntil($leasedUntil) {
        $this->leasedUntil = $leasedUntil;
    }

    /**
     * Gets the lease expiration date
     * 
     * @return string The date when the lease is up
     */
    public function getLeasedUntil() {
        return $this->leasedUntil;
    }

    /**
     * Sets the owner of the car
     *
     * @param Owner $owner The owner of the car
     * @return void
     */
    public function setOwner(Owner $owner) {
        $this->owner = $owner;
    }

    /**
     * Gets the owner of the car
     * 
     * @return Owner The owner of the car
     */
    public function getOwner() {
        return $this->owner;
    }

    /**
     * Sets the tires on the car
     * 
     * @param array<Tire> $tires The car tires
     * @return void
     */
    public function setTires(array $tires) {
        $this->tires = $tires;
    }

    /**
     * Gets the tires on the car
     * 
     * @return array<Tire> The tires on the car
     */
    public function getTires() {
        return $this->tires;
    }
}
?>