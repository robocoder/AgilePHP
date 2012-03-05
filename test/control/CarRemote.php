<?php
/**
 * @package com.makeabyte.agilephp.test.control
 */
class CarRemote extends Remoting {

    public function __construct() {
        parent::__construct();
		parent::registerModel('Car');
    }

    /**
     * Makes the car go fast - vrooom!
     * 
     * @param Car $car The car to speed up
     * @return string
     */
    #@RemoteMethod
    public function goFast($car) {
        return $car;
    }

    /**
     * Makes the car stop - reeerrrrrtttt
     * 
     * @return string
     */
    #@RemoteMethod
    public function stop() {
        return 'Reeerrrrtttttt...';
    }
}
?>