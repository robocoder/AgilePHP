<?php
/**
 * AgilePHP Framework :: The Rapid "for developers" PHP5 framework
 * Copyright (C) 2009 Make A Byte, inc
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
 * @package com.makeabyte.agilephp.interception.interceptors
 */

/**
 * AgilePHP :: Restrict
 * Interceptor for @Restrict annotations
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.interception.interceptors
 * @version 0.1a
 */

#@Interceptor
class Restrict {

	  public $role;
	  public $roles;
	  public $message;

	  #@AroundInvoke
	  public function requireRole( InvocationContext $ic ) {

	  		 $message = $ic->getInterceptor()->message ? $ic->getInterceptor()->message : 'Access Denied';

	  		 $requiredRole = $ic->getInterceptor()->role;
	  	     if( $requiredRole && !Identity::getInstance()->hasRole( $requiredRole ) )
	  	     	 $this->audit( $message, $ic );

	  	     $roles = $ic->getInterceptor()->roles;
	  	     if( is_array( $roles ) ) {

	  	     	 foreach( $roles as $role )
	  	     	 	if( Identity::getInstance()->hasRole( $role ) )
	  	     	 		return $ic->proceed();
	  	     }

	  	     $this->audit( $message, $ic );
	  }

	  /**
	   * Performs an audit entry in the log file
	   * @param $message Custom error message as defined in the annotation defintion
	   * @param $ic The InvocationContext which contains the call state
	   * @return void
	   */
	  private function audit( $message, $ic ) {

	  		  Logger::getInstance()->error( '#@Restrict::audit Access Denied ' . print_r( $ic, true ) );
	  		  throw new AgilePHP_AccessDeniedException( $message );
	  }
}
?>