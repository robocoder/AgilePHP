<?php

 require_once '../src/AgilePHP.php';

 try {
 		$agilephp = AgilePHP::getFramework();  	
  	    $agilephp->setDisplayPhpErrors( true );
  	    $agilephp->setFrameworkRoot( '/home/jhahn/Apps/eclipse-galileo/workspace/AgilePHP/src' );
  	    $agilephp->setDefaultTimezone( 'America/New_York' );

  		MVC::getInstance()->processRequest();
 }
 catch( Exception $e ) {

  	     require_once '../src/mvc/PHTMLRenderer.php';

  	     Logger::getInstance()->error( $e->getMessage() );

  	     $renderer = new PHTMLRenderer();
  	     $renderer->set( 'title', 'AgilePHP Framework :: Error Page' );
	  	 $renderer->set( 'error', $e->getCode() . '   ' . $e->getMessage() . ($agilephp->isInDebugMode() ? '<br>' . $e->getTraceAsString() : '' ) );
	  	 $renderer->render( 'error' );
 }

?>