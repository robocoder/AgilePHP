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
 * @package com.makeabyte.agilephp.identity
 */

/**
 * Sends plain text registration confirmation email.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.identity
 */ 
class BasicRegistrationMailer extends Mailer {

	  public function __construct($token) {

	  	     $url = (@$_SERVER['HTTPS'] != null) ? 'https://' : 'http://';
	  	     $url .= (@$_SERVER['HTTP_HOST'] != null) ? $_SERVER['HTTP_HOST'] : 'localhost';
	  	     $url .= AgilePHP::getRequestBase() . '/LoginController/confirm/';

	  	     $appName = AgilePHP::getAppName();

	  		 $this->setTo(Identity::getEmail());
	  		 $this->setToName(Identity::getUsername());
	  		 $this->setFrom('no-reply@' . $appName);
	  		 $this->setFromName($appName);
	  		 $this->setSubject($appName . ' :: Registration Confirmation');
	  		 $this->setBody('Click on the following link to confirm your registration: ' . PHP_EOL . $url .
	  		 				 	$token . '/' . Scope::getSessionScope()->getSessionId());
	  }
}
?>