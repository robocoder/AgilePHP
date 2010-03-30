<?php
abstract class BaseExtController extends BaseController {

		 public function __construct() {

		  	    parent::__construct();
		  	    parent::createRenderer( 'ExtFormRenderer' );
		 }
}
?>