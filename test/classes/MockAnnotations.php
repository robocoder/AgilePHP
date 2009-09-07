<?php

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
  
 #@TestAnnotation3( name = 'Name value', name2 = 'Name 2 value', obj1 = new Role( 'admin' ) )
 #@TestAnnotation3( name = "Name value", name2 = { key1 = "test", "test2", key3 = 'test3' }, obj1 = Logger::getInstance(), array2 = { newKey = "test", newKey2 = 'test again' }, array3 = { "test", "test2" } )

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
	  #@TestAnnotation3( name = "Name value", name2 = { key1 = "test", "test2", key3 = 'test3' }, obj1 = Logger::getInstance(), array2 = { newKey = "test", newKey2 = 'test again' }, array3 = { "test", "test2" } )
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
	  #@TestAnnotation3( name = "value", name2 = "value 2", obj1 = Logger::getInstance() )
	  public function method3() { }

	  #@TestAnnotation2( name = "Name 1 value", name2 = "Name 2 value" )
	  #@TestAnnotation3( name = "value", name2 = "value 2", obj1 = Logger::getInstance() )
	  #@TestAnnotation3( name = "Name value", name2 = { key1 = "test", "test2", key3 = 'test3' }, obj1 = Logger::getInstance(), array2 = { newKey = "test", newKey2 = 'test again' }, array3 = { "test", "test2" } )
	  public function method4() { }
}
?>