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
 * @package com.makeabyte.agilephp.test.components
 */

/**
 * Sends a simple plain text email.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.components
 * @version 0.2a
 */
class Mailer {

	  private $to;
	  private $toName;
	  private $from;
	  private $fromName;
	  private $subject;
	  private $body;

	  public function __construct() { }
  
	  public function setTo( $toEmail ) {
	  	
	  		 $this->to = $toEmail;
	  }

	  public function setToName( $toName ) {

	  		 $this->toName = $toName;
	  }

	  public function setFrom( $fromEmail ) {
	  	
	  		 $this->from = $fromEmail;
	  }
	  
	  public function setFromName( $fromName ) {
	  	
	  		 $this->fromName = $fromName;
	  }
	  
	  public function setSubject( $subject ) {
	  	
	  		 $this->subject = $subject;
	  }
	  
	  public function setBody( $body ) {
	  	
	  		 $this->body = $body;
	  }

	  public function send() {

	  		 $headers = 'From: ' . $this->fromName . ' <' . $this->from . '>' . "\n";
        	 $headers .= 'Reply-To: ' . $this->from . "\n";
          	 $headers .= 'Return-Path: ' . $this->from . "\n";
        	 $headers .= 'X-mailer: AgilePHP Framework on PHP (' . phpversion() . ')' . "\n";

        	 if( !mail( $this->to, $this->subject, $this->body, $headers ) )
        	 	 throw new AgilePHP_Exception( 'Error sending email' );
	  }
}
?>