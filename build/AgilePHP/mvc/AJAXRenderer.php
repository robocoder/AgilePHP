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
	  public function render($data, $name = null) {

	  		 if(!$this->output)
	  		 	throw new FrameworkException('AJAXRenderer::render Output mode required. Use AJAXRenderer::setOutputMode to set the desired format (json|xml|yaml)');

	  		 if( $this->output == 'json' ) {

	  		 	 $json = $this->toJSON($data, $name);

	  		 	 Log::debug('AJAXRenderer::render Rendering JSON ' . $json);

	  		 	 header('content-type: application/json');
	  		 	 die($json);
	  		 }

	  		 else if($this->output == 'xml') {

	  		 	 if(!$name) $name = 'Result';

	  		 	 $xml = $this->toXML($data, $name);

	  		 	 Log::debug('AJAXRenderer::render Rendering XML ' . $xml);

	  		 	 header('content-type: application/xml');
	  		 	 die($xml);
	  		 }

	  		 else if($this->output == 'yaml') {

	  		     $yaml = $this->toYAML($data);

	  		     Log::debug('AJAXRenderer::render Rendering YAML ' . $yaml);

	  		     header('content-type: application/x-yaml');
	  		     die($yaml);
	  		 }
	  }

	  /**
	   * Renders the specified data according to $output without sending
	   * an HTTP content-type header. After rendering JSON, exit() is called.
	   * 
	   * @param Object $data A stdClass object to output as either XML or JSON.
	   * @return void
	   */
	  public function renderNoHeader($data) {

	 		 if(!$this->output)
	  		 	 throw new FrameworkException('AJAXRenderer::render Output mode required. Use AJAXRenderer::setOutputMode to set the desired format (json|xml|yaml)');

	  		 if($this->output == 'json') {

	  		 	$json = $this->toJSON($data);

	  		 	Log::debug('AJAXRenderer::render Rendering JSON ' . $json);

	  		 	die($json);
	  		 }

	  		 else if($this->output == 'xml') {

	  		 	$xml = $this->toXML($data);

	  		 	Log::debug('AJAXRenderer::render Rendering XML ' . $xml);

	  		 	die($xml);
	  		 }
	  		 
	  		 else if($this->output == 'yaml') {

	  		    $yaml = $this->toYAML($data);

	  		    Log::debug('AJAXRenderer::render Rendering YAML ' . $yaml);

	  		    die($yaml);
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
	  public function renderNoFormat($data) {

	  	     if(!$this->output)
	  		 	throw new FrameworkException('AJAXRenderer::render Output mode required. Use AJAXRenderer::setOutputMode to set the desired format (json|xml|yaml)');

	  		 switch($this->output) {

			  		 case 'json':
			  		 case 'JSON':
			  		 	header('content-type: application/json');
	  		 	 		break;

			  		 case 'xml':
			  		 case 'XML':
			  		 	header('content-type: application/xml');
	  		 	 		break;

			  		 case 'yaml':
			  		 case 'YAML':
			  		     header('content-type: application/x-yaml');
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
	  public function renderNoFormatNoHeader($data) {

	  		 die($data);
	  }

	  /**
	   * Sets the desired output type.
	   * 
	   * @param String $type The data formatting to use during output. (XML|JSON|YAML)
	   * @return void
	   * @throws FrameworkException if invalid formatting type is specified
	   */
	  public function setOutput($type) {

	  		 switch( $type ) {

	  		 		 case 'json':
	  		 		 case 'JSON':
	  		 		 	$this->output = 'json';
	  		 		 	break;

	  		 		 case 'xml':
	  		 		 case 'XML':
	  		 		 	$this->output = 'xml';
	  		 		 	break;
	  		 		 	
	  		 		 case 'yaml':
	  		 		 case 'YAML':
	  		 		     $this->output = 'yaml';
	  		 		     break;

	  		 		 throw new FrameworkException('Unsupported output type \'' . $type . '\'.');
	  		 }
	  }

	  /**
	   * Transforms the specified PHP data to JSON. json_encode does not encode
	   * private fields within objects, so here we make use PHP 5.3+
	   * ReflectionProperty::setAccessible to access the private/protected properties.
	   * 
	   * @param mixed $data An array or object to transform into JSON
	   * @param string $name An optional class name. Defaults to null
	   * @param boolean $isChild Used internally for recursion logic
	   * @return The JSON encoded data
	   * @deprecated Use JsonRenderer::render instead
	   */
	  public function toJSON($data, $name = null, $isChild = false) {

	  		 return JsonRenderer::render($data, $name, $isChild);
	  }

	  /**
	   * Recursively transforms the specified PHP data to XML.
	   * 
	   * @param mixed $data An array or object to transform into XML
	   * @param $name Used internally within the method to perform recursion logic
	   * @return The XML string
	   * @deprecated Use XmlRenderer::render instead
	   */
	  public function toXML($data, $name = 'Result', $pluralName = 'Results', $isChild = false, $declaration = true) {

	  		 return XmlRenderer::render($data, $name, $pluralName, $isChild, $declaration);
	  }

	  /**
	   * Transforms the specified PHP data to YAML.
	   * 
	   * @param mixed $data Data to transform into YAML
	   * @param $encoding YAML_ANY_ENCODING, YAML_UTF8_ENCODING, YAML_UTF16LE_ENCODING, YAML_UTF16BE_ENCODING. Defaults to YAML_ANY_ENCODING.
	   * @param $int $linebreak YAML_ANY_BREAK, YAML_CR_BREAK, YAML_LN_BREAK, YAML_CRLN_BREAK. Defaults to YAML_ANY_BREAK
	   * @return $int string The YAML formatted data.
	   * @deprecated Use YamlRenderer::render instead
	   */
	  public function toYAML($data, $encoding = null, $linebreak = null) {

	  		 return YamlRenderer::render($data, $encoding, $linebreak);
	  }
}
?>