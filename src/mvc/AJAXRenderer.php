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
 * Renders data in JSON or XML, optionally with appropriate content-type header.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp.mvc
 */
class AJAXRenderer extends BaseRenderer {

	  private $output = 'json';

	  /**
	   * Renders the specified PHP data according to $output. The approriate
	   * content-type to the HTTP response header. After rendering JSON,
	   * exit() is called.
	   * 
	   * (non-PHPdoc)
	   * @see src/mvc/BaseRenderer#render($view)
	   */
	  public function render( $data, $name = null ) {

	  		 if( !$this->output )
	  		 	 throw new AgilePHP_Exception( 'AJAXRenderer::render Output mode required. Use AJAXRenderer::setOutputMode to set the desired format (json|xml)' );

	  		 if( $this->output == 'json' ) {

	  		 	 $json = $this->toJSON( $data, $name );

	  		 	 Log::debug( 'AJAXRenderer::render Rendering JSON ' . $json );

	  		 	 header( 'content-type: application/json' );
	  		 	 print $json;
	  		 	 exit;
	  		 }

	  		 else if( $this->output == 'xml' ) {

	  		 	 if( !$name ) $name = 'Result';

	  		 	 $xml = $this->toXML( $data, $name );

	  		 	 Log::debug( 'AJAXRenderer::render Rendering XML ' . $xml );

	  		 	 header( 'content-type: application/xml' );
	  		 	 print $xml;
	  		 	 exit;
	  		 }
	  }

	  /**
	   * Renders the specified data according to $output without sending
	   * an HTTP content-type header. After rendering JSON, exit() is called.
	   * 
	   * @param Object $data A stdClass object to output as either XML or JSON.
	   * @return void
	   */
	  public function renderNoHeader( $data ) {

	 		 if( !$this->output )
	  		 	 throw new AgilePHP_Exception( 'AJAXRenderer::render Output mode required. Use AJAXRenderer::setOutputMode to set the desired format (json|xml)' );

	  		 if( $this->output == 'json' ) {

	  		 	 $json = $this->toJSON( $data );

	  		 	 Log::debug( 'AJAXRenderer::render Rendering JSON ' . $json );

	  		 	 print $json;
	  		 	 exit;
	  		 }

	  		 else if( $this->output == 'xml' ) {

	  		 	 $xml = $this->toXML( $data );

	  		 	 Log::debug( 'AJAXRenderer::render Rendering XML ' . $xml );

	  		 	 print $xml;
	  		 	 exit;
	  		 }
	  }

	  /**
	   * Renders the specified raw data without sending it through any of the AJAXRender
	   * internal formatting/conversion methods. An appropriate HTTP content-type
	   * header is added to the response.
	   *  
	   * @param mixed $data The raw data to render
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
			  		 	header( 'content-type: application/xml' );
	  		 	 		break;
	  		 }

	  		 print $data;
	  }

	  /**
	   * Renders the specified data without sending it through any of the AJAXRenderer
	   * internal formatting/conversion methods. In addition, no HTTP content-type header
	   * is added to the response.
	   * 
	   * @param Object $data The data to render
	   * @return void
	   */
	  public function renderNoFormatNoHeader( $data ) {

	  		 print $data;
	  		 exit;
	  }

	  /**
	   * Sets the desired output type.
	   * 
	   * @param String $type The data formatting to use during output. (XML|JSON)
	   * @return void
	   * @throws AgilePHP_Exception if invalid formatting type is specified
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
	   * @param mixed $data An array or object to transform into JSON
	   * @param String $name Used internally within the method to perform recursion logic.
	   * @return The JSON encoded data
	   */
	  public function toJSON( $data, $name = null, $isChild = false ) {

	  		  $json = '';

	  		  // Format arrays
	  		  if( is_array( $data ) ) {

	  		  	  $i=0;
	  		  	  if( $name && $name != 'stdClass' ) $json .= $name . ' : ';
	  		  	  $json .= '[ ';
	  		  	  foreach( $data as $key => $value ) {

	  		  	  		$i++;
	  		  	  	 	$json .= (is_object( $value ) || is_array( $value )) ?
	  		  	  	 				 $this->toJSON( $value, $name ) :
	  		  	  	 				 ((is_numeric( $key )) ? json_encode( $value ) : json_encode( $value ) );
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

		  		  $json .= ($isChild) ? '"' . $class->getName() . '" : { ' : ' { "' . $class->getName() . '" : { ';

	  		  	  // @todo Interceptors are still being somewhat intrusive to reflection operations
	  		      if( method_exists( $data, 'getInterceptedInstance' ) ) {

	  		     	  $name = preg_replace( '/_Intercepted/', '', $class->getName() );
	  		     	  $data = $data->getInterceptedInstance();
	  		     	  $class = new ReflectionClass( $data );
	  		      }

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
			  		   	   $json .= $this->toJSON( $value, $property->getName(), true ) . ' ';

			  		   else
			  		   		$json .= '"' . $property->getName() . '" : ' . json_encode( $value );

			  		   $json .= ( ($i+1) < count( $properties ) ) ? ', ' : '';
			  	  }
			  	  $json .= ($isChild) ? '} ' : ' } }';
		  	  }

		  	  else {

		  	  	  $json = json_encode( $data );
		  	  }

	  		  return $json;
	  }

	  /**
	   * Recursively transforms the specified PHP data to XML.
	   * 
	   * @param mixed $data An array or object to transform into XML
	   * @param $name Used internally within the method to perform recursion logic
	   * @return The XML string
	   */
	  public function toXML( $data, $name = 'Result', $isChild = false ) {

	  		  $xml = ($isChild) ? '' : "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";

	  	      if( is_array( $data ) ) {

  	 		  	  $xml .= '<' . $name . ((!$isChild) ? 's' : '') . '>';
  	 		  	  foreach( $data as $key => $val ) {

  	 		  	  		if( is_object( $val ) || is_array( $val ) )
  	 		  	  			$xml .= $this->toXML( $val, $name, true );

  	 		  	  		else {

  	 		  	  			$val = mb_convert_encoding( html_entity_decode( $val ), 'UTF-8', 'ISO-8859-1' );
  	 		  	  			$xml .= '<' . $key . '>' . $val . '</' . $key . '>';
  	 		  	  		}
  	 		  	  }
  	 		  	  $xml .= '</' . $name . ((!$isChild) ? 's' : '') . '>';

  	 		  	  return $xml;
	  	      }

	  	      else if( is_object( $data ) ) {

	  	      	  $class = new ReflectionClass( $data );

	  	      	  // stdClass has public properties
		  		  if( $class->getName() == 'stdClass' ) {

		  		  	  if( $name == null ) $name = 'Results';

		  		  	  $xml .= '<' . $name . '>';
		  		  	  foreach( get_object_vars( $data ) as $property => $value ) {
	
		  		 		  if( is_object( $value ) || is_array( $value ) )
		  		 		  	  $xml .= $this->toXML( $value, $property, true );

		  		 		  else {
	
			  		 		  $value = mb_convert_encoding( html_entity_decode( $value ), 'UTF-8', 'ISO-8859-1' );
			  		 		  $xml .= '<' . $property . '>' . $value . '</' . $property . '>';
		  		 		  }
		  		 	  }
		  		 	  $xml .= '</' . $name . '>';

		  		 	  return $xml;
	  		     }

		  	     // @todo Interceptors are still being somewhat intrusive to reflection operations
	  		     if( method_exists( $data, 'getInterceptedInstance' ) ) {

	  		     	 $name = preg_replace( '/_Intercepted/', '', $class->getName() );
	  		     	 $instance = $data->getInterceptedInstance();
	  		     	 $class = new ReflectionClass( $instance );
	  		     	 $data = $instance;
	  		     }

		  		 $xml = '<' . $name . '>';
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
		  		 $xml .= '</' . $name . '>';
	  		 }
	  		 return $xml;
	  }

	  /**
	   * Transforms the specified PHP data to YAML.
	   * 
	   * @param mixed $data Data to transform into YAML
	   * @param $encoding YAML_ANY_ENCODING, YAML_UTF8_ENCODING, YAML_UTF16LE_ENCODING, YAML_UTF16BE_ENCODING. Defaults to YAML_ANY_ENCODING.
	   * @param $int $linebreak YAML_ANY_BREAK, YAML_CR_BREAK, YAML_LN_BREAK, YAML_CRLN_BREAK. Defaults to YAML_ANY_BREAK
	   * @return $int string The YAML formatted data.
	   */
	  public function toYAML( $data, $encoding = YAML_ANY_ENCODING, $linebreak = YAML_ANY_BREAK ) {

	  		 return yaml_emit( $data, $encoding, $linebreak );
	  }
}
?>