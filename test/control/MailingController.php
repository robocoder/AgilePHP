<?php

class MailingController extends BaseModelActionController {

	  protected $model;

	  public function __construct() {

	  		 $this->model = new Mailing();

	  	     parent::__construct();
	  }
  
	  public function getModel() {
	  	
	  	     return $this->model;
	  }

	  public function index() {

	  		 parent::modelList();
	  }

	  public function mailingBroadcast( $process = false) {

	  	     if( $process == true ) {

	  	     	 $request = Scope::getInstance()->getRequestScope();
	  	     	 $error = null;
		  		 $message = null;
		  		 $failed = array();
		  		 $mailer = new Mailer();
		  		 $mailer->setFromName( 'AgilePHP Framework' );
		  		 $mailer->setFrom( 'root@localhost' );

	  	     	 $this->createQuery( 'SELECT * FROM mailing' );
	  	     	 $this->executeQuery();
	  	     	 $recipientCount = 0;

	  	     	 foreach( $this->getResultListAsModels() as $model ) {

			  	     	  if( !$model->isEnabled() ) continue;

			  	     	  try {
				  	     	    $mailer->setToName( $model->getName() );
				  	     	    $mailer->setTo( $model->getEmail() );
				  	     	    $mailer->setSubject( $request->get( 'subject' ) );
				  	     	    $mailer->setBody( $request->get( 'body' ) . "\n\n" . $request->get( 'signature' ) );
				  	     	    $mailer->send();

				  	     	    $recipientCount++;
			  	     	  }
			  	     	  catch( AgilePHP_Exception $e ) {

			  	     	  		 array_push( $failed, $model->getEmail() );
			  	     	  }
	  	     	 }

		  	     if( count( $failedArray ) ) {
	
		  	     	 $this->getRenderer()->set( 'error', 'Email broadcast failed.' );
		  	     	 $this->getRenderer()->set( 'failedEmails', $failed );
		  	     }
		  	     else {
	
		  	     	 $this->getRenderer()->set( 'message', 'Email broadcast sent to ' . $recipientCount . ' recipients.' );
		  	     }
	  	     }

	  		 $this->getRenderer()->render( 'admin_broadcast' );
	  }
}