<?php
/**
 * @package com.makeabyte.agilephp.test
 */
class i18nTest extends PHPUnit_Framework_TestCase {

	   /**
	    * @test
	    */
	   public function translateSpanish() {

	   		  $i18n = i18n::getInstance();
	   		  $i18n->setLocale( 'es_ES' );
	   		  $i18n->setDomain( 'messages' );

	   		  $translation = i18n::translate( 'Welcome to the demo application' );

	   		  PHPUnit_Framework_Assert::assertEquals( 1, preg_match( '/Bienvenido a la aplicacion de demostracion/', $translation ), 'Failed to translate to spanish' );
	   }

	   /**
	    * @test
	    */
	   public function translateFrench() {

	   		  $i18n = i18n::getInstance();
	   		  $i18n->setLocale( 'fr_FR' );
	   		  $i18n->setDomain( 'messages' );

	   		  $translation = i18n::translate( 'Welcome to the demo application' );

	   		  PHPUnit_Framework_Assert::assertEquals( 1, preg_match( '/Bienvenue a l\'application de demonstration/', $translation ), 'Failed to translate french' );
	   }

	   /**
	    * @test
	    */
	   public function translateGerman() {

	   		  $i18n = i18n::getInstance();
	   		  $i18n->setLocale( 'de_DE' );
	   		  $i18n->setDomain( 'messages' );

	   		  $translation = i18n::translate( 'Welcome to the demo application' );

	   		  PHPUnit_Framework_Assert::assertEquals( 1, preg_match( '/Willkommen auf der Demo-Applikation/', $translation ), 'Failed to translate german' );
	   }
}
?>