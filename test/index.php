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
 * @package com.makeabyte.agilephp.test
 */

/**
 * A simple test application used to both illustrate and test the
 * core framework components.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require '../src/AgilePHP.php';

try {

    AgilePHP::setFrameworkRoot(realpath(dirname(__FILE__) . '/../src' ));
	AgilePHP::init();
    AgilePHP::setDefaultTimezone('America/New_York');
    AgilePHP::setDebugMode(true);
	AgilePHP::setAppName('AgilePHP Framework Tests');

	MVC::dispatch();
}
catch( FrameworkException $e ) {

     require_once '../src/mvc/PHTMLRenderer.php';

     Log::error($e->getMessage());

     $renderer = new PHTMLRenderer();
     $renderer->set('title', 'AgilePHP Framework :: Error Page');
  	 $renderer->set('error', $e->getCode() . '   ' . $e->getMessage() . (AgilePHP::isInDebugMode() ? '<br><pre>' . $e->getTraceAsString() . '</pre>' : ''));
  	 $renderer->render('error');
}

?>