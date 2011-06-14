<?php
/**
 * @package com.makeabyte.agilephp.test.data
 */
class DataModelTest extends PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function toXml() {

        $expected = '<Car><year>2011</year><make>Lamborghini</make><model>Murcielago</model><color>Yellow</color><isNew>1</isNew><isCheap></isCheap><leasedUntil></leasedUntil><owner><name>Someone Special</name><dob>01-01-1901</dob></owner><tires><Tire><brand>Good Year</brand><size>235 35x19</size><placement>LF</placement><tread>Good</tread></Tire><Tire><brand>Good Year</brand><size>235 35x19</size><placement>RF</placement><tread>Good</tread></Tire><Tire><brand>Good Year</brand><size>345 25x20</size><placement>RF</placement><tread>Worn</tread></Tire><Tire><brand>Good Year</brand><size>345 25x20</size><placement>RR</placement><tread>Worn</tread></Tire></tires></Car>';
        PHPUnit_Framework_Assert::assertEquals($expected, $this->getMockModel()->toXml(), 'Failed to render expected xml');
    }

	/**
     * @test
     */
    public function toJson() {

        $expected = ' { "year" : 2011, "make" : "Lamborghini", "model" : "Murcielago", "color" : "Yellow", "isNew" : true, "isCheap" : false, "leasedUntil" : null, "owner" : { "name" : "Someone Special", "dob" : "01-01-1901" }  , "tires" : [  { "brand" : "Good Year", "size" : "235 35x19", "placement" : "LF", "tread" : "Good" } ,  { "brand" : "Good Year", "size" : "235 35x19", "placement" : "RF", "tread" : "Good" } ,  { "brand" : "Good Year", "size" : "345 25x20", "placement" : "RF", "tread" : "Worn" } ,  { "brand" : "Good Year", "size" : "345 25x20", "placement" : "RR", "tread" : "Worn" }  ]  } ';
        PHPUnit_Framework_Assert::assertEquals($expected, $this->getMockModel()->toJson(), 'Failed to render expected json');
    }

	/**
     * @test
     */
    public function toYaml() {

        $yaml = $this->getMockModel()->toYaml();
        PHPUnit_Framework_Assert::assertTrue((YamlToModel::transform($yaml) instanceof Car), 'Failed to render expected yaml');
    }

    /**
     * Provides a hierachial model that covers all of the possible data types / combinations
     * 
     * @return Car The mock car model
     */
    private function getMockModel() {

        $owner = new Owner();
        $owner->setName('Someone Special');
        $owner->setDob('01-01-1901');

        $tire1 = new Tire();
        $tire1->setBrand('Good Year');
        $tire1->setSize('235 35x19');
        $tire1->setPlacement('LF');
        $tire1->setTread('Good');

        $tire2 = new Tire();
        $tire2->setBrand('Good Year');
        $tire2->setSize('235 35x19');
        $tire2->setPlacement('RF');
        $tire2->setTread('Good');

        $tire3 = new Tire();
        $tire3->setBrand('Good Year');
        $tire3->setSize('345 25x20');
        $tire3->setPlacement('RF');
        $tire3->setTread('Worn');

        $tire4 = new Tire();
        $tire4->setBrand('Good Year');
        $tire4->setSize('345 25x20');
        $tire4->setPlacement('RR');
        $tire4->setTread('Worn');

        $car = new Car();
        $car->setYear(2011);
        $car->setMake('Lamborghini');
        $car->setModel('Murcielago');
        $car->setColor('Yellow');
        $car->setIsNew(true);
        $car->setIsCheap(false);
        $car->setLeasedUntil(null);
        $car->setOwner($owner);
        $car->setTires(array($tire1, $tire2, $tire3, $tire4));

        return $car;
    }
}
?>