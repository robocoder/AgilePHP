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
 * @package com.makeabyte.agilephp.mvc
 */

/**
 * AgilePHP :: MVC PHTMLRenderer
 * Provides base rendering implementation for PHTML (PHP) views.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.mvc
 * @version 0.1a
 */
class PHTMLRenderer extends BaseRenderer {

      /**
	   * Renders a view by dumping all 'store' variables to locallly scoped (page) variables. The view
	   * is expected to be in <webapp>/view.
	   * 
	   * @param $view The view which is rendered from the web app's 'view' directory
	   * @return void
       */
      public function render( $view ) {

      	 	 $path = AgilePHP::getFramework()->getWebRoot() . '/view/' . $view . '.phtml';

      	 	 if( !file_exists( $path ) )
      	 	      throw new AgilePHP_Exception( 'Error rendering application view. Path does not exist ' . $path );
  
      	 	 foreach( $this->getStore() as $key => $value )
	                  $$key = $value;

	         Logger::getInstance()->debug( 'PHTMLRenderer::render executed with parameter $view = \'' . $view . '\'' );

	         // Prevent local variables from being visible to the view
	         unset( $f );
	         unset( $view );
			 unset( $key );
			 unset( $value );

	         require_once $path;
	  }

	  /**
	   * Renders a component view by dumping all 'store' variables to locallly scoped (page) variables. The view is
	   * expected to be in <webapp>/component/<component_name>/view.
	   * 
	   * @param $componentName
	   * @return unknown_type
	   */
	  public function renderComponent( $componentName, $view ) {

	  		 $path = AgilePHP::getFramework()->getWebRoot() . '/components/' . $componentName . '/view/' . $view . '.phtml';

      	 	 if( !file_exists( $path ) )
      	 	      throw new AgilePHP_Exception( 'Error rendering component view. Path does not exist ' . $path );
  
      	 	 foreach( $this->getStore() as $key => $value )
	                  $$key = $value;

	         Logger::getInstance()->debug( 'PHTMLRenderer::renderComponent Executed with parameter $componentName = \'' . $componentName . '\'' );

	         // Prevent local variables from being visible to the view
	         unset( $f );
	         unset( $view );
			 unset( $key );
			 unset( $value );

	         require_once $path;
	  }
}
?>