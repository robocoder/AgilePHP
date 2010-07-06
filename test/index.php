<?php

 ini_set( 'display_errors', '1' );
 error_reporting( E_ALL );

 require_once '../src/AgilePHP.php';


 try {
 		$agilephp = AgilePHP::getFramework();
 		$agilephp->setDefaultTimezone( 'America/New_York' );
  	    $agilephp->setDisplayPhpErrors( true );
    	$agilephp->setFrameworkRoot( realpath( dirname( __FILE__ ) . '/../src' ) );
    	$agilephp->setAppName( 'AgilePHP Framework Tests' );

  		MVC::getInstance()->dispatch();
 }
 catch( FrameworkException $e ) {

  	     require_once '../src/mvc/PHTMLRenderer.php';

  	     Log::error( $e->getMessage() );

  	     $renderer = new PHTMLRenderer();
  	     $renderer->set( 'title', 'AgilePHP Framework :: Error Page' );
	  	 $renderer->set( 'error', $e->getCode() . '   ' . $e->getMessage() . ($agilephp->isInDebugMode() ? '<br><pre>' . $e->getTraceAsString() . '</pre>' : '' ) );
	  	 $renderer->render( 'error' );
 }

?>