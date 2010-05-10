<?php

 ini_set( 'display_errors', '1' );
 error_reporting( E_ALL );

 require_once '../src/AgilePHP.php';

 try {
 		$agilephp = AgilePHP::getFramework();  	
  	    $agilephp->setDisplayPhpErrors( true );

  	    (preg_match( '/microsoft/i', $_SERVER['SERVER_SOFTWARE'] )) ?
      	     $agilephp->setFrameworkRoot( 'c:\inetpub\wwwroot\AgilePHP\src' ) :
      	     $agilephp->setFrameworkRoot( '/home/jhahn/Apps/eclipse-galileo/workspace/AgilePHP/src' );

  	    $agilephp->setDefaultTimezone( 'America/New_York' );

  		MVC::getInstance()->processRequest();
 }
 catch( AgilePHP_Exception $e ) {

  	     require_once '../src/mvc/PHTMLRenderer.php';

  	     Logger::getInstance()->error( $e->getMessage() );

  	     $renderer = new PHTMLRenderer();
  	     $renderer->set( 'title', 'AgilePHP Framework :: Error Page' );
	  	 $renderer->set( 'error', $e->getCode() . '   ' . $e->getMessage() . ($agilephp->isInDebugMode() ? '<br><pre>' . $e->getTraceAsString() . '</pre>' : '' ) );
	  	 $renderer->render( 'error' );
 }

?>