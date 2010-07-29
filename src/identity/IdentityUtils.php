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
 * @package com.makeabyte.agilephp.identity
 */

/**
 * Utility provider for Identity package
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.identity
 */
class IdentityUtils {

	  /**
	   * Generates a variable length character token used to sign requests.
	   * 
	   * @return String Variable length token that must be present in the reset password
	   * 	     		url in order to successfully complete the process.
	   */
	  public static function createToken() {

			 $numbers = '1234567890';
			 $lcase = 'abcdefghijklmnopqrstuvwzyz';
			 $ucase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

			 $length = rand(1, 20);

			 $token = null;
			 for($i=0; $i<$length; $i++) {

			  	 if(rand(0, 1)) {

			  	    $cRand = rand(0, 25);
			  	    $token .= (rand(0, 1)) ? $lcase[$cRand] : $ucase[$cRand];
			  	 }
			  	 else {

			  	    $nRand = rand(0, 9);
			  	   	$token .= $numbers[$nRand];
			  	 }
			 }

	  		 return $token;
	  }
}
?>