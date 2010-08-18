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
 * @package com.makeabyte.agilephp.mvc
 */

/**
 * Provides base rendering implementation for PHTML (PHP) views.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.mvc
 */
class PHTMLRenderer extends BaseRenderer {

      /**
	   * Renders a view by dumping all 'store' variables to locally scoped (page) variables. The view
	   * is expected to be in <webapp>/view.
	   *
	   * @param String $view The view which is rendered from the web app's 'view' directory
	   * @return void
       */
      public function render($view) {

      	 	 $path = AgilePHP::getWebRoot() . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . $view . '.phtml';

      	 	 if(!file_exists($path))
      	 	    throw new FrameworkException('Error rendering application view. Path does not exist ' . $path);

      	 	 foreach($this->getStore() as $key => $value)
	                 $$key = $value;

	         //Log::debug('PHTMLRenderer::render executed with parameter $view = \'' . $view . '\'');

	         // Prevent local variables from being visible to the view
	         unset($f, $view, $key, $value);

	         require $path;
	  }
}
?>