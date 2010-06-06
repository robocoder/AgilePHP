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
 * Demonstrates the ability to make a simple, secure rest style web service using
 * the MVC and REST framework components. Note this class can be used as a standard
 * PHP class or a DAO for example, in conjunction with being a REST service, due to
 * the Aspect Oriented Programming style using interceptors and annotations.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.control
 */
#@BasicAuthentication
#@RestService
class users extends BaseController {

	  #@GET
	  public function index() {

	  		 return PersistenceManager::getInstance()->find( new User() );
	  }

	  #@GET
	  #@Path( resource = '/{username}' )
	  #@ProduceMime( type = 'application/xml' )
	  public function getUser( $username ) {

	  		 $user = new User();
	  		 $user->setUsername( $username );

	  		 return $user;
	  }

	  #@GET
	  #@Path( resource = '/{username}/role' )
	  public function getRole( $username ) {

	  		 $user = new User();
	  		 $user->setUsername( $username );

	  		 return $user->getRole();
	  }

	  #@GET
	  #@Path( resource = '/{username}/session' )
	  public function getSession( $username ) {

	  		 $user = new User();
	  		 $user->setUsername( $username );

	  		 return $user->getSession();
	  }

	  #@POST
	  #@Path( resource = '/{username}' )
	  #@ConsumeMime( type = 'application/xml' )
	  #@ProduceMime( type = 'application/xml' )
	  public function createUser( $username, $data ) {

	  		 $user = new User();
	  		 $user->setUsername( $username );
	  		 $user->setPassword( (string)$data->password );
	  		 $user->setEmail( (string)$data->email );
	  		 $user->setCreated( (string)$data->created );
	  		 $user->setLastLogin( (string)$data->lastLogin );
	  		 $user->setEnabled( (string)$data->enabled );

	  		 $role = new Role();
	  		 $role->setName( (string)$data->Role->name );

	  		 $user->setRole( $role );

	  		 $pm = PersistenceManager::getInstance();
	  		 $pm->persist( $user );

	  		 return $user;
	  }

	  #@PUT
	  #@Path( resource = '/{username}' )
	  #@ConsumeMime( type = 'application/xml' )
	  #@ProduceMime( type = 'application/xml' )
	  public function updateUser( $username, $data ) {

	  		 $user = new User();
	  		 $user->setUsername( (string)$data->username );
	  		 $user->setPassword( (string)$data->password );
	  		 $user->setEmail( (string)$data->email );
	  		 $user->setCreated( (string)$data->created );
	  		 $user->setLastLogin( (string)$data->lastLogin );
	  		 $user->setEnabled( (string)$data->enabled );

	  		 $role = new Role();
	  		 $role->setName( (string)$data->Role->name );

	  		 $user->setRole( $role );

	  		 if( $id = (string)$data->Session->id ) {

	  		 	$session = new Session();
	  		 	$session->setId( (string)$data->Session->id );

	  		 	$user->setSession( $session );	
	  		 }

	  		 $pm = PersistenceManager::getInstance();
	  		 $pm->merge( $user );

	  		 return $user;
	  }

	  #@DELETE
	  #@Path( resource = '/{username}' )
	  public function deleteUser( $username ) {

	  		 $user = new User();
	  		 $user->setUsername( $username );

	  		 PersistenceManager::getInstance()->delete( $user );
	  }
}
?>