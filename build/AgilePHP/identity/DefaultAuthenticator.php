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
 * Default authentication handler.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.identity
 */
class DefaultAuthenticator implements Authentication {

	  public static function authenticate($username, $password) {

	  		 $model = Identity::getModel();
	  		 
	  		 if(method_exists($model, 'getInterceptedInstance')) {

	  		    if(!$model->getInterceptedInstance() instanceof IdentityModel)
	  		       throw new FrameworkException('Model must implement IdentityModel interface');
	  		 }
	  		 else
	  		    if(!$model instanceof IdentityModel)
	  		       throw new FrameworkException('Model must implement IdentityModel interface');

	  	     Log::debug('DefaultAuthenticator::authenticate Authenticating username \'' . $username . '\' with password \'' . $password . '\'.');

	  		 $model->setUsername($username); // #@Id interceptor populates ActiveRecord state

	  		 if(!$model->getPassword()) return false;

	  		 $crypto = new Crypto();
	  		 $hashed = $crypto->getDigest($password);

			 if(!preg_match('/' . $hashed . '/', $model->getPassword())) return false;

	  		 if($model->getEnabled() == 'No') throw new AccessDeniedException('Account Disabled');

			 return $model;
	  }
}
?>