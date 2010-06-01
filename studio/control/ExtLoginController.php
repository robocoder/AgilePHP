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
 * @package com.makeabyte.agilephp.studio.control
 */

/**
 * Controller for ExtJS form based login related tasks
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.studio.control
 */
class ExtLoginController extends BaseExtController {

	  public function __construct() {

	  	     parent::__construct();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/mvc/BaseController#index()
	   */
	  public function index() { }

	  /**
	   * Authenticates a user account using AgilePHP Identity and Scope components.
	   * 
	   * @return void
	   */
	  public function login() {

	  		 $request = Scope::getRequestScope();

	  		 if( !$username = $request->getSanitized( 'username' ) ) {

	  		 	$this->getRenderer()->setError( 'Username required' );
	  		 	$this->getRenderer()->render( false );
	  		 }

	  		 if( !$password = $request->getSanitized( 'password' ) ) {

	  		 	 $this->getRenderer()->setError( 'Password required' );
	  		 	 $this->getRenderer()->render( false );
	  		 }

			 if( !Identity::getInstance()->login( $username, $password ) ) {

			 	 Scope::getRequestScope()->invalidate();
			 	 $this->getRenderer()->setError( 'Invalid username/password' );
	  		 	 $this->getRenderer()->render( false );
			 }

			 $this->getRenderer()->set( 'username', Identity::getInstance()->getUsername() );
			 $this->getRenderer()->set( 'role', Identity::getInstance()->getRole()->getName() );
	  	     $this->getRenderer()->render( true );
	  }

	  /**
	   * Destroys the session which was created by login()
	   * 
	   * @return void
	   */
	  public function logout() {

	  	     Identity::getInstance()->logout();
	  }
}
?>