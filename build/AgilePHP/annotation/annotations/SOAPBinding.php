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
 * @package com.makeabyte.agilephp.annotation.annotations
 */

/**
 * Annotation which authorizes a method for use with the
 * AgilePHP WebService component.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.annotation.annotations
 * <code>
 * #@WebService( serviceName = 'MyAPIService', targetNameSpace = 'http://www.mydomain.com/index.php/MyAPI' )
 * #@SOAPBinding( style = SOAPStyle::RPC, use = SOAPStyle::ENCODED )
 * class MyAPI {
 * 
 * 		 // I am an RPC/Encoded SOAP web service and can be easily changed into
 * 		 // RPC/Literal or Document Literal Wrapped by simply changing the #@SOAPBinding
 * 		 // values.
 * }
 * </code>
 */
class SOAPBinding {

	  /**
	   * @var String The SOAP style used in the WSDL. (STYLE_DOCUMENT|STYLE_RPC) Default is STYLE_DOCUMENT.
	   */
	  public $style = SoapStyle::DOCUMENT;

	  /**
	   * @var String Specifies the encoding rules of the SOAP message. (USE_ENCODED|USE_LITERAL). Default is USE_LITERAL.
	   */
	  public $use = SoapStyle::LITERAL;
}

/**
 * SOAP binding constants class
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.annotation.annotations
 * @version 0.1a
 */
class SOAPStyle {

	  const RPC = 'rpc';
	  const DOCUMENT = 'document';

	  const ENCODED = 'encoded';
	  const LITERAL = 'literal';
}
?>