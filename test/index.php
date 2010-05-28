<?php

 ini_set( 'display_errors', '1' );
 error_reporting( E_ALL );

 require_once '../src/AgilePHP.php';

 try {
 		$agilephp = AgilePHP::getFramework();  	
  	    $agilephp->setDisplayPhpErrors( true );
    	$agilephp->setFrameworkRoot( realpath( dirname( __FILE__ ) . '/../src' ) );
  	    $agilephp->setDefaultTimezone( 'America/New_York' );

  		MVC::getInstance()->processRequest();
 }
 catch( AgilePHP_Exception $e ) {

  	     require_once '../src/mvc/PHTMLRenderer.php';

  	     Logger::error( $e->getMessage() );

  	     $renderer = new PHTMLRenderer();
  	     $renderer->set( 'title', 'AgilePHP Framework :: Error Page' );
	  	 $renderer->set( 'error', $e->getCode() . '   ' . $e->getMessage() . ($agilephp->isInDebugMode() ? '<br><pre>' . $e->getTraceAsString() . '</pre>' : '' ) );
	  	 $renderer->render( 'error' );
 }

?>