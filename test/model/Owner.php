<?php
/**
 * @package com.makeabyte.agilephp.test.model
 */
class Owner extends DataModel {

    /**
     * @var string The owners name
     */
    private $name;
    /**
     * @var string dob The owners Date of Birth
     */
    private $dob;

    public function __construct($name = null, $dob = null) {
        $this->name = $name;
        $this->dob = $dob;
    }

    /**
     * Sets the owners name
     * 
     * @param string $name The owners name
     * @return void
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Gets the owners name
     * 
     * @return string The owners name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the owners Date of Birth
     * 
     * @param string $dob The date of birth
     * @return void
     */
    public function setDob($dob) {
        $this->dob = $dob;
    }

    /**
     * Gets the owners Date of Birth
     * 
     * @return string The owners Date of Birth
     */
    public function getDob() {
        return $this->dob;
    }
}
?>