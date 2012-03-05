<?php
/**
 * Tests the util\ModelGenerator class.
 *
 * Note that before PHP 5.3.3, the property_exists function used in this test class
 * fails for private properties in base classes (which is fixed in 5.3.3)
 *
 * @package com.makeabyte.agilephp.test.util
 */
class ModelGeneratorTest extends PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function createCar() {

        $fields = array('year', 'make', 'model', 'color', 'isNew', 'isCheap', 'leasedUntil', 'owner', 'tires');

        $generator = new ModelGenerator();
        $generator->setClassName('Car');
        $generator->setFields($fields);

        $model = $generator->createModel();
        eval($model);

        $car = new Car();

        PHPUnit_Framework_Assert::assertType('Car', $car, 'Failed to create car model');

        PHPUnit_Framework_Assert::assertTrue(property_exists('Car', 'year'), 'Failed to locate generated Car::year property');
        PHPUnit_Framework_Assert::assertTrue(property_exists('Car', 'make'), 'Failed to locate generated Car::make property');
        PHPUnit_Framework_Assert::assertTrue(property_exists('Car', 'model'), 'Failed to locate generated Car::model property');
        PHPUnit_Framework_Assert::assertTrue(property_exists('Car', 'color'), 'Failed to locate generated Car::color property');
        PHPUnit_Framework_Assert::assertTrue(property_exists('Car', 'isNew'), 'Failed to locate generated Car::isNew property');
        PHPUnit_Framework_Assert::assertTrue(property_exists('Car', 'isCheap'), 'Failed to locate generated Car::isCheap property');
        PHPUnit_Framework_Assert::assertTrue(property_exists('Car', 'leasedUntil'), 'Failed to locate generated Car::leasedUntil property');
        PHPUnit_Framework_Assert::assertTrue(property_exists('Car', 'owner'), 'Failed to locate generated Car::owner property');
        PHPUnit_Framework_Assert::assertTrue(property_exists('Car', 'tires'), 'Failed to locate generated Car::tires property');

        PHPUnit_Framework_Assert::assertTrue(method_exists($car, 'setYear'), 'Failed to locate generated Car::setYear method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($car, 'getYear'), 'Failed to locate generated Car::getYear method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($car, 'setMake'), 'Failed to locate generated Car::setMake method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($car, 'getMake'), 'Failed to locate generated Car::getMake method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($car, 'setModel'), 'Failed to locate generated Car::setModel method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($car, 'getModel'), 'Failed to locate generated Car::getModel method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($car, 'setColor'), 'Failed to locate generated Car::setColor method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($car, 'getColor'), 'Failed to locate generated Car::getColor method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($car, 'setIsNew'), 'Failed to locate generated Car::setIsNew method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($car, 'getIsNew'), 'Failed to locate generated Car::getIsNew method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($car, 'setIsCheap'), 'Failed to locate generated Car::setIsCheap method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($car, 'getIsCheap'), 'Failed to locate generated Car::getIsCheap method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($car, 'setLeasedUntil'), 'Failed to locate generated Car::setLeasedUntil method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($car, 'getLeasedUntil'), 'Failed to locate generated Car::getLeasedUntil method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($car, 'setOwner'), 'Failed to locate generated Car::setOwner method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($car, 'getOwner'), 'Failed to locate generated Car::getOwner method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($car, 'setTires'), 'Failed to locate generated Car::setTires method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($car, 'getTires'), 'Failed to locate generated Car::getTires method');
    }

    /**
     * @test
     */
    public function createTire() {

        $fields = array('brand', 'size', 'placement', 'tread');

        $generator = new ModelGenerator();
        $generator->setClassName('Tire');
        $generator->setFields($fields);

        $model = $generator->createModel();
        eval($model);

        $tire = new Tire();

        PHPUnit_Framework_Assert::assertType('Tire', $tire, 'Failed to create Tire model');

        PHPUnit_Framework_Assert::assertTrue(property_exists('Tire', 'brand'), 'Failed to locate generated Tire::brand property');
        PHPUnit_Framework_Assert::assertTrue(property_exists('Tire', 'size'), 'Failed to locate generated Tire::size property');
        PHPUnit_Framework_Assert::assertTrue(property_exists('Tire', 'placement'), 'Failed to locate generated Tire::placement property');
        PHPUnit_Framework_Assert::assertTrue(property_exists('Tire', 'tread'), 'Failed to locate generated Tire::tread property');

        PHPUnit_Framework_Assert::assertTrue(method_exists($tire, '__construct'), 'Failed to locate generated Tire constructor');
        PHPUnit_Framework_Assert::assertTrue(method_exists($tire, 'setBrand'), 'Failed to locate generated Tire::setBrand method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($tire, 'getBrand'), 'Failed to locate generated Tire::getBrand method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($tire, 'setSize'), 'Failed to locate generated Tire::setSize method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($tire, 'getSize'), 'Failed to locate generated Tire::getSize method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($tire, 'setPlacement'), 'Failed to locate generated Tire::setPlacement method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($tire, 'getPlacement'), 'Failed to locate generated Tire::getPlacement method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($tire, 'setTread'), 'Failed to locate generated Tire::setTread method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($tire, 'getTread'), 'Failed to locate generated Tire::getTreat method');
    }

    /**
     * @test
     */
    public function createOwner() {

        $fields = array('name', 'dob');

        $generator = new ModelGenerator();
        $generator->setClassName('Owner');
        $generator->setFields($fields);

        $model = $generator->createModel();
        eval($model);

        $owner = new Owner();

        PHPUnit_Framework_Assert::assertType('Owner', $owner, 'Failed to create Tire model');

        PHPUnit_Framework_Assert::assertTrue(property_exists('Owner', 'name'), 'Failed to locate generated Owner::name property');
        PHPUnit_Framework_Assert::assertTrue(property_exists('Owner', 'dob'), 'Failed to locate generated Owner::dob property');

        PHPUnit_Framework_Assert::assertTrue(method_exists($owner, 'setName'), 'Failed to locate generated Owner::setName method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($owner, 'getName'), 'Failed to locate generated Owner::getName method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($owner, 'setDob'), 'Failed to locate generated Owner::setDob method');
        PHPUnit_Framework_Assert::assertTrue(method_exists($owner, 'getDob'), 'Failed to locate generated Owner::getDob method');
    }
}
?>