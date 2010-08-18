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
 * @package com.makeabyte.agilephp
 */

/**
 * Sends simple plain text emails.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 */
abstract class Mailer {

	  private $to;
	  private $toName;
	  private $from;
	  private $fromName;
	  private $subject;
	  private $body;

	  /**
	   * Sets the email address of the person receiving the email
	   * 
	   * @param string $email The recipients email address
	   * @return void
	   */
	  public function setTo($email) {
	  	
	  		 $this->to = $email;
	  }

	  /**
	   * Sets the name of the person receiving the email
	   * 
	   * @param string $name The recipients name
	   * @return void
	   */
	  public function setToName($name) {

	  		 $this->name = $name;
	  }

	  /**
	   * Sets the email of the person sending the email
	   * 
	   * @param string $email The senders email address
	   * @return void
	   */
	  public function setFrom($email) {
	  	
	  		 $this->from = $email;
	  }

	  /**
	   * Sets the name of the person sending the email
	   * 
	   * @param string $name The senders name
	   * @return void
	   */
	  public function setFromName($name) {
	  	
	  		 $this->fromName = $name;
	  }
	  
	  /**
	   * Sets the subject line of the email
	   * 
	   * @param string $subject The text to display in the subject line
	   * @return void
	   */
	  public function setSubject($subject) {
	  	
	  		 $this->subject = $subject;
	  }

	  /**
	   * Sets the email message body
	   * 
	   * @param string $body The email message body
	   * @return void
	   */
	  public function setBody($body) {

	  		 $this->body = $body;
	  }

	  /**
	   * Sends the email
	   * 
	   * @return void
	   * @throws FrameworkException if there was an error sending
	   */
	  public function send() {

	  		 $headers = 'From: ' . $this->fromName . ' <' . $this->from . '>' . "\n";
        	 $headers .= 'Reply-To: ' . $this->from . "\n";
          	 $headers .= 'Return-Path: ' . $this->from . "\n";
        	 $headers .= 'X-mailer: AgilePHP Framework on PHP (' . phpversion() . ')' . "\n";

        	 if(!mail($this->to, $this->subject, $this->body, $headers))
        	 	 throw new FrameworkException('Error sending email');
	  }
}
?>