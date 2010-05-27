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
 * @package com.makeabyte.agilephp
 */

/**
 * Contains basic miscellaneous data validation algorithms.
 *  
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * @version 0.4a
 */
class Validator {

	  private function __construct() { }
	  private function __clone() { }

	  /**
	   * Validates an email address by checking its syntax and performing
	   * and DNS lookup on the domain.
	   * 
	   * @param String $email The email address to validate
	   * @return bool True if the email address is considered valid, false otherwise.
	   * @static
	   */
	  public static function validateEmail( $email ) {

   			 $atIndex = strrpos( $email, '@' );
   			 if( is_bool( $atIndex ) && !$atIndex )
      			 return false;
   
   			 $domain = substr( $email, $atIndex + 1 );
      		 $local = substr( $email, 0, $atIndex );
      		 $localLen = strlen( $local );
      		 $domainLen = strlen( $domain );
      		 if( $localLen < 1 || $localLen > 64 ) {

      		 	 // local part length exceeded
         	     return false;
      		 }
      		 else if( $domainLen < 1 || $domainLen > 255 ) {

      		 	  // domain part length exceeded
         		  return false;
      		 }
      		 else if ($local[0] == '.' || $local[$localLen-1] == '.') {

      		 	  // local part starts or ends with '.'
         	      return false;
      		 }
      		 else if( preg_match( '/\\.\\./', $local ) ) {

      		 	  // local part has two consecutive dots
         	      return false;
      		 }
      		 else if( !preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain ) ) {

      		 	  // character not valid in domain portion
         		  return false;
      		 }
      		 else if( preg_match('/\\.\\./', $domain ) ) {

      		 	  // domain part has two consecutive dots
         		  return false;
      		 }
      		 else if( !preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 			str_replace( "\\\\", "", $local ) ) ) {

                  // character not valid in local part unless 
		          // local part is quoted
         		  if( !preg_match( '/^"(\\\\"|[^"])+"$/', str_replace( "\\\\", "", $local ) ) ) {

         		  	  return false;
         		  }
      		}

      		if( !( checkdnsrr( $domain, "MX" ) || checkdnsrr( $domain, "A" ) ) ) {

      			// domain not found in DNS
		        return false;
      		}

   			return true;
	  }

	  /**
	   * Validates a number by ensuring it is either an int or float.
	   * 
	   * @param mixed $data The data to validate
	   * @return bool True if validation is successful, false otherwise
	   * @static
	   */
	  public static function validateNumber( $data ) {

	  		 return is_int( $data ) || is_float( $data );
	  }

	  /**
	   * Validates the specified data by ensuring it is a string and its
	   * length is less than or equal to that of the specified $length.
	   * 
	   * @param String $data The data to validate
	   * @param Integer $length Optional length parameter. If present the length
	   * 						is compared against the passed data to ensure
	   * 						its length is less than or equal to the specified
	   * 						$length.
	   * @return bool True if validation is successful, false otherwise
	   * @static
	   */
	  public static function validateString( $data, $length = null ) {

	  		 if( gettype( $data ) == 'string' ) {

	  		 	 if( $length && is_int( $length ) )
	  		 	 	 if( !strlen( $data ) <= $length )
	  		 	 	 	 return false;

	  		 	 return true;
	  		 }

	  		 return false;
	  }

	  /**
	   * Validates the specified data by ensuring it is a valid IP address.
	   * 
	   * @param String $data The data to validate
	   * @return bool True if the $data is a valid IP address
	   * @static
	   */
	  public static function validateIP( $data ) {

	  		 if( preg_match( '/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/', $data ) )
	  		 	 return true;

	  		 return false;
	  }
}
?>