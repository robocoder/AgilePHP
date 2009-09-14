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
 * AgilePHP :: MVC AJAXRenderer
 * Renders data to JSON and XML with appropriate content-type header.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp.mvc
 * @version 0.2a
 */
class AJAXRenderer extends BaseRenderer {

	  private $output = 'json';

	  /**
	   * Renders the specified PHP data according to $output. The approriate
	   * content-type to the HTTP response header.
	   * 
	   * (non-PHPdoc)
	   * @see src/mvc/BaseRenderer#render($view)
	   */
	  public function render( $data ) {

	  		 if( !$this->output )
	  		 	 throw new AgilePHP_Exception( 'AJAXRenderer::render Output mode required. Use AJAXRenderer::setOutputMode to set the desired format (json|xml)' );

	  		 if( $this->output == 'json' ) {

	  		 	 $json = $this->toJSON( $data );

	  		 	 Logger::getInstance()->debug( 'AJAXRenderer::render Rendering JSON ' . $json );

	  		 	 header( 'content-type: application/json' );
	  		 	 print $json;
	  		 }

	  		 else if( $this->output == 'xml' ) {

	  		 	 $xml = $this->toXML( $data );

	  		 	 Logger::getInstance()->debug( 'AJAXRenderer::render Rendering XML ' . $xml );

	  		 	 header( 'content-type: text/xml' );
	  		 	 print $xml;
	  		 }
	  }

	  /**
	   * Renders the specified data according to $output without sending
	   * an HTTP content-type header.
	   * 
	   * @param $data The data to render
	   * @return void
	   */
	  public function renderNoHeader( $data ) {

	 		 if( !$this->output )
	  		 	 throw new AgilePHP_Exception( 'AJAXRenderer::render Output mode required. Use AJAXRenderer::setOutputMode to set the desired format (json|xml)' );

	  		 if( $this->output == 'json' ) {

	  		 	 $json = $this->toJSON( $data );

	  		 	 Logger::getInstance()->debug( 'AJAXRenderer::render Rendering JSON ' . $json );

	  		 	 print $json;
	  		 }

	  		 else if( $this->output == 'xml' ) {

	  		 	 $xml = $this->toXML( $data );

	  		 	 Logger::getInstance()->debug( 'AJAXRenderer::render Rendering XML ' . $xml );

	  		 	 print $xml;
	  		 }
	  }

	  /**
	   * Renders the specified data without sending it through any of the AJAXRender
	   * internal formatting/converting methods. An appropriate HTTP content-type
	   * header is added to the response.
	   *  
	   * @param $data The raw data to render
	   * @return void
	   */
	  public function renderNoFormat( $data ) {

	  	     if( !$this->output )
	  		 	 throw new AgilePHP_Exception( 'AJAXRenderer::render Output mode required. Use AJAXRenderer::setOutputMode to set the desired format (json|xml)' );

	  		 switch( $this->output ) {

			  		 case 'json':
			  		 case 'JSON':
			  		 	header( 'content-type: application/json' );
	  		 	 		break;

			  		 case 'xml':
			  		 case 'XML':
			  		 	header( 'content-type: text/xml' );
	  		 	 		break;
	  		 }

	  		 print $data;
	  }

	  /**
	   * Renders the specified data without sending it through any of the AJAXRenderer
	   * internal formatting/converting methods. In addition, no HTTP content-type header
	   * is added to the response.
	   * 
	   * @param $data The data to render
	   * @return void
	   */
	  public function renderNoFormatNoHeader( $data ) {

	  		 print $data;
	  }

	  /**
	   * Sets the desired output type.
	   * 
	   * @param $type The data type output to the requestor. (XML|JSON)
	   * @return void
	   */
	  public function setOutput( $type ) {

	  		 switch( $type ) {

	  		 		 case 'json':
	  		 		 case 'JSON':
	  		 		 	$this->output = 'json';
	  		 		 	break;

	  		 		 case 'xml':
	  		 		 case 'XML':
	  		 		 	$this->output = 'xml';
	  		 		 	break;

	  		 		 throw new AgilePHP_Exception( 'Unsupported output type \'' . $type . '\'.' );
	  		 }
	  }

	  /**
	   * Transforms the specified PHP data to JSON. json_encode does not encode
	   * private fields within objects, so here we make use PHP 5.3+
	   * ReflectionProperty::setAccessible to access the private/protected properties.
	   * 
	   * @param $data The data to transform to JSON
	   * @param $name Used internally within the method to perform
	   * 			  recursion logic.
	   * @return The JSON encoded data
	   */
	  private function toJSON( $data, $name = null ) {

	  		  $json = '';

	  		  // Format arrays
	  		  if( is_array( $data ) ) {

	  		  	  $i=0;
	  		  	  if( $name && $name != 'stdClass' ) $json .= $name . ' : ';
	  		  	  $json .= '[ ';
	  		  	  foreach( $data as $key => $value ) {

	  		  	  		$i++;
	  		  	  	 	$json .= (is_object( $value ) || is_array( $value )) ?
	  		  	  	 				 $this->toJSON( $value, $name ) : "$key : " . json_encode( $value );
	  		  	  	 	$json .= ( $i < count( $data ) ) ? ', ' : '';
	  		  	  }
	  		  	  $json .= ' ]';
	  		  	  if( $name && $name != 'stdClass' ) $json .= ' }';
	  		  }

	  		  // Format objects (that have private fields)
	  		  else if( is_object( $data ) ) {

		  		  $class = new ReflectionClass( $data );

		  		  // stdClass has public properties
		  		  if( $class->getName() == 'stdClass' )
		  		  	  return json_encode( $data );

	  		  	  $json .= '{ ';

		  		  $properties = $class->getProperties();
			  	  for( $i=0; $i<count( $properties ); $i++ ) {
	
			  		   $property = $properties[$i];
	
			  		   $context = null;
			  		   if( $property->isPublic() )
			  		   	   $context = 'public';
	
			  		   else if( $property->isProtected() )
		  		 		   	    $context = 'protected';
	
		  		 	   else if( $property->isPrivate() )
			  		 		  	$context = 'private';
	
			  		   $value = null;
			  		   if( $context != 'public' ) {
	
	  		 		  	   $property->setAccessible( true );
			  		 	   $value = $property->getValue( $data );
			  		 	   $property->setAccessible( false );
			  		   }
			  		   else {
		
			  		   	   $value = $property->getValue( $data );
			  		   }
	
			  		   if( is_object( $value ) || is_array( $value ) )
			  		   	   $json .= $this->toJSON( $value, $property->getName() ) . ' ';

			  		   else
			  		   		$json .= $property->getName() . ' : ' . json_encode( $value );

			  		   $json .= ( ($i+1) < count( $properties ) ) ? ', ' : '';
			  	  }
			  	  $json .= ' }';
		  	  }

		  	  else {

		  	  	  $json = json_encode( $data );
		  	  }

	  		  return $json;
	  }

	  /**
	   * Recursively transforms the specified PHP data to XML.
	   * 
	   * @param $data The data to transform to XML
	   * @param $name Used internally within the method to perform
	   * 			  recursion logic
	   * @return The XML string
	   */
	  private function toXML( $data, $name = null ) {

	  	      if( is_array( $data ) ) {

  	 		  	  if( $name ) $xml .= '<' . $name . '>';
  	 		  	  foreach( $data as $key => $val ) {
  	 		  	  	
  	 		  	  		if( is_object( $val ) || is_array( $val ) )
  	 		  	  			$xml .= '<' . $key . '>' . $this->toXML( $val ) . '</' . $key . '>';

  	 		  	  		else {

  	 		  	  			$val = mb_convert_encoding( html_entity_decode( $val ), 'UTF-8', 'ISO-8859-1' );
  	 		  	  			$xml .= '<' . $key . '>' . $val . '</' . $key . '>';
  	 		  	  		}
  	 		  	  }
  	 		  	  if( $name ) $xml .= '</' . $name . '>';
	  	      }

	  	      else if( is_object( $data ) ) {
	  	     	
		  		 $class = new ReflectionClass( $data );
		  		 
		  		 $xml = '<' . $class->getName() . '>';
		  		 foreach( $class->getProperties() as $property ) {
	
		  		 		  $context = null;
		  		 		  if( $property->isPublic() )
		  		 		  	  $context = 'public';
		  		 		  else if( $property->isProtected() )
		  		 		  	  $context = 'protected';
		  		 		  else if( $property->isPrivate() )
		  		 		  	  $context = 'private';
	
		  		 		  $value = null;
		  		 		  if( $context != 'public' ) {
	
		  		 		  	  $property->setAccessible( true );
		  		 		  	  $value = $property->getValue( $data );
		  		 		  	  $property->setAccessible( false );
		  		 		  }
		  		 		  else {
	
		  		 		  	  $value = $property->getValue( $data );
		  		 		  }
	
		  		 		  if( is_object( $value ) || is_array( $value ) )
		  		 		  	  $xml .= $this->toXML( $value, $property->getName() );
		  	
		  		 		  else {
	
			  		 		  $value = mb_convert_encoding( html_entity_decode( $value ), 'UTF-8', 'ISO-8859-1' );
			  		 		  $xml .= '<' . $property->getName() . '>' . $value . '</' . $property->getName() . '>';
		  		 		  }
		  		 } 
	  		 }
	  		 return $xml;
	  }
}
?>