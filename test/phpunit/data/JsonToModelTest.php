<?php
/**
 * @package com.makeabyte.agilephp.test.data
 */
class JsonToModelTest extends PHPUnit_Framework_TestCase {

    /**
     * @test
     * @expectedException TransformException
     */
    public function intRender() {

        $data = json_encode(1);
        PHPUnit_Framework_Assert::assertEquals(1, JsonToModel::transform($data), 'Failed to transform int');
    }
    
	/**
     * @test
     * @expectedException TransformException
     */
    public function stringRender() {

        $data = json_encode('Test');
        PHPUnit_Framework_Assert::assertEquals('"Test"', JsonToModel::transform($data), 'Failed to transform string');
    }

	/**
     * @test
     * @expectedException TransformException
     */
    public function booleanRender() {

        $data1 = json_encode(true);
        $data2 = json_encode(false);

        PHPUnit_Framework_Assert::assertEquals('true', JsonToModel::transform($data1), 'Failed to transform boolean true');
        PHPUnit_Framework_Assert::assertEquals('false', JsonToModel::transform($data2), 'Failed to transform boolean false');
    }

    /**
     * @test 
     */
    public function primitiveArrayRender() {

        $array = json_encode(array('test', 1, false, true));

        $response = JsonToModel::transform($array);

        PHPUnit_Framework_Assert::assertType('array', $response, 'Failed to transform primitive array');
        PHPUnit_Framework_Assert::assertEquals(4, count($response), 'Failed to transform primitive array elements');
        PHPUnit_Framework_Assert::assertEquals('test', $response[0], 'Failed to transform primitive array element 0');
        PHPUnit_Framework_Assert::assertEquals(1, $response[1], 'Failed to transform primitive array element 1');
        PHPUnit_Framework_Assert::assertEquals(false, $response[2], 'Failed to transform primitive array element 2');
        PHPUnit_Framework_Assert::assertEquals(true, $response[3], 'Failed to transform primitive array element 3');
    }

    /**
     * @test
     */
    public function primitiveObjectRender() {

        /*
            $car = new Car();
            $car->year = 2011;
            $car->make = 'Lamborghini';
            $car->model = 'Murcielago';
            $car->color = 'Yellow';
            $car->isNew = true;
            $car->isCheap = false;
            $car->leasedUntil = null;
        */

        // JSON representation of the above model
        $json = ' { "year" : 2011, "make" : "Lamborghini", "model" : "Murcielago", "color" : "Yellow", "isNew" : true, "isCheap" : false, "leasedUntil" : null, "owner" : null, "tires" : null } ';

        $model = JsonToModel::transform($json);
        PHPUnit_Framework_Assert::assertType('stdClass', $model, 'Failed to transform primitive Car object');
        PHPUnit_Framework_Assert::assertEquals(2011, $model->year, 'Failed to get transformed Car->year field');
        PHPUnit_Framework_Assert::assertEquals('Lamborghini', $model->make, 'Failed to get transformed Car->make field');
        PHPUnit_Framework_Assert::assertEquals('Murcielago', $model->model, 'Failed to get transformed Car->model field');
        PHPUnit_Framework_Assert::assertEquals('Yellow', $model->color, 'Failed to get transformed Car->color field');
        PHPUnit_Framework_Assert::assertEquals(true, $model->isNew, 'Failed to get transformed Car->isNew field');
        PHPUnit_Framework_Assert::assertEquals(false, $model->isCheap, 'Failed to get transformed Car->isCheap field');
        PHPUnit_Framework_Assert::assertEquals(null, $model->leasedUntil, 'Failed to get transformed Car->leadedUntil field');
    }

    /**
     * @test
     */
    public function complexArrayRenderWithoutClassNames() {

        /*
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
        */

        $json = ' { "year" : 2011, "make" : "Lamborghini", "model" : "Murcielago", "color" : "Yellow", "isNew" : true, "isCheap" : false, "leasedUntil" : null, "owner" : { "name" : "Someone Special", "dob" : "01-01-1901" }  , "tires" : [  { "brand" : "Good Year", "size" : "235 35x19", "placement" : "LF", "tread" : "Good" } ,  { "brand" : "Good Year", "size" : "235 35x19", "placement" : "RF", "tread" : "Good" } ,  { "brand" : "Good Year", "size" : "345 25x20", "placement" : "LR", "tread" : "Worn" } ,  { "brand" : "Good Year", "size" : "345 25x20", "placement" : "RR", "tread" : "Worn" }  ]  } ';
        $model = JsonToModel::transform($json);

        PHPUnit_Framework_Assert::assertType('stdClass', $model, 'Failed to transform complex array without class name defined');
        PHPUnit_Framework_Assert::assertEquals(2011, $model->year, 'Failed to get transformed Car->year field');
        PHPUnit_Framework_Assert::assertEquals('Lamborghini', $model->make, 'Failed to get transformed Car->make field');
        PHPUnit_Framework_Assert::assertEquals('Murcielago', $model->model, 'Failed to get transformed Car->model field');
        PHPUnit_Framework_Assert::assertEquals('Yellow', $model->color, 'Failed to get transformed Car->color field');
        PHPUnit_Framework_Assert::assertEquals(true, $model->isNew, 'Failed to get transformed Car->isNew field');
        PHPUnit_Framework_Assert::assertEquals(false, $model->isCheap, 'Failed to get transformed Car->isCheap field');
        PHPUnit_Framework_Assert::assertEquals(null, $model->leasedUntil, 'Failed to get transformed Car->leadedUntil field');

        PHPUnit_Framework_Assert::assertType('stdClass', $model->owner, 'Failed to transform complex array element Car->owner');
        PHPUnit_Framework_Assert::assertEquals('Someone Special', $model->owner->name, 'Failed to get transformed Car->owner->name field');
        PHPUnit_Framework_Assert::assertEquals('01-01-1901', $model->owner->dob, 'Failed to get transformed Car->owner->dob field');

        PHPUnit_Framework_Assert::assertEquals('Good Year', $model->tires[0]->brand, 'Failed to get transformed Car->tires[0]->brand field');
        PHPUnit_Framework_Assert::assertEquals('235 35x19', $model->tires[0]->size, 'Failed to get transformed Car->tires[0]->size field');
        PHPUnit_Framework_Assert::assertEquals('LF', $model->tires[0]->placement, 'Failed to get transformed Car->tires[0]->placement field');
        PHPUnit_Framework_Assert::assertEquals('Good', $model->tires[0]->tread, 'Failed to get transformed Car->tires[0]->tread field');
        
        PHPUnit_Framework_Assert::assertEquals('Good Year', $model->tires[1]->brand, 'Failed to get transformed Car->tires[1]->brand field');
        PHPUnit_Framework_Assert::assertEquals('235 35x19', $model->tires[1]->size, 'Failed to get transformed Car->tires[1]->size field');
        PHPUnit_Framework_Assert::assertEquals('RF', $model->tires[1]->placement, 'Failed to get transformed Car->tires[1]->placement field');
        PHPUnit_Framework_Assert::assertEquals('Good', $model->tires[1]->tread, 'Failed to get transformed Car->tires[1]->tread field');

        PHPUnit_Framework_Assert::assertEquals('Good Year', $model->tires[2]->brand, 'Failed to get transformed Car->tires[2]->brand field');
        PHPUnit_Framework_Assert::assertEquals('345 25x20', $model->tires[2]->size, 'Failed to get transformed Car->tires[2]->size field');
        PHPUnit_Framework_Assert::assertEquals('LR', $model->tires[2]->placement, 'Failed to get transformed Car->tires[2]->placement field');
        PHPUnit_Framework_Assert::assertEquals('Worn', $model->tires[2]->tread, 'Failed to get transformed Car->tires[2]->tread field');
        
        PHPUnit_Framework_Assert::assertEquals('Good Year', $model->tires[3]->brand, 'Failed to get transformed Car->tires[3]->brand field');
        PHPUnit_Framework_Assert::assertEquals('345 25x20', $model->tires[3]->size, 'Failed to get transformed Car->tires[3]->size field');
        PHPUnit_Framework_Assert::assertEquals('RR', $model->tires[3]->placement, 'Failed to get transformed Car->tires[3]->placement field');
        PHPUnit_Framework_Assert::assertEquals('Worn', $model->tires[3]->tread, 'Failed to get transformed Car->tires[3]->tread field');
    }

	/**
     * @test
     */
    public function complexArrayRenderWithClassNames() {

        /*
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
        */

        $json = ' { "year" : 2011, "make" : "Lamborghini", "model" : "Murcielago", "color" : "Yellow", "isNew" : true, "isCheap" : false, "leasedUntil" : null, "owner" : { "_class": "Owner", "name" : "Someone Special", "dob" : "01-01-1901" }  , "tires" : [  { "_class": "Tire", "brand" : "Good Year", "size" : "235 35x19", "placement" : "LF", "tread" : "Good" } ,  { "_class": "Tire", "brand" : "Good Year", "size" : "235 35x19", "placement" : "RF", "tread" : "Good" } ,  { "_class": "Tire", "brand" : "Good Year", "size" : "345 25x20", "placement" : "LR", "tread" : "Worn" } ,  { "_class": "Tire", "brand" : "Good Year", "size" : "345 25x20", "placement" : "RR", "tread" : "Worn" }  ]  } ';
        $model = JsonToModel::transform($json, 'Car');

        PHPUnit_Framework_Assert::assertType('Car', $model, 'Failed to render complex array WITH class name to type Car');
        PHPUnit_Framework_Assert::assertEquals(2011, $model->getYear(), 'Failed to get transformed Car->year field');
        PHPUnit_Framework_Assert::assertEquals('Lamborghini', $model->getMake(), 'Failed to get transformed Car->make field');
        PHPUnit_Framework_Assert::assertEquals('Murcielago', $model->getModel(), 'Failed to get transformed Car->model field');
        PHPUnit_Framework_Assert::assertEquals('Yellow', $model->getColor(), 'Failed to get transformed Car->color field');
        PHPUnit_Framework_Assert::assertEquals(true, $model->getIsNew(), 'Failed to get transformed Car->isNew field');
        PHPUnit_Framework_Assert::assertEquals(false, $model->getIsCheap(), 'Failed to get transformed Car->isCheap field');
        PHPUnit_Framework_Assert::assertEquals(null, $model->getLeasedUntil(), 'Failed to get transformed Car->leadedUntil field');

        PHPUnit_Framework_Assert::assertType('Owner', $model->getOwner(), 'Failed to transform complex array element Car->owner');
        PHPUnit_Framework_Assert::assertEquals('Someone Special', $model->getOwner()->getName(), 'Failed to get transformed Car->owner->name field');
        PHPUnit_Framework_Assert::assertEquals('01-01-1901', $model->getOwner()->getDob(), 'Failed to get transformed Car->owner->dob field');

        $tires = $model->getTires();
        PHPUnit_Framework_Assert::assertEquals('Good Year', $tires[0]->getBrand(), 'Failed to get transformed Car->tires[0]->brand field');
        PHPUnit_Framework_Assert::assertEquals('235 35x19', $tires[0]->getSize(), 'Failed to get transformed Car->tires[0]->size field');
        PHPUnit_Framework_Assert::assertEquals('LF', $tires[0]->getPlacement(), 'Failed to get transformed Car->tires[0]->placement field');
        PHPUnit_Framework_Assert::assertEquals('Good', $tires[0]->getTread(), 'Failed to get transformed Car->tires[0]->tread field');

        PHPUnit_Framework_Assert::assertEquals('Good Year', $tires[1]->getBrand(), 'Failed to get transformed Car->tires[1]->brand field');
        PHPUnit_Framework_Assert::assertEquals('235 35x19', $tires[1]->getSize(), 'Failed to get transformed Car->tires[1]->size field');
        PHPUnit_Framework_Assert::assertEquals('RF', $tires[1]->getPlacement(), 'Failed to get transformed Car->tires[1]->placement field');
        PHPUnit_Framework_Assert::assertEquals('Good', $tires[1]->getTread(), 'Failed to get transformed Car->tires[1]->tread field');

        PHPUnit_Framework_Assert::assertEquals('Good Year', $tires[2]->getBrand(), 'Failed to get transformed Car->tires[2]->brand field');
        PHPUnit_Framework_Assert::assertEquals('345 25x20', $tires[2]->getSize(), 'Failed to get transformed Car->tires[2]->size field');
        PHPUnit_Framework_Assert::assertEquals('LR', $tires[2]->getPlacement(), 'Failed to get transformed Car->tires[2]->placement field');
        PHPUnit_Framework_Assert::assertEquals('Worn', $tires[2]->getTread(), 'Failed to get transformed Car->tires[2]->tread field');

        PHPUnit_Framework_Assert::assertEquals('Good Year', $tires[3]->getBrand(), 'Failed to get transformed Car->tires[3]->brand field');
        PHPUnit_Framework_Assert::assertEquals('345 25x20', $tires[3]->getSize(), 'Failed to get transformed Car->tires[3]->size field');
        PHPUnit_Framework_Assert::assertEquals('RR', $tires[3]->getPlacement(), 'Failed to get transformed Car->tires[3]->placement field');
        PHPUnit_Framework_Assert::assertEquals('Worn', $tires[3]->getTread(), 'Failed to get transformed Car->tires[3]->tread field');
    }
}
?>