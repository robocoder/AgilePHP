<?php
/**
 * AgilePHP Web Based IDE
 * 
 * @package com.makeabyte.agilephp.studio
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
      AgilePHP::init();
	  AgilePHP::setDefaultTimezone('America/New_York');
	  AgilePHP::setFrameworkRoot(realpath(dirname(__FILE__) . '/../src'));
	  AgilePHP::handleErrors();

	  MVC::dispatch();
}
catch(Exception $e) {

  	   Log::error($e->getMessage() . DIRECTORY_SEPARATOR . $e->getTraceAsString());

	   $renderer = new ExtFormRenderer();
	   $renderer->setError($e->getMessage());
	   $renderer->render(false);
}
?>