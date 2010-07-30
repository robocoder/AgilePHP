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
 * @package com.makeabyte.agilephp.webservice.rest
 */

/**
 * REST utility/helper class for handling mime negotiation and data transformations.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.webservice.rest
 */
class RestUtil {

	  /**
	   * Responsible for negotiating a mime type used to send data from the server
	   * to the client. This is done by first checking the client preferred mime types
	   * and ensuring one of the following mimes is supported:
	   * 1) application/xml
	   * 2) application/json
	   * 3) application/xhtml+xml
	   *
	   * The first mime type to match according to the clients preferred list is used.
	   * Finally, the REST service action method is introspected in search of a #@ProduceMime
	   * annotation. If #@ProduceMime::type is accepted by the client this mime type is returned,
	   * otherwise a 406 "Not Acceptable" is returned to the client.
	   *
	   * @param string $class The service to negotiate with
	   * @param string $method The service method to negotiate with
	   * @return string The negotiated mime type
	   * @static
	   */
	  public static function negotiate($class, $method) {

	  		  $supportedMimes = array('application/xml', 'application/json', 'application/x-yaml', 'application/xhtml+xml', '*/*');

			  // Get the mime types the client desires from the HTTP_ACCEPT header.
	  		  // NOTE: This is very primitive/"light-weight" and does not provide full support for RFC 2616 Accept headers,
	  		  //       however, does support basic content negotiation none the less.
	  		  $clientMimes = array();
			  foreach(explode(',', $_SERVER['HTTP_ACCEPT']) as $mimeType) {

					  $item = explode(';', $mimeType);
					  $clientMimes[$item[0]] = floatval(array_key_exists(1, $item) ? substr($item[1], 2) : 1);
			  }
			  arsort($clientMimes);

			  // Find the first preferred client mime type which the REST service supports.
			  foreach($clientMimes as $mimeType => $index) {

					  if(in_array($mimeType, $supportedMimes)) {

					     $mime = ($mimeType == '*/*') ? 'application/xml' : $mimeType;
					 	 break;
					  }
			  }

			  // If the client does not accept one of the supported mime types, throw a 406.
			  if(!isset($mime)) {

			  	 Log::error('RestUtil::negotiate Client does not accept any of the supported mime types.');
			  	 throw new RestServiceException(406);
			  }

			  // Store the consume and produce mime values if present. Assume application/xml if the client does not specify content type
			  $response = array();
			  $response['ConsumeMime'] = (@$_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : 'application/xml';

			  // Parse #@ProduceMime and #@ConsumeMime values from the REST service method if present
	  		  $annotes = Annotation::getMethodsAsArray($class);
	  		  foreach($annotes[$method] as $annotation) {

	  		 		  if($annotation instanceof ProduceMime) {

	  		 		     // If the REST service has a #@ProduceMime::type defined which the client does not accept, send a 406 and exit.
	  		 		     if(!array_key_exists($annotation->type, $clientMimes)) {

	  		 		        Log::error('RestUtil::negotiate Client does not accept mime type defined by #@ProduceMime::type');
	  		 		   	   	throw new RestServiceException(406);
	  		 		   	 }

	  		 		   	 $mime = $annotation->type;
	  		 		   }

	  		 		   if($annotation instanceof ConsumeMime)
	  		 		   	  $response['ConsumeMime'] = $annotation->type;
	  		  }

	  		  $response['ProduceMime'] = $mime;

	  		  return $response;
	  }

	  /**
	   * Transforms data being produced by a REST service into a new mime/data type
	   * for presentation to the client.
	   *
	   * @param mixed $data The data produced by the REST service. THIS DATA MUST BE EITHER AN OBJECT OR AN ARRAY!
	   * @param string $mime The mime type which describes the new data formatting.
	   * 					 (application/xml|application/json|application/x-yaml|application/xhtml+xml)
	   */
	  public static function serverTransform($data, $mime) {

	  		 if(!is_object($data) && !is_array($data)) {

	  		 	Log::debug('RestUtil::serverTransform The specified data must be either an object or array at \'' . $data . '\.');
	  		 	throw new RestServiceException(500);
	  		 }

	  		 switch($mime) {

	  		 	case 'application/xml':
	  		 		 header('content-type: application/xml');
	  		 	 	 return XmlRenderer::render($data);
 		 		break;

	  		 	case 'application/json':
	  		 		 header('content-type: application/json');
	  		 		 return JsonRenderer::render($data);
	  		 	break;

	  		 	case 'application/x-yaml':
	  		 		  header('content-type: application/x-yaml');
	  		 		  return YamlRenderer::render($data);
	  		 	break;

	  		 	case 'application/xhtml+xml':
	  		 	      if(is_array($data)) {
	  		 	         if(!isset($data[0])) {

	  		 	            Log::error('RestUtil::serverTransform Received null data');
	  		 	            throw new RestServiceException(500);
	  		 	         }
	  		 	         $className = get_class($data[0]);
	  		 	      }
	  		 	      else
	  		 	         $className = get_class($data);

	  		 	      $class = new ReflectionClass($className);

	  		 	      $namespace = explode('\\', $class->getName());
                      $className = array_pop($namespace);

	  		 		  // Perform XSLT transformation if a model xsl view exists
	  		 		  $renderer = new XSLTRenderer();
	  		 		  if(file_exists(AgilePHP::getWebRoot() . '/view/' . $className . '.xsl')) {
	  		 		  	  return $renderer->transformXsl($className, XmlRenderer::render($data));
	  		 		  }
	  		 		  else {
	  		 		  	  // Otherwise send the response as XML
	  		 		  	  header('content-type: application/xml');
	  		 		  	  return XmlRenderer::render($data);
	  		 		  }
	  		 	break;

	  		 	default:
	  		 		Log::debug('RestUtil::serverTransform Could not produce unsupported mime type \'' . $mime . '\'.');
	  		 		throw new RestServiceException(500);
	  		 }
	  }

	  /**
	   * Transforms the data consumed from the client request into
	   * an object which represents the specified mime type. This data
	   * is then presented to the requsted REST service resource.
	   *
	   * 1) application/xml       = SimpleXMLElement
	   * 2) application/json      = JSON unserialized string
	   * 3) application/x-yaml    = YAML unserialized string
	   * 4) application/xhtml+xml = Data is returned untouched
	   *
	   * @param string $data The data being consume from a cilent HTTP request (PUT|POST|DELETE)
	   * @param string $mime The mime type which describes the data.
	   * 					 (application/xml|application/json|application/x-yaml|application/xhtml+xml)
	   * @return void
	   */
	  public static function consumeTransform($data, $mime) {

	         try {
        			 switch($mime) {

        			 	case 'application/xml':
        			 		return XmlToModel::transform($data);
        			 	break;

        			 	case 'application/json':
        			 		return JsonToModel::transform($data);
        			 	break;

        			 	case 'application/x-yaml':
        			 		 return YamlToModel::transform($data);
        			 	break;

        			 	case 'application/xhtml+xml':
        			 		 return $data;
        		 		break;

        			 	default:
        			 		Log::debug('RestUtil::consumeTransform Could not transform consumed data type \'' . $mime . '\'. Using raw data as last resort.');
        			 		return $data;
        			 }
	         }
	         catch(FrameworkException $e) {

	               throw new RestServiceException(400, $e->getMessage());
	         }
	  }
}
?>