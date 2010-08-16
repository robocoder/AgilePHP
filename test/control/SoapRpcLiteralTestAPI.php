<?php
/**
 * AgilePHP Framework :: The Rapid "for developers" PHP5 framework
 * Copyright (C) 2009-2010 Make A Byte, inc
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package com.makeabyte.agilephp.test.control
 */

/**
 * Test class used to mimic an API exposing some application logic via SOAP using RPC/Literal WSDL.
 *
 * NOTE: The AgilePHP #@WebMethod annotation authorizes a method for inclusion during WSDL generation.
 *
 * 		 The AgilePHP #@WSDL interceptor uses the data types specified in
 *	     the PHP-doc comment blocks (specifically the @param and @return annotations) during
 *	     WSDL generation. If these types are not present, the #@WSDL generator will use xsd:anyType.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.control
 */

#@WebService(serviceName = 'TestAPIService', targetNamespace = 'http://localhost/test/index.php/SoapRpcLiteralTestAPI')
#@SOAPBinding(style = SOAPStyle::RPC, use = SOAPStyle::LITERAL)
class SoapRpcLiteralTestAPI extends SOAPService {

	  /**
	   * The #@In interceptor performs Dependancy Injection. Will be a new
	   * instance of MathTest at runtime.
	   */
	  public $MathTest;

	  public function __construct() {

	  		 $this->MathTest = new MathTest();
	  }

	  /**
	   * The #@WSDL interceptor will handle generating WSDL document for this web service class
	   * based on annotations and PHP-doc comments that describe parameter data types and return
	   * types.
	   */
	  #@WSDL
	  public function wsdl() {}

	  /**
	   * Tests the TestAPI web service by outputting the string 'TestAPI works!'.
	   *
	   * @return string
	   */
	  #@WebMethod
	  public function test() {

	  		 return 'TestAPI works!';
	  }

	  /**
	   * Adds two numbers.
	   *
	   * @param int $a Base integer number
	   * @param int $b The number to add to the base
	   * @return int The sum
	   */
	  #@WebMethod
	  public function add($a, $b) {

	  		 return $this->MathTest->add($a, $b);
	  }

	  /**
	   * Subtracts two numbers.
	   *
	   * @param int $a Base integer number
	   * @param int $b The number to subtract from the base
	   * @return int The difference
	   */
	  #@WebMethod
	  public function subtract($a, $b) {

	  		 return $this->MathTest->subtract($a, $b);
	  }

	  /**
	   * Multiplies two numbers.
	   *
	   * @param int $a Base integer number
	   * @param int $b The number to multiply to the base
	   * @return int The product
	   */
	  #@WebMethod
	  public function multiply($a, $b) {

	  		 return $this->MathTest->multiply($a, $b);
	  }

	  /**
	   * Divides two numbers.
	   *
	   * @param int $a Base integer number
	   * @param int $b The divisor of the base
	   * @return int The quotient
	   */
	  #@WebMethod
	  public function divide($a, $b) {

	  		 return $this->MathTest->divide($a, $b);
	  }

	  /**
	   * Tests complex data type 'object' parameter.
	   *
	   * @param MathTest $MathTest An instance of MathTest
	   * @return int Returns the sum of both A and B fields in the MathTest object.
	   */
	  #@WebMethod
	  public function objectParameterTest(stdClass $MathTest) {

	  		 return ($MathTest->a + $MathTest->b);
	  }

	  /**
	   * Tests complex data type array parameter.
	   *
	   * @param MathTest[] $MathTests An array of MathTest instances
	   * @return MathTest[] Returns the same array that was passed in.
	   */
	  #@WebMethod
	  public function arrayOfObjectsTest(stdClass $MathTests) {

	  		 return $MathTests;
	  }

	  /**
	   * Tests the ability to receive and return a simple string array.
	   *
	   * @param string[] $strings An array of strings
	   * @return string[] The same strings that were passed in
	   */
	  #@WebMethod
	  public function arrayStringTest(stdClass $strings) {

	  		 return $strings;
	  }

	  /**
	   * Tests the ability to receive and return a simple string array.
	   *
	   * @param string[][] $strings A multi-dimensional array of strings
	   * @return string[][] The same multi-dimensional array that was passed in
	   */
	  #@WebMethod
	  public function arrayOfArraysTest(stdClass $strings) {

	  		 return $strings;
	  }

	  /**
	   * Tests complex data type array of objects parameter.
	   *
	   * @param MathTest[] $MathTests An array of MathTest instances
	   * @return int Adds A and B for each array and returns the sum of all MathTest objects
	   */
	  #@WebMethod
	  public function arrayAddTest(stdClass $MathTests) {

			 $sum = 0;
	  		 foreach($MathTests->MathTests as $MathTest)
	  		 	$sum += ($MathTest->a + $MathTest->b);

	  		 return $sum;
	  }
}
?>