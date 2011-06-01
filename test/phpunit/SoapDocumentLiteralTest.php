<?php

/**
 * This tests the RPC/LITERAL web service type. Note that since use literal removes
 * type information from the WSDL, it is not possible to restore returned objects
 * as PHP data types using a SOAPClient classmap. All objects are returned as stdClass
 * when using RPC/LITERAL.
 *
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class SoapDocumentLiteralTest extends PHPUnit_Framework_TestCase {

 	  private $options = array('uri' => 'http://localhost/test/index.php/SoapDocumentLiteralTestAPI/?XDEBUG_SESSION_START=1&KEY=agilephp',
				  'soapaction' => '',
				  'trace' => 1,
				  'exceptions' => 1,
 	  			  'cache_wsdl' => 0,
 	  			  'style' => SOAP_DOCUMENT,
 	  			  'use' => SOAP_LITERAL
 	 );

	  private $client;
	  private $mt1;
	  private $mt2;

	  public function __construct() {

	  	     // Maps Document/Literal WSDL data types to PHP data types
	  		 $this->options['classmap'] = array(
                                    'test' => 'test',
                                    'testResponse' => 'testResponse',
                                    'add' => 'add',
                                    'addResponse' => 'addResponse',
                                    'subtract' => 'subtract',
                                    'subtractResponse' => 'subtractResponse',
                                    'multiply' => 'multiply',
                                    'multiplyResponse' => 'multiplyResponse',
                                    'divide' => 'divide',
                                    'divideResponse' => 'divideResponse',
                                    'MathTest' => 'MathTest',
                                    'objectParameterTest' => 'objectParameterTest',
                                    'objectParameterTestResponse' => 'objectParameterTestResponse',
                                    'MathTestArray' => 'MathTestArray',
                                    'arrayOfObjectsTest' => 'arrayOfObjectsTest',
                                    'arrayOfObjectsTestResponse' => 'arrayOfObjectsTestResponse',
                                    'stringArray' => 'stringArray',
                                    'arrayStringTest' => 'arrayStringTest',
                                    'arrayStringTestResponse' => 'arrayStringTestResponse',
                                    'stringArrayArray' => 'stringArrayArray',
                                    'arrayOfArraysTest' => 'arrayOfArraysTest',
                                    'arrayOfArraysTestResponse' => 'arrayOfArraysTestResponse',
                                    'arrayAddTest' => 'arrayAddTest',
                                    'arrayAddTestResponse' => 'arrayAddTestResponse',
                                  );

			 parent::__construct();
	  		 $this->client = new SOAPClient('http://localhost/test/index.php/SoapDocumentLiteralTestAPI/wsdl/?XDEBUG_SESSION_START=1&KEY=agilephp', $this->options);

	  		 $this->mt1 = new MathTest();
			 $this->mt1->a = 1;
			 $this->mt1->b = 2;

			 $this->mt2 = new MathTest();
			 $this->mt2->a = 5;
			 $this->mt2->b = 10;
	  }

	  public function testShowAvailableMethods() {

			 PHPUnit_Framework_Assert::assertEquals(count($this->client->__getFunctions()), 10, 'Failed to get all api methods');
	  }

	  public function testTest() {

	  		 $result = $this->invoke('test', null);
	  		 PHPUnit_Framework_Assert::assertEquals('TestAPI works!', $result->return, 'Unexpected test method result');
	  }

	  public function testAdd() {

	  		 $o = new add;
	  		 $o->a = 1;
	  		 $o->b = 2;

	  		 $result = $this->invoke('add', $o);
	  		 PHPUnit_Framework_Assert::assertEquals(3, $result->return, 'Unexpected add method result');
	  }

	  public function testSubtract() {

	  		 $o = new subtract;
	  		 $o->a = 2;
	  		 $o->b = 1;

	  		 $result = $this->invoke('subtract', $o);
	  		 PHPUnit_Framework_Assert::assertEquals(1, $result->return, 'Unexpected subtract method result');
	  }

	  public function testMultiply() {

	  		 $o = new multiply;
	  		 $o->a = 2;
	  		 $o->b = 2;

	  		 $result = $this->invoke('multiply', $o);
	  		 PHPUnit_Framework_Assert::assertEquals(4, $result->return, 'Unexpected multiply method result');
	  }

	  public function testDivide() {

	  		 $o = new divide;
	  		 $o->a = 4;
	  		 $o->b = 2;

	  		 $result = $this->invoke('divide', $o);
	  		 PHPUnit_Framework_Assert::assertEquals(2, $result->return, 'Unexpected divide method result');
	  }

	  public function testObjectParameterTest() {

	  		 $o = new objectParameterTest;
	  		 $o->MathTest = $this->mt1;

			 $result = $this->invoke('objectParameterTest', $o);
			 PHPUnit_Framework_Assert::assertEquals('3', $result->return, 'objectParameterTest returned unexpected response \'' . $result->return . '\'. Expected 3.');
	  }

	  public function testArrayStringTest() {

	  		 $o = new arrayStringTest;
	  		 $o->strings = array('test1', 'test2');

	  		 $result = $this->invoke('arrayStringTest', $o);

			 PHPUnit_Framework_Assert::assertType('array', $result->return, 'Unexpected return value for arrayStringTest');
			 PHPUnit_Framework_Assert::assertEquals($result->return[0], 'test1', 'Failed get arrayStringTest element 0');
			 PHPUnit_Framework_Assert::assertEquals($result->return[1], 'test2', 'Failed get arrayStringTest element 1');
	  }

	  public function testArrayOfObjectsTest() {

	  		 $o = new arrayOfObjectsTest;
	  		 $o->MathTests = array($this->mt1, $this->mt2);

	  		 $result = $this->invoke('arrayOfObjectsTest', $o);

			 PHPUnit_Framework_Assert::assertType('array', $result->return, 'Failed assert arrayOfObjectsTest return value is array');
			 PHPUnit_Framework_Assert::assertType('MathTest', $result->return[0], 'Failed get assert arrayOfObjectsTest element 0 is type stdClass');
			 PHPUnit_Framework_Assert::assertType('MathTest', $result->return[1], 'Failed get assert arrayOfObjectsTest element 1 is type MathTest');
			 PHPUnit_Framework_Assert::assertEquals($result->return[0]->getA(), 1, 'Failed get arrayOfObjectsTest element 0 "a" property is 1');
			 PHPUnit_Framework_Assert::assertEquals($result->return[0]->getB(), 2, 'Failed get arrayOfObjectsTest element 0 "b" property is 2');
			 PHPUnit_Framework_Assert::assertEquals($result->return[1]->getA(), 5, 'Failed get arrayOfObjectsTest element 1 "a" property is 5');
			 PHPUnit_Framework_Assert::assertEquals($result->return[1]->getB(), 10, 'Failed get arrayOfObjectsTest element 1 "b" property is 10');
	  }

	  public function testArrayOfArraysTest() {

	  		 $o = new arrayOfArraysTest;
	  		 $o->strings = array(array('test1', 'test2'), array('test3', 'test4'));

	  		 // THIS WONT WORK FOR DOC/LITERAL
	  		 // $result = $this->invoke('arrayOfArraysTest', $o);

			 /*
				 Multi-dimensional arrays, such as this are not supported for the document-literal or RPC-literal formats in the current release.
				 However, it is possible to work around this limitation by wrapping the array in a PHP value type and then using an array of these
				 value types.
			 */
	  }

	  public function testArrayAddTest() {

	  		 $o = new arrayAddTest;
	  		 $o->MathTests = array($this->mt1, $this->mt2);

	  		 $result = $this->invoke('arrayAddTest', $o);
			 PHPUnit_Framework_Assert::assertEquals($result->return, 18, 'Failed to assert arrayAddTest sum added up to 18');
	  }

	  private function invoke($function, $parameters) {

	  		  try {
	  		  	     $result = $this->client->__soapCall($function, array($parameters), $this->options);
	  		  	     if(is_soap_fault($result))
	  		  	     	 throw new SoapFault($result);

	  		  	     return $result;
			  }
			  catch(Exception $e) {

					 echo $e->getMessage();
			  }
	  }
}

/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class test {
}
/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class testResponse {
  public $return; // string
}
/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class add {
  public $a; // int
  public $b; // int
}
/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class addResponse {
  public $return; // int
}
/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class subtract {
  public $a; // int
  public $b; // int
}
/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class subtractResponse {
  public $return; // int
}
/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class multiply {
  public $a; // int
  public $b; // int
}
/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class multiplyResponse {
  public $return; // int
}
/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class divide {
  public $a; // int
  public $b; // int
}
/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class divideResponse {
  public $return; // int
}
/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class objectParameterTest {
  public $MathTest; // MathTest
}
/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class objectParameterTestResponse {
  public $return; // int
}
/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class MathTestArray {
  public $MathTests; // MathTest
}
/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class arrayOfObjectsTest {
  public $MathTests; // MathTestArray
}
/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class arrayOfObjectsTestResponse {
  public $return; // MathTestArray
}
/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class stringArray {
  public $strings; // string
}
/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class arrayStringTest {
  public $strings; // stringArray
}
/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class arrayStringTestResponse {
  public $return; // stringArray
}
/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class stringArrayArray {
  public $strings; // stringArray
}
/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class arrayOfArraysTest {
  public $strings; // stringArrayArray
}
/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class arrayOfArraysTestResponse {
  public $return; // stringArrayArray
}
/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class arrayAddTest {
  public $MathTests; // MathTestArray
}
/**
 * @package com.makeabyte.agilephp.test.webservice.soap
 */
class arrayAddTestResponse {
  public $return; // int
}
?>