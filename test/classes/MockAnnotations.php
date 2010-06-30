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
 * @package com.makeabyte.agilephp.test.classes
 */

/**
 * A trivial class containing annotations that get tested by the AnnotationTest unit test.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.classes
 */
require_once 'annotations/TestAnnotation1.php';
require_once 'annotations/TestAnnotation2.php';
require_once 'annotations/TestAnnotation3.php';

/**
 * AgilePHP :: Test :: MockAnnotation
 * 
 * Here are some class level annotations...
 * 
 * 
 * These are some annotations that will not be processed since
 * they are inside of a PHP T_DOC_COMMENT.
 * 
 * #@Simple
 * #@TestAnnotation1( name = "Name Value" )
 */

 // These annotations will be processed

 #@Simple
 #@TestAnnotation1( name = "Name Value" )
 #@TestAnnotation1( name = 'Name Value' )
 #@TestAnnotation2( name = "Name 1 value", name2 = "Name 2 value" )
 #@TestAnnotation2( name = 'Name 1 value', name2 = 'Name 2 value' )
  
 #@TestAnnotation3( name = 'Name value', name2 = 'Name 2 value', obj1 = IdentityManagerFactory::getManager() )
 #@TestAnnotation3( name = "Name value", name2 = { key1 = "test", "test2", key3 = 'test3' }, obj1 = IdentityManagerFactory::getManager(), array2 = { newKey = "test", newKey2 = 'test again' }, array3 = { "test", "test2" } )

/**
 * The classic start of an eclipse file...
 * @author jhahn
 *
 */
class MockAnnotations {

	  /**
	   * This is an annotated property. PHPdoc style comments do not interfere with parsing...
	   * 
	   * @var string
	   */
	  #@TestAnnotation1( name = "value" )
	  #@TestAnnotation3( name = "Name value", name2 = { key1 = "test", "test2", key3 = 'test3' }, obj1 = IdentityManagerFactory::getManager(), array2 = { newKey = "test", newKey2 = 'test again' }, array3 = { "test", "test2" } )
	  private $foo;
	  
	  #@Simple
	  private $bar;

	  #@Simple
	  #@TestAnnotation3( name = 'value', name2 = 'value 2', obj1 = new User() )
	  /**
	   * Constructors can have annotations too. Note that you can annotate above or below
	   * the PHPdoc.
	   * 
	   * @return void
	   */
	  public function __construct() { }

	  #@TestAnnotation1( name = "value" )
	  public function method1() { }

	  public function method2() { }

	  #@Simple
	  #@TestAnnotation3( name = "value", name2 = "value 2", obj1 = IdentityManagerFactory::getManager() )
	  public function method3() { }

	  #@TestAnnotation2( name = "Name 1 value", name2 = "Name 2 value" )
	  #@TestAnnotation3( name = "value", name2 = "value 2", obj1 = IdentityManagerFactory::getManager() )
	  #@TestAnnotation3( name = "Name value", name2 = { key1 = "test", "test2", key3 = 'test3' }, obj1 = IdentityManagerFactory::getManager(), array2 = { newKey = "test", newKey2 = 'test again' }, array3 = { "test", "test2" } )
	  public function method4() { }
}
?>