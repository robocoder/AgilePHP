<?php 

ini_set( 'soap.wsdl_cache_enabled', '0' );
require_once 'BaseTest.php';
require_once 'classes/MathTest.php';

class WebServiceTest extends BaseTest {

 	  private $options = array( 'uri' => 'http://localhost/test/index.php/TestAPI', 
				  'soapaction' => '',
				  'classmap' => array( 'MathTest' => 'MathTest' ), // Maps MathTest WSDL data types to MathTest PHP data type (otherwise returned as stdClass)
				  'trace' => 1,
				  'exceptions' => 1 );

	  private $client;
	  private $mt1;
	  private $mt2;

	  public function __construct() {

	  		 parent::__construct();
	  		 $this->client = new SOAPClient( 'http://localhost/test/index.php/TestAPI/wsdl', $this->options );

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
	  }
	  
	  public function testArrayOfArraysTest() {

	  		 $aTest3 = $this->client->arrayOfArraysTest( array( array( 'test1', 'test2', array( 1, 2 ) ), array( 'test3', 'test4' ) ) );
			 PHPUnit_Framework_Assert::assertType( 'array', $aTest3, 'Failed assert arrayOfArraysTest return value is array' );
			 PHPUnit_Framework_Assert::assertType( 'array', $aTest3[0], 'Failed assert arrayOfArraysTest return value element[0] is array' );
			 PHPUnit_Framework_Assert::assertType( 'array', $aTest3[0][2], 'Failed assert arrayOfArraysTest return value element[0][2] is array' );
			 PHPUnit_Framework_Assert::assertType( 'array', $aTest3[1], 'Failed assert arrayOfArraysTest return value element[1] is array' );
	  }
	  
	  public function testArrayAddTest() {

	  	 	 $int = $this->client->arrayAddTest( array( $this->mt1, $this->mt2 ) );
			 PHPUnit_Framework_Assert::assertEquals( $int, 18, 'Failed to assert arrayAddTest sum added up to 18' );
	  }
}
?>