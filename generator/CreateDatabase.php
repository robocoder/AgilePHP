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
 * @package com.makeabyte.agilephp.generator
 */

/**
 * Creates a physical database using persistence.xml configuration.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.generator
 * @version 0.1a
 */
require_once 'util' . DIRECTORY_SEPARATOR . 'AgilePHPGen.php';
require_once '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'AgilePHP.php';
require_once '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'PersistenceManager.php';

class CreateDatabase extends AgilePHPGen {

	  /**
	   * Creates the database schema for the current web application
	   * based on persistence.xml configuration. 
	   *  
	   * @return void
	   */
	  public function testCreateDatabase() {

	  		 $agilephp = AgilePHP::getFramework();
      	     $agilephp->setWebRoot( $this->getCache()->getProjectRoot() );
      	     $agilephp->setFrameworkRoot( $this->getCache()->getProjectRoot() . DIRECTORY_SEPARATOR . 'AgilePHP' );

	  		 $pm = new PersistenceManager();
	  		 $pm->create();
	  }
}
?>