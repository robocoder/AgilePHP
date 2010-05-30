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
 * AgilePHP interceptor responsible for formatting arguments as a SQL
 * DateTime data type.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.interception.interceptors
 */

#@Interceptor
class SQLDateTime {

	  /**
	   * @var Integer Optional parameter index value. Use to apply interception
	   * 	  logic to a specific parameter in a method that takes multiple arguments.
	   */
	  public $parameter;

	  #@AroundInvoke
	  public function setDateTime( InvocationContext $ic ) {

	  		 if( !$ic->getParameters() )
	  		 	 throw new AgilePHP_InterceptionException( '#@SQLDateTime::encrypt Requires a method which accepts at least one parameter.' );

		  	 // Dont process arguments being set by persistence classes
	  		 $callee = $ic->getCallee();
	  		 $pieces = explode( DIRECTORY_SEPARATOR, $callee['file'] );
	  		 $className = str_replace( '.php', '', array_pop( $pieces ) );

	  		 if( $className == 'BasePersistence' || $className == 'PersistenceManager' || preg_match( '/dialect$/i', $className ) )
	  		 	 return $ic->proceed();

	  		 $params = $ic->getParameters();

	  		 if( $parameter ) {

	  		 	 if( !array_key_exists( $parameter, $params ) )
	  		 	 	 throw new AgilePHP_InterceptionException( '#@SQLDateTime::parameter index out of bounds' );

	  		 	 $params[$parameter] = date( 'Y-m-d H:i:s', $params[$parameter] );

	  		 	 $ic->setParameters( $params );

	  		 	 Log::debug( '#@SQLDateTime::setDateTime Set value to ' . $params[$parameter] );

	  		 	 return $ic->proceed();
	  		 }

			 $ic->setParameters( array( date( 'Y-m-d H:i:s', $params[0] ) ) );

			 Log::debug( '#@SQLDateTime::setDateTime Set value to ' . date( 'Y-m-d H:i:s', $params[0] ) );

			 return $ic->proceed();
	  }
}
?>