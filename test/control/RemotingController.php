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
 * Responsible for processing all AgilePHP remoting calls.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.control
 * @version 0.1a
 */
class RemotingController extends Remoting {

	  public function __construct() {
	  		 parent::__construct();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/mvc/BaseController#index()
	   * @throws AgilePHP_RemotingException
	   */
	  public function index() {
	  		 throw new AgilePHP_RemotingException( 'Malformed Request' );
	  }

	  /**
	   * Loads the specified class
	   *  
	   * @param $class The class to remote
	   * @return void
	   * @throws AgilePHP_RemotingException
	   */
	  public function load( $class ) {

	  		 if( !isset( $class ) || count( $class ) < 1 )
				 throw new AgilePHP_RemotingException( 'Class required' );

			 parent::__construct( $class );
			 parent::createStub();
	  }
}
?>