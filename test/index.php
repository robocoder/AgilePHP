<?php

 require '../src/AgilePHP.php';

 try {
 		AgilePHP::init();
        AgilePHP::setDefaultTimezone('America/New_York');
        AgilePHP::setDebugMode(true);
  	    AgilePHP::setDisplayPhpErrors(true);
    	AgilePHP::setFrameworkRoot(realpath(dirname(__FILE__) . '/../src' ));
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