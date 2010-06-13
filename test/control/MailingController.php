<?php
/**
 * AgilePHP Framework :: The Rapid "for developers" PHP5 framework
 * Copyright (C) 2009-2010 Make A Byte, inc
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package com.makeabyte.agilephp.test.control
 */

/**
 * Responsible for handling all processing and view rendering for the mailing
 * list  module.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.control
 */
class MailingController extends BaseModelActionController {

	  protected $model;

	  public function __construct() {

	  		 $this->model = new Mailing();

	  	     parent::__construct();
	  }
  
	  public function getModel() {
	  	
	  	     return $this->model;
	  }

	  public function mailingBroadcast( $process = false) {

	  	     if( $process == true ) {

	  	     	 $request = Scope::getRequestScope();
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
			  	     	  catch( FrameworkException $e ) {

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