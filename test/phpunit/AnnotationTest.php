<?php

/**
 * @package com.makeabyte.agilephp.test.annotation
 */
class AnnotationTest extends BaseTest {

	  /**
	   * @test
	   */
	  public function getClassLevelAnnotations() {

	  		 $class = Annotation::getClass( 'MockAnnotations' );

	  		 PHPUnit_Framework_Assert::assertType( 'AnnotatedClass', $class, 'Failed to assert type is AnnotatedClass' );

	  		 PHPUnit_Framework_Assert::assertTrue( $class->hasAnnotation( 'Simple' ) );
	  		 PHPUnit_Framework_Assert::assertTrue( $class->hasAnnotation( 'TestAnnotation1' ) );
	  		 PHPUnit_Framework_Assert::assertTrue( $class->hasAnnotation( 'TestAnnotation2' ) );
	  		 PHPUnit_Framework_Assert::assertTrue( $class->hasAnnotation( 'TestAnnotation3' ) );

	  		 PHPUnit_Framework_Assert::assertFalse( $class->hasAnnotation( 'TestAnnotation4' ) );

	  		 #@Simple
	  		 $s = $class->getAnnotation( 'Simple' );	  		 
	  		 PHPUnit_Framework_Assert::assertType( 'Simple', $s, 'Failed to assert getAnnotation() returned type Simple for annotation Simple' );

	  		 #@TestAnnotation1( name = "Name Value" )
 			 #@TestAnnotation1( name = 'Name Value' )
	  		 $ta1 = $class->getAnnotation( 'TestAnnotation1' );
	  		 PHPUnit_Framework_Assert::assertType( 'array', $ta1, 'Failed to assert getAnnotation() returned array for annotation TestAnnotation1' );
			 PHPUnit_Framework_Assert::assertType( 'TestAnnotation1', $ta1[0], 'Failed to assert first array element returned by getAnnotation() is an instance of TestAnnotation1' );
			 PHPUnit_Framework_Assert::assertType( 'TestAnnotation1', $ta1[1], 'Failed to assert second array element returned by getAnnotation() is an instance of TestAnnotation1' );

			 #@TestAnnotation3( name = 'Name value', name2 = 'Name 2 value', obj1 = IdentityManagerFactory::getManager() );
	  		 #@TestAnnotation3( name = "Name value", name2 = { key1 = "test", "test2", key3 = 'test3' }, obj1 = IdentityManagerFactory::getManager(), array2 = { newKey = "test", newKey2 = 'test again' }, array3 = { "test", "test2" } )
	  		 $ta3 = $class->getAnnotation( 'TestAnnotation3' );
	  		 PHPUnit_Framework_Assert::assertType( 'array', $ta3, 'Failed to assert getAnnotation() returned array for annotation TestAnnotation3' );
	  		 PHPUnit_Framework_Assert::assertType( 'TestAnnotation3', $ta3[0], 'Failed to assert first array element returned by getAnnotation() is an instance of TestAnnotation3' );
			 PHPUnit_Framework_Assert::assertType( 'TestAnnotation3', $ta3[1], 'Failed to assert second array element returned by getAnnotation() is an instance of TestAnnotation3' );

			 PHPUnit_Framework_Assert::assertType( 'string', $ta3[0]->name, 'Failed to assert TestAnnotation3::name element 1 contains a string value' );
			 PHPUnit_Framework_Assert::assertType( 'IdentityManager', $ta3[0]->obj1, 'Failed to assert TestAnnotation3::obj1 element 1 is type IdentityManager' );
			 PHPUnit_Framework_Assert::assertTrue( $ta3[0]->obj1 instanceof IdentityManager, 'Failed to assert TestAnnotation3::obj1 element 1 is an instance of IdentityManager' );
			 PHPUnit_Framework_Assert::assertTrue( $ta3[0]->obj1->getModel() instanceof User, 'Failed to assert TestAnnotation3::obj1::model is an instance of User' );

			 PHPUnit_Framework_Assert::assertType( 'string', $ta3[1]->name, 'Failed to assert TestAnnotation3::name element 2 contains a string value' );
			 PHPUnit_Framework_Assert::assertType( 'array', $ta3[1]->name2, 'Failed to assert TestAnnotation3::name2 element 2 contains an array value' );
			 PHPUnit_Framework_Assert::assertEquals( 'test', $ta3[1]->name2['key1'], 'Failed to get TestAnnotation3::name2 element 2, element 1 by associative key index' );
			 PHPUnit_Framework_Assert::assertEquals( 'test2', $ta3[1]->name2[0], 'Failed to get TestAnnotation3::name2 element 2, element 2 by numeric key index 0' );
			 PHPUnit_Framework_Assert::assertType( 'IdentityManager', $ta3[1]->obj1, 'Failed to assert TestAnnotation3::obj1 element 2 is type IdentityManager' );
			 PHPUnit_Framework_Assert::assertTrue( $ta3[1]->obj1 instanceof IdentityManager, 'Failed to assert TestAnnotation3::obj1 element 2 is an instance of IdentityManager' );
	  }

	  /**
	   * @test
	   */
	  public function getMethodAnnoationsByMethodName() {

	  		 $class = Annotation::getClass( 'MockAnnotations' );

	  		 #@TestAnnotation1( name = "value" )
	  		 $method1 = Annotation::getMethod( 'MockAnnotations', 'method1' );
	  		 PHPUnit_Framework_Assert::assertTrue( $method1->hasAnnotation( 'TestAnnotation1' ), 'Failed to assert that method1 contains TestAnnotation1 annotation using Annotation::getMethod to obtain a reference' );

	  		 #@TestAnnotation1( name = "value" )
	  		 $m1 = $class->getMethod( 'method1' );
	  		 $annotations = $m1->getAnnotations();
	  		 PHPUnit_Framework_Assert::assertTrue( $m1->hasAnnotation( 'TestAnnotation1' ), 'Failed to assert that method1 contains TestAnnotation1 annotation using AnnotatedClass::getMethod to obtain a reference' );
	  		 PHPUnit_Framework_Assert::assertType( 'TestAnnotation1', $annotations[0], 'Failed to get method1 annotations' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'value', $annotations[0]->name, 'Failed to get method1 annotation name property value' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'value', $m1->getAnnotation( 'TestAnnotation1' )->name, 'Failed to get value by getAnnotation( $name )' );
	  		 PHPUnit_Framework_Assert::assertTrue( $m1->isAnnotated(), 'Failed to assert that method1 is annotated using isAnnotated()' );

	  		 $m2 = $class->getMethod( 'method2' );
	  		 PHPUnit_Framework_Assert::assertFalse( $m2->isAnnotated(), 'Failed to assert that method2 is NOT annotated using isAnnotated()' );

	  		 #@TestAnnotation2( name = "Name 1 value", name2 = "Name 2 value" )
	  		 #@TestAnnotation3( name = "value", name2 = "value 2", obj1 = IdentityManagerFactory::getManager() )
	  		 #@TestAnnotation3( name = "Name value", name2 = { key1 = "test", "test2", key3 = 'test3' }, obj1 = IdentityManagerFactory::getManager(), array2 = { newKey = "test", newKey2 = 'test again' }, array3 = { "test", "test2" } )
	  		 $m4 = $class->getMethod( 'method4' );
	  		 PHPUnit_Framework_Assert::assertTrue( $m4->isAnnotated(), 'Failed to assert that method4 is annotated using isAnnotated()' );
	  		 $annotations = $m4->getAnnotations( 'TestAnnotation3' );
	  		 PHPUnit_Framework_Assert::assertType( 'array', $annotations, 'Failed to assert that getAnnotations( \'TestAnnotation3\' ) on method4 is type array' );
	  }

	  /**
	   * @test
	   */
	  public function getMethodLevelAnnotations() {

	  		 $class = Annotation::getClass( 'MockAnnotations' );
	  		 $methods = $class->getMethods();
	  		 
	  		 PHPUnit_Framework_Assert::assertType( 'array', $methods, 'Failed to assert AnnotatedClass::getMethods() returned type array' );

	  		 #@TestAnnotation2( name = "Name 1 value", name2 = "Name 2 value" )
			 #@TestAnnotation3( name = "value", name2 = "value 2", obj1 = IdentityManagerFactory::getManager() )
	  		 #@TestAnnotation3( name = "Name value", name2 = { key1 = "test", "test2", key3 = 'test3' }, obj1 = IdentityManagerFactory::getManager(), array2 = { newKey = "test", newKey2 = 'test again' }, array3 = { "test", "test2" } )
	  		 PHPUnit_Framework_Assert::assertType( 'AnnotatedMethod', $methods[2], 'Failed to assert type is AnnotatedMethod' );
	  		 PHPUnit_Framework_Assert::assertType( 'array', $methods[2]->getAnnotations(), 'Failed to assert AnnotatedMethod object at element 3 contains an array of annotations' );
	  		 PHPUnit_Framework_Assert::assertEquals( 'value', $methods[2]->getAnnotation( 'TestAnnotation3' )->name, 'Failed to assert AnnotatedMethod at element 3 TestAnnotation3::name annotation is equal to \'value\'.' );
	  		 PHPUnit_Framework_Assert::assertType( 'IdentityManager', $methods[2]->getAnnotation( 'TestAnnotation3' )->obj1, 'Failed to assert method4\'s first TestAnnotation3::obj1 annotation is type IdentityManager' );
	  }

	  /**
	   * @test
	   */
	  public function getPropertyLevelAnnotations() {

	  		 $class = Annotation::getClass( 'MockAnnotations' );
	  		 $properties = $class->getProperties();

	  		 PHPUnit_Framework_Assert::assertType( 'array', $properties, 'Failed to assert AnnotatedClass::getProperties() returned type array' );

	  		 #@TestAnnotation1( name = "value" )
	  		 #@TestAnnotation3( name = "Name value", name2 = { key1 = "test", "test2", key3 = 'test3' }, obj1 = IdentityManagerFactory::getManager(), array2 = { newKey = "test", newKey2 = 'test again' }, array3 = { "test", "test2" } )
			 $foo = $class->getProperty( 'foo' );
			 $annotations = $foo->getAnnotations();
			 PHPUnit_Framework_Assert::assertType( 'AnnotatedProperty', $foo, 'Failed to assert type is AnnotatedProperty' );
			 PHPUnit_Framework_Assert::assertType( 'TestAnnotation1', $foo->getAnnotation( 'TestAnnotation1' ), 'Failed to assert property foo annotation element 1 returned type TestAnnotation1' );
			 PHPUnit_Framework_Assert::assertType( 'TestAnnotation3', $annotations[1], 'Failed to assert property foo annotation element 2 returned type TestAnnotation3' );
			 
			 $props = $class->getProperties();
			 $bProcessed = false;
			 foreach( $props as $prop ) {
			 	if( $prop->getName() == 'bar' ) {

			 		$bProcessed = true;
			 		PHPUnit_Framework_Assert::assertType( 'Simple', $prop->getAnnotation( 'Simple' ), 'Failed to assert Simple annotation is of type Simple' );
			 	}
			 }

			 PHPUnit_Framework_Assert::assertTrue( $bProcessed, 'Failed to get properties using AnnotationProperty reflection method getProperties()' );
	  }
}
?>