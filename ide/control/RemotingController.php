<?php
class RemotingController extends Remoting {

	  public function __construct() {
	  		 parent::__construct();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/mvc/BaseController#index()
	   * @throws AgilePHP_RemotingException
	   */
	  public function index() {
	  		 throw new AgilePHP_RemotingException( 'Malformed Request' );
	  }

	  /**
	   * Loads the specified class
	   *  
	   * @param $class The class to remote
	   * @return void
	   * @throws AgilePHP_RemotingException
	   */
	  public function load( $class ) {

	  		 if( !isset( $class ) || count( $class ) < 1 )
				 throw new AgilePHP_RemotingException( 'Class required' );

			 parent::__construct( $class );
			 parent::createStub();
	  }
}
?>