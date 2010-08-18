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
 * @package com.makeabyte.agilephp.webservice.soap
 */

/**
 * Interceptor responsible for generating a WSDL file for the implementing web service.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.webservice.soap
 * <code>
 * #@WSDL
 * public function wsdl() {
 * 
 * 		  // I will generate a WSDL file with xml content header when invoked
 * }
 * </code>
 */

#@Interceptor
class WSDL {

	  private $class;
	  private $serviceName;
	  private $targetNamespace;
	  private $SOAPBinding;

	  private $types = '';
	  private $messages = '';
	  private $ptOperations = '';
	  private $bOperations = '';

	  private $processedComplexArrays = array();
	  private $processedComplexObjects = array();
	  private $wrappedTypes = array();

	  /**
	   * Generates and outputs a WSDL file according to the #@SOAPBinding in the 
	   * implementing #@WebService.
	   * 
	   * @param InvocationContext $ic The intercepted call context
	   * @return void
	   */
	  #@AroundInvoke
	  public function generateWSDL(InvocationContext $ic) {

	  		 $callee = $ic->getCallee();
	  		 $this->class = $callee['class']; // The PHP standard class to expose via SOAP

	  		 // Supply default web service configuration values
	  		 $this->serviceName = $this->class . 'Service';
	 		 $this->targetNamespace = 'http://' . $_SERVER['HTTP_HOST'] . AgilePHP::getRequestBase() . '/' . MVC::getController();

	  		 $annotations = Annotation::getClassAsArray($this->class);

	  		 // Initalize web service configuration from #@WebService annotation if present
	  		 if(count($annotations)) {

	  		 	 foreach($annotations as $annotation) {

	  		 	 		if($annotation instanceof WebService) {

	  		 	 			if($annotation->serviceName)
	  		 	 				$serviceName = $annotation->serviceName;

	  		 	 			if($annotation->targetNamespace)
	  		 	 				$this->targetNamespace = $annotation->targetNamespace;
	  		 	 		}

	  		 	 		if($annotation instanceof SOAPBinding)
	  		 	 			$this->SOAPBinding = $annotation;
	  		 	 }
	  		 }

	  		 // Set default SOAPBinding if not explicitly defined
	  		 if(!$this->SOAPBinding)
	  		 	 $this->SOAPBinding = new SOAPBinding();

	  		 $clazz = new ReflectionClass($ic->getTarget());
	  		 $methods = Annotation::getMethodsAsArray($this->class);

	  		 foreach($methods as $method => $annotations) {

	  		 		  $isWSDLMethod = false;
	  		 		  $isWebMethod = false;
	  		 		  $parts = '';

	  		 		  $m = $clazz->getMethod($method);

		 	    	  foreach($annotations as $annote) {

		 	    	  		if($annote instanceof WSDL) {

		 	    	  			$isWSDLMethod = true;
		 	    	  			break;
		 	    	  		}

		 	    	  		// Include only methods annotated with #@WebMethod annotation
		 	    	  		$isWebMethod = ($annote instanceof WebMethod || $isWebMethod == $method) ? $method : false;
		 	    	  		if(!$isWebMethod) continue;
		 	    	  }

		 	    	  if($isWSDLMethod) continue;

		 	    	  // Document/Literal Wrapped
	  		 		  if($this->SOAPBinding->style == SOAPStyle::DOCUMENT &&
		 	    	  				$this->SOAPBinding->use == SOAPStyle::LITERAL) {

		 	    	  			$this->createWrappedMethod($m);
			 	    		 	$this->createWrappedMessage($method);
			 	    		 	$this->createPortTypeOperation($method);
				      			$this->createBindingOperation($method);
			 	    		 	continue;
		 	    	  }
		 	    	  
		 	    	  // RPC/ENCODED/LITERAL
		 	    	  foreach($m->getParameters() as $param) {
		 	    	  	
		 	    	  		$dataType = $this->getParameterTypeFromDocBlock($m, $param);
		 	    	  		$ns = $this->getTypeNamespace($param->name, $param->name);
		 	    	  		if(preg_match('/\[\]/', $dataType)) {

		 	    	  			$ns = 'tns';
		 	    	  			$dataType = preg_replace('/\[\]/', 'Array', $dataType);
		 	    	  			$this->arrayToComplexType($dataType, $ns, $param->name);
		 	    	  		}
		 	    	  		$parts .= "\t\t<part name=\"" . $param->name . "\" type=\"$ns:" . $dataType . '"/>' . PHP_EOL;
 	    		 	  }

 	    		 	  $returnType = $this->getReturnTypeFromDocBlock($m);
 	    		 	  $ns = $this->getTypeNamespace($returnType, 'return');

				      $this->createMessage($method, $parts, $returnType, $ns);
				      $this->createPortTypeOperation($method);
				      $this->createBindingOperation($method);
      		  }

    		  $xml = $this->assemble();

	    	  header('content-type: text/xml');
			  header('content-length: ' . strlen($xml));
			  print($xml);
	  }

	  /**
	   * Adds all of the generated pieces together to form the complete WSDL document.
	   * 
	   * @return string The WSDL document
	   */
	  private function assemble() {

	  		  $types = $this->getTypes();

	  		  $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>" . PHP_EOL;
	  		  $xml .= "<definitions name=\"" . $this->class . "\" targetNamespace=\"" . $this->targetNamespace . "\"" . PHP_EOL;
	  		  $xml .= "\txmlns:tns=\"" . $this->targetNamespace . "\" xmlns:soap=\"http://schemas.xmlsoap.org/wsdl/soap/\"" . PHP_EOL;
	  		  $xml .= "\txmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soap-enc=\"http://schemas.xmlsoap.org/soap/encoding/\"" . PHP_EOL;
	  		  $xml .= "\txmlns:wsdl=\"http://schemas.xmlsoap.org/wsdl/\" xmlns=\"http://schemas.xmlsoap.org/wsdl/\">" . PHP_EOL . PHP_EOL;

	  		  if($types) $xml .= $types;

	  		  $xml .= $this->messages;
	  		  $xml .= PHP_EOL . "\t<portType name=\"" . $this->class . "PortType\">" . PHP_EOL;
	  		  $xml .= $this->ptOperations;
	  		  $xml .= "\t</portType>" . PHP_EOL;

	  		  $xml .= PHP_EOL . "\t<binding name=\"" . $this->class . 'Binding" type="tns:' . $this->class . "PortType\">" . PHP_EOL;
	  		  $xml .= "\t\t<soap:binding style=\"" . $this->SOAPBinding->style . "\" transport=\"http://schemas.xmlsoap.org/soap/http\"/>" . PHP_EOL;
	  		  $xml .= $this->bOperations;
	  		  $xml .= "\t</binding>" . PHP_EOL;
	  		  $xml .= PHP_EOL . "\t<service name=\"" . $this->serviceName . "\">" . PHP_EOL;
	  		  $xml .= "\t\t<port name=\"" . $this->class . "Port\" binding=\"tns:" . $this->class . "Binding\">" . PHP_EOL;
	  		  $xml .= "\t\t\t<soap:address location=\"" . $this->targetNamespace . "\"/>" . PHP_EOL;
	  		  $xml .= "\t\t</port>" . PHP_EOL;
	  		  $xml .= "\t</service>" . PHP_EOL;
	  		  $xml .= '</definitions>';

	  		  return $xml;
	  }

	  /**
	   * Creates a single messages part and appends it to the messages class property.
	   * 
	   * @param string $method The name of the message part
	   * @param string $part The request part
	   * @param string $returnType The PHP data type being returned from this message part (extracted from PHP-doc Comment)
	   * @return void
	   */
	  private function createMessage($method, $part, $returnType, $ns) {
	  	
	  		  if(stristr($returnType, '[]'))
	  		  	  $returnType = preg_replace('/\[\]/', 'Array', $returnType);

	  		  $messages = "\t<message name=\"" . $method . "Request\">" . PHP_EOL;
	  		  $messages .= $part;
	  		  $messages .= "\t</message>" . PHP_EOL;

	  		  $messages .= "\t<message name=\"" . $method . "Response\">" . PHP_EOL;
	  		  if($returnType) $messages .= "\t\t<part name=\"" . $method . "Return\" type=\"$ns:$returnType\" />" . PHP_EOL;
	  		  $messages .= "\t</message>" . PHP_EOL;

	  		  $this->messages .= $messages;
	  }

	  /**
	   * Creates a single message and its part for use with Document/Literal (wrapeed) and appends it to the messages property.
	   * 
	   * @param string $method The name of the message part
	   * @param string $part The request part
	   * @param string $returnType The PHP data type being returned from this message part (extracted from PHP-doc Comment)
	   * @return void
	   */
	  private function createWrappedMessage($method) {

	  		  $messages = "\t<message name=\"" . $method . "\">" . PHP_EOL;
	  		  $messages .= "\t\t<part name=\"parameters\" element=\"tns:" . $method . "\"/>" . PHP_EOL;
	  		  $messages .= "\t</message>" . PHP_EOL;

	  		  $messages .= "\t<message name=\"" . $method . "Response\">" . PHP_EOL;
	  		  $messages .= "\t\t<part name=\"parameters\" element=\"tns:" . $method . "Response\"/>" . PHP_EOL;
	  		  $messages .= "\t</message>" . PHP_EOL;

	  		  $this->messages .= $messages;		  
	  }

	  /**
	   * Creates a new wsdl binding operation and appends it to the operations class property.
	   * 
	   * @param string $method The name of the operation to bind
	   * @return void
	   */
	  private function createBindingOperation($method) {

	  		  $operation = "\t\t<wsdl:operation name=\"$method\">" . PHP_EOL;
	  		  $operation .= "\t\t\t<soap:operation/>" . PHP_EOL;
	  		  $operation .= "\t\t\t\t<input>" . PHP_EOL;
	  		  $operation .= "\t\t\t\t\t<soap:body use=\"" . $this->SOAPBinding->use . "\"" . (($this->SOAPBinding->use == SOAPStyle::ENCODED) ? ' encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"' : '') . "/>" . PHP_EOL;
			  $operation .= "\t\t\t\t</input>" . PHP_EOL;
			  $operation .= "\t\t\t\t<output>" . PHP_EOL;
			  $operation .= "\t\t\t\t\t<soap:body use=\"" . $this->SOAPBinding->use . "\"" . (($this->SOAPBinding->use == SOAPStyle::ENCODED) ? ' encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"' : '') . "/>" . PHP_EOL;
      		  $operation .= "\t\t\t\t</output>" . PHP_EOL;
      		  $operation .= "\t\t</wsdl:operation>" . PHP_EOL;

      		  $this->bOperations .= $operation;
	  }

	  /**
	   * Creates a Port Type operation and appends it to the class property ptOperations.
	   * 
	   * @param string $method The name of the port type operation
	   * @return void
	   */
	  private function createPortTypeOperation($method) {

			  $operation = "\t\t<wsdl:operation name=\"$method\">" . PHP_EOL;
			  $operation .= "\t\t\t<input message=\"tns:" . $method . "" .
			  		 (($this->SOAPBinding->style == SOAPStyle::DOCUMENT &&
			  		 	 $this->SOAPBinding->use == SOAPStyle::LITERAL) ? '" name="' . $method . '' : 'Request') . "\"/>" . PHP_EOL;
			  $operation .= "\t\t\t<output message=\"tns:" . $method . "" .
			  		 (($this->SOAPBinding->style == SOAPStyle::DOCUMENT &&
			  		 	 $this->SOAPBinding->use == SOAPStyle::LITERAL) ? 'Response" name="' . $method . 'Response' : 'Response') . "\"/>" . PHP_EOL;
			  $operation .= "\t\t</wsdl:operation>" . PHP_EOL;

			  $this->ptOperations .= $operation;
	  }

	  /**
	   * Creates the wsdl:types node containing each of the complexType's used in the web service
	   * 
	   * @return String The complexType elements represented in XML
	   */
	  private function getTypes() {

	  		  if(!$this->types) return;

	  		  $xml = "\t<wsdl:types>" . PHP_EOL;
	  		  $xml .= "\t\t<xsd:schema targetNamespace=\"" . $this->targetNamespace . "\">" . PHP_EOL;
			  $xml .= $this->types;
			  $xml .= "\t\t</xsd:schema>" . PHP_EOL;
	  		  $xml .= "\t</wsdl:types>" . PHP_EOL;

	  		  return $xml;
	  }

	  /**
	   * Creates a new complex type node for the specified class / data type.
	   * 
	   * @param mixed $class The name of the data type
	   * @param string $ns The namespace used for this data type
	   * @return void
	   */
	  private function objectToComplexType($class, $ns, $isWrapped = false) {

	  		  if(in_array($class, $this->processedComplexObjects)) return;

	  		  $eol = PHP_EOL;

	  		  $reflClass = new ReflectionClass($class);
			  $xml = "\t\t\t<xsd:complexType name=\"" . $reflClass->getName() . "\">" . $eol;
			  $xml .= "\t\t\t\t<xsd:sequence>" . $eol;
			  
			  foreach($reflClass->getProperties() as $property) {

			  		   $type = $this->getPropertyTypeFromDocBlock($property);

			  		   if($this->SOAPBinding->use == SOAPStyle::LITERAL &&	preg_match('/\[\]/', $type)) {

			  		   	   $type = preg_replace('/\[\]/', '', $type);
			  		   	   $ns = $this->getTypeNamespace($type, $property->name);
			  		   	   $xml .= "\t\t\t\t\t<xsd:element name=\"" . $property->name . "\" minOccurs=\"0\" maxOccurs=\"unbounded\" type=\"" . $ns . ":" . $type . "\"/>" . $eol;
			  		   }
			  		   else {

			  		   	   $ns = $this->getTypeNamespace($type, $property->name);
					   	   $xml .= "\t\t\t\t\t<xsd:element name=\"" . $property->name . "\" type=\"" . $ns . ":" . $type . "\"/>" . $eol;
			  		   }
			  }

			  $xml .= "\t\t\t\t</xsd:sequence>" . $eol;
			  $xml .= "\t\t\t</xsd:complexType>" . $eol;

			  $this->types .= $xml;

			  array_push($this->processedComplexObjects, $class);
	  }

	  /**
	   * Creates a Document/Literal wrapped complex type
	   * 
	   * @return void
	   */
	  private function createWrappedMethod(ReflectionMethod $method) {

	  		  $this->getTypeNamespace($method->getName(), $method->getName());
	  		  $this->getTypeNamespace($method->getName() . 'Response', $method->getName());

	  		  $eol = PHP_EOL;

	  		  $xml = "\t\t\t<xsd:element name=\"" . $method->getName() . "\" type=\"tns:" . $method->getName() . "\"/>" . $eol;
	  		  $xml .= "\t\t\t<xsd:element name=\"" . $method->getName() . "Response\" type=\"tns:" . $method->getName() . "Response\"/>" . $eol;
	  		  $this->types .= $xml;

	  		  return;
	  }

	  /**
	   * Creates a new complex type node for the specified array.
	   * 
	   * @param string $name The complexType name
	   * @param string $ns The namespace to use as a reference for the data type
	   * @return void
	   */
	  private function arrayToComplexType($name, $ns, $paramName) {

	  		  if(in_array($name, $this->processedComplexArrays)) return;

	  		  $eol = PHP_EOL;

	  		  if($this->SOAPBinding->use == SOAPStyle::ENCODED) {

	  		  	  $type = preg_replace('/Array/', '[]', $name);
	  		      $name = preg_replace('/\[\]/', '', $name);

				  $xml = "\t\t\t<xsd:complexType name=\"" . $name . "\">" . $eol;
				  $xml .= "\t\t\t\t<xsd:complexContent>" . $eol;
				  $xml .= "\t\t\t\t\t<xsd:restriction base=\"soap-enc:Array\">" . $eol;
				  $xml .= "\t\t\t\t\t\t<xsd:attribute ref=\"soap-enc:arrayType\" wsdl:arrayType=\"" . $ns . ":" . $type . "\"/>" . $eol;
				  $xml .= "\t\t\t\t\t</xsd:restriction>" . $eol;
				  $xml .= "\t\t\t\t</xsd:complexContent>" . $eol;
				  $xml .= "\t\t\t</xsd:complexType>" . $eol;
	  		  }

	  		  else if($this->SOAPBinding->use == SOAPStyle::LITERAL) {

	  		  	  $type = $name;

	  		  	  $xsdTypes = array('string', 'int', 'float', 'double', 'boolean');

	  		  	  preg_match_all('/Array/', $name, $matches);
	  		  	  if(count($matches[0]) == 1) {

		  		  	  $rawType = preg_replace('/Array/', '', $name);
		  		  	  if(in_array($rawType, $xsdTypes)) {

		  		  	  	  $type = $rawType;
		  		  	  	  $ns = 'xsd';
		  		  	  }
		  		  	  else {
		  		  	  	
		  		  	  	  $type = $rawType;
		  		  	  	  $ns = 'tns';
		  		  	  }
	  		  	  }
	  		  	  else {
	  		  	  	
	  		  	  	  $type = preg_replace('/Array/', '', $name, 1);
	  		  	  }
	  		  	  
	  		  	  $xml = "\t\t\t<xsd:complexType name=\"" . $name . "\">" . $eol;
				  $xml .= "\t\t\t\t<xsd:sequence>" . $eol;
				  $xml .= "\t\t\t\t\t\t<xsd:element minOccurs=\"0\" maxOccurs=\"unbounded\" name=\"$paramName\" type=\"" . $ns . ":" . $type . "\"/>" . $eol;
				  $xml .= "\t\t\t\t</xsd:sequence>" . $eol;
				  $xml .= "\t\t\t</xsd:complexType>" . $eol;
	  		  }

			  $this->types .= $xml;

			  array_push($this->processedComplexArrays, $name);
	  }

	  /**
	   * Gets a property / field level data type from the web service PHP-doc comments block for the specified property
	   * 
	   * @param ReflectionProperty $property A PHP ReflectionProperty instance representing the property to extract the data type for.
	   * @return string The extracted PHP data type
	   */
	  private function getPropertyTypeFromDocBlock(ReflectionProperty $property) {

	  		  preg_match('/@var\\s*(.*?)\\s/i', $property->getDocComment(), $matches);
	  		  return (isset($matches[1])) ? trim($matches[1]) : 'anyType';	  		  	  
	  }

	  /**
       * Gets a param data type from the web service PHP-doc comments block for the method being inspected.
       * 
       * @param ReflectionMethod $method A PHP ReflectionMethod instance representing the method to extract the parameter data type for.
       * @return string The extracted PHP data type 
	   */
	  private function getParameterTypeFromDocBlock(ReflectionMethod $method, $param) {

	  		  preg_match('/@param\\s*(.*?\\[?\\]?)\\s*\$' . $param->name . '/i', $method->getDocComment(), $matches);
	  		  return (isset($matches[1])) ? trim($matches[1]) : 'anyType';	  		  	  
	  }

	  /**
       * Gets a web servie method return type from the PHP-doc comments block on the method being inspected.
       * 
       * @param ReflectionMethod $method A PHP ReflectionMethod instance representing the method to extract the parameter data type for.
       * @return string The extracted PHP data type 
	   */
	  private function getReturnTypeFromDocBlock(ReflectionMethod $method) {

	  		  preg_match('/@return\\s*(.*?)\\s/i', $method->getDocComment(), $matches);
	  		  return (isset($matches[1])) ? trim($matches[1]) : null;
	  }

	  /**
	   * Returns the WSDL namespace value based on data type.
	   * 
	   * @param string $type The data type to inspect
	   * @return string The namespace value to assign
	   */
	  private function getTypeNamespace($type, $paramName) {

	  		  // This is an array
	  		  if(preg_match('/\[\]/', $type)) {

		  		  $type = preg_replace('/\[\]/', 'Array', $type);
		  		  $ns = 'tns';
		  		  $this->arrayToComplexType($type, $ns, $paramName);
		  		  return $ns;
	  		  }

	  		  // The type is not an array
	  		  if(class_exists($type, false)) {

	  		  	  // The type is an object
		  		  $ns = 'tns';
		  		  $this->objectToComplexType($type, $ns);

		  		  return $ns;
		  	  }
		  	  else {

		  	  	  // The type is primitive
		  	  	  return 'xsd';
	  		  }
	  }
}
?>