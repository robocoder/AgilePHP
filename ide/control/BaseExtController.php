<?php

abstract class BaseExtController extends BaseController {

		 public function __construct() {

		  	    parent::__construct();
		  	    parent::createRenderer( 'ExtFormRenderer' );
		 }

		 /**
		  * Custom PHP error handling function which throws an AgilePHP_Exception instead of reporting
		  * a PHP warning.
		  * 
		  * @param Integer $errno Error number
		  * @param String $errmsg Error message
		  * @param String $errfile The name of the file that caused the error
		  * @param Integer $errline The line number that caused the error
		  * @return void
		  * @throws AgilePHP_Exception
		  */
	 	  public static function ErrorHandler( $errno, $errmsg, $errfile, $errline ) {

	    	     throw new AgilePHP_Exception( $errmsg, $errno );
		  }
}
?>