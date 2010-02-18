<?php

ini_set( 'include_path', ini_get('include_path') . PATH_SEPARATOR . '/usr/share/php/PHPUnit' );

require_once 'PHPUnit/Framework.php';
require_once '../src/AgilePHP.php';

class BaseTest extends PHPUnit_Framework_TestCase {

	  private $agilephp;

	  public function __construct() {

	  	      $this->agilephp = AgilePHP::getFramework();
	  	      $this->agilephp->setDisplayPhpErrors( true );

	  	   	  if( preg_match( '/microsoft/i', isset( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : '' ) ) { 

	  	   	 	  $this->agilephp->setWebRoot( 'D:\Documents and Settings\JHahn\My Documents\Eclipse Workspace\AgilePHP\test' );
      	     	  $this->agilephp->setFrameworkRoot( 'D:\Documents and Settings\JHahn\My Documents\Eclipse Workspace\AgilePHP\src' );
			  }
			  else { 

			 	  $this->agilephp->setWebRoot( '/home/jhahn/Apps/eclipse-galileo/workspace/AgilePHP/test' );
      	     	  $this->agilephp->setFrameworkRoot( '/home/jhahn/Apps/eclipse-galileo/workspace/AgilePHP/src' );
	 		  }

      	      $this->agilephp->setDefaultTimezone( 'America/New_York' );
	  }

	  public function testAgilePHPNotNull() {

	  	     PHPUnit_Framework_Assert::assertNotNull( $this->agilephp, "Failed to create an instance of the AgilePHP framework." );
	  }	 
}
?>