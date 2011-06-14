<?php
/**
 * @package com.makeabyte.agilephp.test.util
 */
class DocBlockParserTest extends PHPUnit_Framework_TestCase {

	/**
     * @test
     */
    public function getPropertyType() {

        $class = new ReflectionClass(new Car());
        $type = DocBlockParser::getPropertyType($class->getProperty('year'));
        PHPUnit_Framework_Assert::assertEquals('int', $type, 'Failed to assert Car::year is of type int');
    }
    
	/**
     * @test
     */
    public function getPropertyArrayType() {

        $class = new ReflectionClass(new Car());
        $type = DocBlockParser::getPropertyArrayType($class->getProperty('tires'));
        PHPUnit_Framework_Assert::assertEquals('Tire', $type, 'Failed to assert Car::tires is of type Tire');
    }

	/**
     * @test
     */
    public function getParameterType() {

        $class = new ReflectionClass(new Car());
        $method = $class->getMethod('setYear');
        $parameters = $method->getParameters();

        $type = DocBlockParser::getParameterType($method, $parameters[0]);

        PHPUnit_Framework_Assert::assertEquals('int', $type, 'Failed to assert Car::setYear parameter is of type int');
    }

    /**
     * @test
     */
    public function getParameterArrayType() {

        $class = new ReflectionClass(new Car());
        $method = $class->getMethod('setTires');
        $parameters = $method->getParameters();

        $type = DocBlockParser::getParameterArrayType($method, $parameters[0]);

        PHPUnit_Framework_Assert::assertEquals('Tire', $type, 'Failed to assert Car::setTires array elements are of type Tire');
    }
    
    /**
     * @test
     */
    public function isUserSpaceObject() {

        // PHP primitive types
        PHPUnit_Framework_Assert::assertFalse(DocBlockParser::isUserSpaceObject('string'), 'Failed to assert string is not a custom data type');
        PHPUnit_Framework_Assert::assertFalse(DocBlockParser::isUserSpaceObject('int'), 'Failed to assert int is not a custom data type');
        PHPUnit_Framework_Assert::assertFalse(DocBlockParser::isUserSpaceObject('integer'), 'Failed to assert integer is not a custom data type');
        PHPUnit_Framework_Assert::assertFalse(DocBlockParser::isUserSpaceObject('float'), 'Failed to assert float is not a custom data type');
        PHPUnit_Framework_Assert::assertFalse(DocBlockParser::isUserSpaceObject('double'), 'Failed to assert double is not a custom data type');
        PHPUnit_Framework_Assert::assertFalse(DocBlockParser::isUserSpaceObject('bool'), 'Failed to assert bool is not a custom data type');
        PHPUnit_Framework_Assert::assertFalse(DocBlockParser::isUserSpaceObject('boolean'), 'Failed to boolean int is not a custom data type');
        PHPUnit_Framework_Assert::assertFalse(DocBlockParser::isUserSpaceObject('array'), 'Failed to assert array is not a custom data type');
        PHPUnit_Framework_Assert::assertFalse(DocBlockParser::isUserSpaceObject('object'), 'Failed to assert object is not a custom data type');
        PHPUnit_Framework_Assert::assertFalse(DocBlockParser::isUserSpaceObject('resource'), 'Failed to assert resource is not a custom data type');
        PHPUnit_Framework_Assert::assertFalse(DocBlockParser::isUserSpaceObject('null'), 'Failed to assert null is not a custom data type');
        PHPUnit_Framework_Assert::assertFalse(DocBlockParser::isUserSpaceObject('void'), 'Failed to assert void is not a custom data type');

        // User space objects
        PHPUnit_Framework_Assert::assertTrue(DocBlockParser::isUserSpaceObject('Car'), 'Failed to assert "Car" is not a custom data type');
        PHPUnit_Framework_Assert::assertTrue(DocBlockParser::isUserSpaceObject('Owner'), 'Failed to assert "Owner" is not a custom data type');
        PHPUnit_Framework_Assert::assertTrue(DocBlockParser::isUserSpaceObject('Tire'), 'Failed to assert "Tire" is not a custom data type');
    }

    /**
     * @test
     */
    public function getReturnType() {

        $class = new ReflectionClass(new Car());

        $type = DocBlockParser::getReturnType($class->getMethod('getOwner'));
        PHPUnit_Framework_Assert::assertEquals('Owner', $type, 'Failed to assert Car::getOwner return type is Owner');

        $type = DocBlockParser::getReturnType($class->getMethod('getLeasedUntil'));
        PHPUnit_Framework_Assert::assertEquals('string', $type, 'Failed to assert Car::getLeasedUntil return type is string');

        $type = DocBlockParser::getReturnType($class->getMethod('getIsNew'));
        PHPUnit_Framework_Assert::assertEquals('boolean', $type, 'Failed to assert Car::getIsNew return type is boolean');

        $type = DocBlockParser::getReturnType($class->getMethod('getTires'));
        PHPUnit_Framework_Assert::assertEquals('array', $type, 'Failed to assert Car::getTires return type is array');

        $type = DocBlockParser::getReturnArrayType($class->getMethod('getTires'));
        PHPUnit_Framework_Assert::assertEquals('Tire', $type, 'Failed to assert Car::getTires return array element type is Tire');
    }
    
    /**
     * @test
     */
    public function getReturnArrayType() {

    	$class = new ReflectionClass(new Car());
    	$type = DocBlockParser::getReturnArrayType($class->getMethod('getTires'));
    	PHPUnit_Framework_Assert::assertEquals('Tire', $type, 'Failed to assert Car::getTires array element return type is Tire');
    }
}
?>