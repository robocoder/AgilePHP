<?php
/**
 * @package com.makeabyte.agilephp.test.data
 */
class JsonRendererTest extends PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function intRender() {

        PHPUnit_Framework_Assert::assertEquals(1, JsonRenderer::render(1), 'Failed to render int');
    }
    
	/**
     * @test
     */
    public function stringRender() {

        PHPUnit_Framework_Assert::assertEquals('"Test"', JsonRenderer::render('Test'), 'Failed to render string');
    }

	/**
     * @test
     */
    public function booleanRender() {

        PHPUnit_Framework_Assert::assertEquals('true', JsonRenderer::render(true), 'Failed to render boolean true');
        PHPUnit_Framework_Assert::assertEquals('false', JsonRenderer::render(false), 'Failed to render boolean false');
    }

    /**
     * @test 
     */
    public function primitiveArrayRender() {

        $array = array('test', 1, false, true);
        PHPUnit_Framework_Assert::assertEquals('[ "test", 1, false, true ]', JsonRenderer::render($array), 'Failed to render primitive array');
    }

    /**
     * @test
     */
    public function primitiveObjectRender() {

        $car = new Car();
        $car->year = 2011;
        $car->make = 'Lamborghini';
        $car->model = 'Murcielago';
        $car->color = 'Yellow';
        $car->isNew = true;
        $car->isCheap = false;
        $car->leasedUntil = null;

        $expected = ' { "year" : 2011, "make" : "Lamborghini", "model" : "Murcielago", "color" : "Yellow", "isNew" : true, "isCheap" : false, "leasedUntil" : null, "owner" : null, "tires" : null } ';
        PHPUnit_Framework_Assert::assertEquals($expected, JsonRenderer::render($car), 'Failed to render primitive object');
    }

    /**
     * @test
     */
    public function complexArrayRenderWithoutClassNames() {

        $owner = new Owner();
        $owner->name = 'Someone Special';
        $owner->dob = '01-01-1901';

        $tire1 = new Tire();
        $tire1->brand = 'Good Year';
        $tire1->size = '235 35x19';
        $tire1->placement = 'LF';
        $tire1->tread = 'Good';

        $tire2 = new Tire();
        $tire2->brand = 'Good Year';
        $tire2->size = '235 35x19';
        $tire2->placement = 'RF';
        $tire2->tread = 'Good';

        $tire3 = new Tire();
        $tire3->brand = 'Good Year';
        $tire3->size = '345 25x20';
        $tire3->placement = 'LR';
        $tire3->tread = 'Worn';

        $tire4 = new Tire();
        $tire4->brand = 'Good Year';
        $tire4->size = '345 25x20';
        $tire4->placement = 'RR';
        $tire4->tread = 'Worn';

        $car = new Car();
        $car->year = 2011;
        $car->make = 'Lamborghini';
        $car->model = 'Murcielago';
        $car->color = 'Yellow';
        $car->isNew = true;
        $car->isCheap = false;
        $car->leasedUntil = null;
        $car->owner = $owner;
        $car->tires = array($tire1, $tire2, $tire3, $tire4);

        $expected = ' { "year" : 2011, "make" : "Lamborghini", "model" : "Murcielago", "color" : "Yellow", "isNew" : true, "isCheap" : false, "leasedUntil" : null, "owner" : { "name" : "Someone Special", "dob" : "01-01-1901" }  , "tires" : [  { "brand" : "Good Year", "size" : "235 35x19", "placement" : "LF", "tread" : "Good" } ,  { "brand" : "Good Year", "size" : "235 35x19", "placement" : "RF", "tread" : "Good" } ,  { "brand" : "Good Year", "size" : "345 25x20", "placement" : "LR", "tread" : "Worn" } ,  { "brand" : "Good Year", "size" : "345 25x20", "placement" : "RR", "tread" : "Worn" }  ]  } ';       
        PHPUnit_Framework_Assert::assertEquals($expected, JsonRenderer::render($car), 'Failed to render complex array without class names');
    }

	/**
     * @test
     */
    public function complexArrayRenderWithClassNames() {

        $owner = new Owner();
        $owner->name = 'Someone Special';
        $owner->dob = '01-01-1901';

        $tire1 = new Tire();
        $tire1->brand = 'Good Year';
        $tire1->size = '235 35x19';
        $tire1->placement = 'LF';
        $tire1->tread = 'Good';

        $tire2 = new Tire();
        $tire2->brand = 'Good Year';
        $tire2->size = '235 35x19';
        $tire2->placement = 'RF';
        $tire2->tread = 'Good';

        $tire3 = new Tire();
        $tire3->brand = 'Good Year';
        $tire3->size = '345 25x20';
        $tire3->placement = 'LR';
        $tire3->tread = 'Worn';

        $tire4 = new Tire();
        $tire4->brand = 'Good Year';
        $tire4->size = '345 25x20';
        $tire4->placement = 'RR';
        $tire4->tread = 'Worn';

        $car = new Car();
        $car->year = 2011;
        $car->make = 'Lamborghini';
        $car->model = 'Murcielago';
        $car->color = 'Yellow';
        $car->isNew = true;
        $car->isCheap = false;
        $car->leasedUntil = null;
        $car->owner = $owner;
        $car->tires = array($tire1, $tire2, $tire3, $tire4);

        $expected = ' { "_class": "Car", "year" : 2011, "make" : "Lamborghini", "model" : "Murcielago", "color" : "Yellow", "isNew" : true, "isCheap" : false, "leasedUntil" : null, "owner" : { "_class": "Owner", "name" : "Someone Special", "dob" : "01-01-1901" }  , "tires" : [  { "_class": "Tire", "brand" : "Good Year", "size" : "235 35x19", "placement" : "LF", "tread" : "Good" } ,  { "_class": "Tire", "brand" : "Good Year", "size" : "235 35x19", "placement" : "RF", "tread" : "Good" } ,  { "_class": "Tire", "brand" : "Good Year", "size" : "345 25x20", "placement" : "LR", "tread" : "Worn" } ,  { "_class": "Tire", "brand" : "Good Year", "size" : "345 25x20", "placement" : "RR", "tread" : "Worn" }  ]  } ';       
        PHPUnit_Framework_Assert::assertEquals($expected, JsonRenderer::render($car, null, false, true), 'Failed to render complex array WITH class names');
    }
}

/**
 * @package com.makeabyte.agilephp.test.data
 */
class Car {

    public $year;
    public $make;
    public $model;
    public $color;
    public $isNew;
    public $isCheap;
    public $leasedUntil;
    public $owner;
    public $tires;
}

/**
 * @package com.makeabyte.agilephp.test.data
 */
class Tire {

    public $brand;
    public $size;
    public $placement;
    public $tread;
}

/**
 * @package com.makeabyte.agilephp.test.data
 */
class Owner {

    public $name;
    public $dob;
}
?>