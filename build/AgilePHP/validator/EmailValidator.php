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
 * @package com.makeabyte.agilephp.validator
 */

/**
 * Validates email addresses by checking its syntax and checking the domain
 * for valid A and MX records.
 *  
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.validator
 */
class EmailValidator extends Validator {

	  /**
	   * Validates an email address by checking its syntax and performing
	   * and DNS lookup on the domain.
	   * 
	   * @param String $email The email address to validate
	   * @return bool True if the email address is considered valid, false otherwise.
	   */
	  public function validate() {

   			 $atIndex = strrpos( $this->data, '@' );
   			 if( is_bool( $atIndex ) && !$atIndex )
      			 return false;
   
   			 $domain = substr( $this->data, $atIndex + 1 );
      		 $local = substr( $this->data, 0, $atIndex );
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
}
?>