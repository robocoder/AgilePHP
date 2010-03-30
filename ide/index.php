<?php
/**
 * AgilePHP Web Based IDE
 * 
 * @package com.makeabyte.agilephp.ide
 */

/**
 * This is the main page responsible for dispatching MVC requests. This application
 * is design to work solely within the context of an Ext RIA application.
 * 
 * @author Jeremy Hahn
 * @version 0.1
 * @see http://www.extjs.com
 */
 require_once '../src/AgilePHP.php';

 try {
		$agilephp = AgilePHP::getFramework();
		$agilephp->setDefaultTimezone( 'America/New_York' );
		$agilephp->setFrameworkRoot( '/home/jhahn/Apps/eclipse-galileo/workspace/AgilePHP/src' );

		MVC::getInstance()->processRequest();
 }
 catch( Exception $e ) {

  	     Logger::getInstance()->error( $e->getMessage() );

		 //$renderer = new ExtRenderer();
		 //$renderer->setError( ($agilephp->isInDebugMode() ? '<pre>' . $e->getTraceAsString() . '</pre>' : $e->getMessage() ) );
		 //$renderer->render( false );

  	     $renderer = new PHTMLRenderer();
  	     echo ($agilephp->isInDebugMode() ? $e->getMessage() . '<pre>' . $e->getTraceAsString() . '</pre>' : $e->getMessage() );
 }
?>