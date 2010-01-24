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
 * @package com.makeabyte.agilephp.interception.interceptors
 */

/**
 * AgilePHP interceptor responsible for encrypting passwords using the
 * AgilePHP Crypto component.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.interception.interceptors
 * @version 0.1a
 * @example @Id
 */

#@Interceptor
class Password {

	  /**
	   * @var Integer Optional parameter index value. Use to apply interception
	   * 	  logic to a specific parameter in a method that takes multiple arguments.
	   */
	  public $parameter;

	  #@AroundInvoke
	  public function hash( InvocationContext $ic ) {

	  		 if( !$ic->getParameters() )
	  		 	 throw new AgilePHP_InterceptionException( '#@Password::encrypt Requires a method which accepts at least one parameter.' );

		  	 // Dont encrypt passwords coming from persistence 'find' operation.
	  		 $callee = $ic->getCallee();
	  		 $pieces = explode( DIRECTORY_SEPARATOR, $callee['file'] );
	  		 $className = str_replace( '.php', '', array_pop( $pieces ) );

	  		 if( $className == 'BasePersistence' || $className == 'PersistenceManager' )
	  		 	 return $ic->proceed();

	  		 // Hash the parameter
	  		 $crypto = new Crypto();
	  		 $params = $ic->getParameters();

	  		 if( $parameter ) {

	  		 	 if( !array_key_exists( $parameter, $params ) )
	  		 	 	 throw new AgilePHP_InterceptionException( '#@Password::parameter index out of bounds' );

	  		 	 $params[$parameter] = $crypto->getDigest( $params[$parameter] );

	  		 	 $ic->setParameters( $params );

	  		 	 Logger::getInstance()->debug( '#@Password::hash Password secured using ' . $crypto->getAlgorithm() );

	  		 	 return $ic->proceed();
	  		 }

			 $ic->setParameters( array( $crypto->getDigest( $params[0] ) ) );
			 
			 Logger::getInstance()->debug( '#@Password::hash Password secured using ' . $crypto->getAlgorithm() );

			 return $ic->proceed();
	  }
}
?>