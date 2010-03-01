<?php

require_once 'BaseTest.php';
require_once 'classes/MathTest.php';

/**
 * This tests the RPC/LITERAL web service type. Note that since use literal removes
 * type information from the WSDL, it is not possible to restore returned objects 
 * as PHP data types using a SOAPClient classmap. All objects are returned as stdClass
 * when using RPC/LITERAL.
 */
class SoapRpcLiteralTest extends BaseTest {

 	  private $options = array( 'uri' => 'http://localhost/test/index.php/SoapRpcLiteralTestAPI', 
				  'soapaction' => '',
				  'trace' => 1,
				  'exceptions' => 1,
 	  			  'cache_wsdl' => 0,
 	  			  'use' => SOAP_LITERAL
 	  );

	  private $client;
	  private $mt1;
	  private $mt2;

	  public function __construct() {

	  		 parent::__construct();
	  		 $this->client = new SOAPClient( 'http://localhost/test/index.php/SoapRpcLiteralTestAPI/wsdl', $this->options );

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

			 PHPUnit_Framework_Assert::assertType( 'array', $aTest->strings, 'Unexpected return value for arrayStringTest' );
			 PHPUnit_Framework_Assert::assertEquals( $aTest->strings[0], 'test1', 'Failed get arrayStringTest element 0' );
			 PHPUnit_Framework_Assert::assertEquals( $aTest->strings[1], 'test2', 'Failed get arrayStringTest element 1' );
	  }

	  public function testArrayOfObjectsTest() {

			 $array = array( $this->mt1, $this->mt2 );
	  		 $aTest2 = $this->client->arrayOfObjectsTest( $array, 'arrayParameterTest' );
			 PHPUnit_Framework_Assert::assertType( 'array', $aTest2->MathTests, 'Failed assert arrayOfObjectsTest return value is array' );
			 PHPUnit_Framework_Assert::assertType( 'stdClass', $aTest2->MathTests[0], 'Failed get assert arrayOfObjectsTest element 0 is type MathTest' );
			 PHPUnit_Framework_Assert::assertType( 'stdClass', $aTest2->MathTests[1], 'Failed get assert arrayOfObjectsTest element 1 is type MathTest' );
			 PHPUnit_Framework_Assert::assertEquals( $aTest2->MathTests[0]->a, 1, 'Failed get arrayOfObjectsTest element 0 "a" property is 1' );
			 PHPUnit_Framework_Assert::assertEquals( $aTest2->MathTests[0]->b, 2, 'Failed get arrayOfObjectsTest element 0 "b" property is 2' );
			 PHPUnit_Framework_Assert::assertEquals( $aTest2->MathTests[1]->a, 5, 'Failed get arrayOfObjectsTest element 1 "a" property is 5' );
			 PHPUnit_Framework_Assert::assertEquals( $aTest2->MathTests[1]->b, 10, 'Failed get arrayOfObjectsTest element 1 "b" property is 10' );
	  }

	  public function testArrayOfArraysTest() {

	  		 $aTest3 = $this->client->arrayOfArraysTest( array( array( 'test1', 'test2' ), array( 'test3', 'test4' ) ) );

			 PHPUnit_Framework_Assert::assertType( 'array', $aTest3->strings, 'Failed assert arrayOfArraysTest return value is array' );
			 PHPUnit_Framework_Assert::assertType( 'array', $aTest3->strings, 'Failed assert arrayOfArraysTest return value element[0] is array' );
			 PHPUnit_Framework_Assert::assertType( 'array', $aTest3->strings[0]->strings, 'failed to find first array element' );
			 PHPUnit_Framework_Assert::assertType( 'array', $aTest3->strings[1]->strings, 'failed to find second array element' );
			 
			 PHPUnit_Framework_Assert::assertEquals( 'test1', $aTest3->strings[0]->strings[0], 'failed to assert first child array element 1' );
			 PHPUnit_Framework_Assert::assertEquals( 'test2', $aTest3->strings[0]->strings[1], 'failed to assert first child array element 2' );
			 PHPUnit_Framework_Assert::assertEquals( 'test3', $aTest3->strings[1]->strings[0], 'failed to assert second child array element 1' );
			 PHPUnit_Framework_Assert::assertEquals( 'test4', $aTest3->strings[1]->strings[1], 'failed to assert second child array element 2' );
	  }

	  public function testArrayAddTest() {

	  	 	 $int = $this->client->arrayAddTest( array( $this->mt1, $this->mt2 ) );
			 PHPUnit_Framework_Assert::assertEquals( $int, 18, 'Failed to assert arrayAddTest sum added up to 18' );
	  }
}
?>