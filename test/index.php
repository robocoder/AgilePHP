<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

 require '../src/AgilePHP.php';

 try {
        AgilePHP::setFrameworkRoot(realpath(dirname(__FILE__) . '/../src' ));
 		AgilePHP::init();
        AgilePHP::setDefaultTimezone('America/New_York');
        AgilePHP::setDebugMode(true);
    	AgilePHP::setAppName('AgilePHP Framework Tests');

    	MVC::dispatch();
 }
 catch( FrameworkException $e ) {

  	     require_once '../src/mvc/PHTMLRenderer.php';

  	     Log::error($e->getMessage());

  	     $renderer = new PHTMLRenderer();
  	     $renderer->set('title', 'AgilePHP Framework :: Error Page');
	  	 $renderer->set('error', $e->getCode() . '   ' . $e->getMessage() . (AgilePHP::isInDebugMode() ? '<br><pre>' . $e->getTraceAsString() . '</pre>' : ''));
	  	 $renderer->render('error');
 }

?>