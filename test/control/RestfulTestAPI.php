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
 * Demonstrates the ability to make a simple rest style web service using
 * the AgilePHP MVC and AJAXRenderer components.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.control
 * @version 0.1a
 */
class RestfulTestAPI extends BaseController {

	  public function __construct() {

	  		 parent::__construct();
	  		 $this->createRenderer( 'AJAXRenderer' );
	  		 $this->getRenderer()->setOutput( 'xml' );
	  }

	  public function index() {

	  		 $user = new User();
	  		 $user->setUsername( 'admin' );

	  		 // return $user; #PHP 5.3+ supports ReflectionProperty::setAccessible and can use AJAXRenderer to render private properties.

	  		 // Before PHP 5.3, ReflectionProperty::setAccessible doesnt exist
	  		 // and therefore AJAXRenderer can not render the above object
	  		 // to XML since its properties are all private. This is a tedious
	  		 // workaround for older versions of PHP.
	  		 $o = new stdClass();
	  		 $o->username = $user->getUsername();
	  		 $o->password = $user->getPassword();
	  		 $o->email = $user->getEmail();
	  		 $o->created = $user->getCreated();
	  		 $o->lastLogin = $user->getLastLogin();

	  		 $this->getRenderer()->render( $o );
	  }

	  public function getRole( $name ) {

	  		 $role = new Role();
	  		 $role->setName( $name );

	  		 $o = new stdClass;
	  		 $o->name = $role->getName();
	  		 $o->description = $role->getDescription();

	  		 $this->getRenderer()->render( $o );
	  }

	  public function getRoles() {

	  		 $pm = new PersistenceManager();
	  		 $roles = $pm->find( new Role() );
	  		 $retval = array();

	  		 foreach( $roles as $role ) {

		  		 $o = new stdClass;
		  		 $o->name = $role->getName();
		  		 $o->description = $role->getDescription();

	  		 	 /*
	  		 	  * This could also be a multi-dimensional array and it would the same way
	  		 	 $o = array();
	  		 	 $o['name'] = $role->getName();
	  		 	 $o['description'] = $role->getDescription();
	  		 	 */

		  		 array_push( $retval, $o );
	  		 }

	  		 $this->getRenderer()->render( $retval, 'Role' );
	  }
}
?>