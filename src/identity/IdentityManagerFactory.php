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
 * Factory responsible for creating IdentityManager implementations
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.identity
 * @abstract
 */
abstract class IdentityManagerFactory {

	  private static $manager;

	  /**
	   * Singleton IdentityManager constructor. Returns the IdentityManager instance
	   * responsible for the IdentityModel per agilephp.xml.
	   * 
	   * @return IdentityManager An IdentityManager implementation
	   */
	  public static function getManager() {

	  		 if(self::$manager == null) {

	  		     $agilephp_xml = AgilePHP::getWebRoot() . DIRECTORY_SEPARATOR . 'agilephp.xml';
		  		 $xml = simplexml_load_file($agilephp_xml);

		  		 // No Identity configuration present - provide working default configuration
		  		 if(!$xml->identity) {

		  		 	 self::$manager = new IdentityManagerImpl();
		  		 	 self::$manager->setModel(new User());
		  	      	 self::$manager->setModelName('User');
					 self::$manager->setAuthenticator('DefaultAuthenticator');
		  	      	 self::$manager->setForgotPasswdMailer('BasicForgotPasswdMailer');
		  	      	 self::$manager->setResetPasswdMailer('BasicResetPasswdMailer');
		  	      	 self::$manager->setRegistrationMailer('BasicRegistrationMailer');
		  		 }
		  		 else {

			  		 // Configuration provided - Initialize using agilephp.xml configuration
		  		 	 $manager = ((string)$xml->identity->attributes()->manager) ?
		  		 	 		 (string)$xml->identity->attributes()->manager : 'IdentityManagerImpl';
	
		  		 	 self::$manager = new $manager;
		  		 	 Log::debug('Identity::__construct Initalizing manager \'' . self::$manager->getModelName() . '\'.');
	
		  		 	 $authenticator = ((string)$xml->identity->attributes()->authenticator) ?
			  		 		(string)$xml->identity->attributes()->authenticator : 'DefaultAuthenticator';
		  		     self::$manager->setAuthenticator($authenticator);
	
			  	     if($model = (string)$xml->identity->attributes()->model) {
	
			  	      	 self::$manager->setModel(new $model());
				  		 self::$manager->setModelName($model);
			  	     }
			  	     else {
	
			  	      	  self::$manager->setModel(new User());
			  	      	  self::$manager->setModelName('User');
			  	     }
	
			  	     Log::debug('Identity::__construct Initalizing domain model \'' . self::$manager->getModelName() . '\'.');
	
			  		 $forgotPasswdMailer = ((string)$xml->identity->attributes()->forgotPasswdMailer) ?
			  		 		(string)$xml->identity->attributes()->forgotPasswdMailer : 'BasicForgotPasswdMailer';
		  		     self::$manager->setForgotPasswdMailer($forgotPasswdMailer);
	
		  		     $resetPasswdMailer = ((string)$xml->identity->attributes()->resetPasswdMailer) ?
			  		 		(string)$xml->identity->attributes()->resetPasswdMailer : 'BasicResetPasswdMailer';
		  		     self::$manager->setResetPasswdMailer($resetPasswdMailer);
	
			  		 $registrationMailer = ((string)$xml->identity->attributes()->registrationMailer) ?
			  		 		(string)$xml->identity->attributes()->registrationMailer : 'BasicRegistrationMailer';
		  		     self::$manager->setRegistrationMailer($registrationMailer);
		  		 }

		  		 // Initialize Identity from previous session if one exits
		  		 $session = Scope::getSessionScope();
	      		 if($model = $session->get('IDENTITY_MODEL'))
		  		  	self::$manager->setModel($model);
	  		 }

	  		 return self::$manager;
	 }
}
?>