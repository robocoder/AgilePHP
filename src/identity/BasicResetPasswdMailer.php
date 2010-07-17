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
 * Sends plain text emails which contain newly reset passwords
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.identity
 */
class BasicResetPasswdMailer extends Mailer {

	  public function __construct($username, $password, $email) {

	         $appName = AgilePHP::getAppName();

	  	     $this->setTo($email);
	  		 $this->setToName($username);
	  		 $this->setFrom('no-reply@' . $appName);
	  		 $this->setFromName($appName);
	  		 $this->setSubject($appName . ' :: New Password');
	  		 $this->setBody('Your new password is: ' . $password);
	  }
}
?>