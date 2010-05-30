<?php
/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class SoapRpcEncodedTest extends BaseTest {

 	  private $options = array( 'uri' => 'http://localhost/test/index.php/SoapRpcEncodedTestAPI', 
				  'soapaction' => '',
				  'classmap' => array( 'MathTest' => 'MathTest' ), // Maps MathTest WSDL data types to MathTest PHP data type (otherwise returned as stdClass)
				  'trace' => 1,
				  'exceptions' => 1,
 	  			  'cache_wsdl' => 0
 	  );

	  private $client;
	  private $mt1;
	  private $mt2;

	  public function __construct() {

	  		 parent::__construct();
	  		 $this->client = new SOAPClient( 'http://localhost/test/index.php/SoapRpcEncodedTestAPI/wsdl', $this->options );

	  		 $this->mt1 = new MathTest();
			 $this->mt1->setA( 1 );
			 $this->mt1->setB( 2 );

			 $this->mt2 = new MathTest();
			 $this->mt2->setA( 5 );
			 $this->mt2->setB( 10 );
	  }

	  public function testShowAvailableMethods() {

			 PHPUnit_Framework_Assert::assertEquals( count( $this->client->__getFunctions() ), 10, 'Failed to get all api methods' );
	  }
	  
	  public function testTest() {
	  	
	  		 $result = $this->client->test();
	  		 PHPUnit_Framework_Assert::assertEquals( 'TestAPI works!', $result, 'Unexpected test method result' );
	  }
	  
	  public function testAdd() {

	  		 $result = $this->client->add( 1, 2 );
	  		 PHPUnit_Framework_Assert::assertEquals( 3, $result, 'Unexpected add method result' );
	  }

	  public function testSubtract() {

	  		 $result = $this->client->subtract( 2, 1 );
	  		 PHPUnit_Framework_Assert::assertEquals( 1, $result, 'Unexpected subtract method result' );
	  }
	  
	  public function testMultiply() {

	  		 $result = $this->client->multiply( 2, 2 );
	  		 PHPUnit_Framework_Assert::assertEquals( 4, $result, 'Unexpected multiply method result' );
	  }
	  
	  public function testDivide() {

	  		 $result = $this->client->divide( 4, 2 );
	  		 PHPUnit_Framework_Assert::assertEquals( 2, $result, 'Unexpected divide method result' );
	  }

	  public function testObjectParameterTest() {

			 $oTest = $this->client->objectParameterTest( $this->mt1 );		
			 PHPUnit_Framework_Assert::assertEquals( '3', $oTest, 'objectParameterTest returned unexpected response \'' . $oTest . '\'. Expected 3.' );
	  }
	  
	  public function testArrayStringTest() {
	  	
	  		 $aTest = $this->client->arrayStringTest( array( 'test1', 'test2' ) );
			 PHPUnit_Framework_Assert::assertType( 'array', $aTest, 'Unexpected return value for arrayStringTest' );
			 PHPUnit_Framework_Assert::assertEquals( $aTest[0], 'test1', 'Failed get arrayStringTest element 0' );
			 PHPUnit_Framework_Assert::assertEquals( $aTest[1], 'test2', 'Failed get arrayStringTest element 1' );
	  }
	  
	  public function testArrayOfObjectsTest() {

			 $array = array( $this->mt1, $this->mt2 );
	  		 $aTest2 = $this->client->arrayOfObjectsTest( $array, 'arrayParameterTest' );
			 PHPUnit_Framework_Assert::assertType( 'array', $aTest2, 'Failed assert arrayOfObjectsTest return value is array' );
			 PHPUnit_Framework_Assert::assertType( 'MathTest', $aTest2[0], 'Failed get assert arrayOfObjectsTest element 0 is type MathTest' );
			 PHPUnit_Framework_Assert::assertType( 'MathTest', $aTest2[1], 'Failed get assert arrayOfObjectsTest element 1 is type MathTest' );
			 PHPUnit_Framework_Assert::assertEquals( $aTest2[0]->getA(), 1, 'Failed get arrayOfObjectsTest element 0 "a" property is 1' );
			 PHPUnit_Framework_Assert::assertEquals( $aTest2[0]->getB(), 2, 'Failed get arrayOfObjectsTest element 0 "b" property is 2' );
			 PHPUnit_Framework_Assert::assertEquals( $aTest2[1]->getA(), 5, 'Failed get arrayOfObjectsTest element 1 "a" property is 5' );
			 PHPUnit_Framework_Assert::assertEquals( $aTest2[1]->getB(), 10, 'Failed get arrayOfObjectsTest element 1 "b" property is 10' );
	  }

	  public function testArrayOfArraysTest() {

	  		 $aTest3 = $this->client->arrayOfArraysTest( array( array( 'test1', 'test2' ), array( 'test3', 'test4' ) ) );
			 PHPUnit_Framework_Assert::assertType( 'array', $aTest3, 'Failed assert arrayOfArraysTest return value is array' );
			 PHPUnit_Framework_Assert::assertType( 'array', $aTest3[0], 'Failed assert arrayOfArraysTest return value element[0] is array' );
			 PHPUnit_Framework_Assert::assertType( 'array', $aTest3[1], 'Failed assert arrayOfArraysTest return value element[1] is array' );
			 PHPUnit_Framework_Assert::assertEquals( 'test1', $aTest3[0][0], 'failed to assert first child array element 1' );
			 PHPUnit_Framework_Assert::assertEquals( 'test2', $aTest3[0][1], 'failed to assert first child array element 2' );
			 PHPUnit_Framework_Assert::assertEquals( 'test3', $aTest3[1][0], 'failed to assert second child array element 1' );
			 PHPUnit_Framework_Assert::assertEquals( 'test4', $aTest3[1][1], 'failed to assert second child array element 2' );
	  }
	  
	  public function testArrayAddTest() {

	  	 	 $int = $this->client->arrayAddTest( array( $this->mt1, $this->mt2 ) );
			 PHPUnit_Framework_Assert::assertEquals( $int, 18, 'Failed to assert arrayAddTest sum added up to 18' );
	  }
}
?>