<?php

class RemotingController extends Remoting {

	  public function __construct() { }

	  /**
	   * Loads the specified class
	   *  
	   * @param $class The class to remote
	   * @return void
	   */
	  public function load( $class ) {

	  		 if( !isset( $class ) || count( $class ) < 1 )
				 throw new AgilePHP_RemotingException( 'Class required' );

			 parent::__construct( $class );
			 parent::createStub();
	  }
}
?>