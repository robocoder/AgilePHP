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

require_once 'webservice/remoting/RemotingException.php';
require_once 'webservice/remoting/RemoteMethod.php';

/**
 * Provides client side JavaScript remoting to PHP objects. Handles 
 * marshalling/unmarshalling of JSON objects between the client and server
 * as well as providing client side stubs for remote classes.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * @abstract
 */
abstract class Remoting extends BaseController {

	  	 private $class;
	  	 private $models = array();

	  	 /**
	  	  * Initalizes the Remoting instance when the extension class is created.
	  	  * 
	  	  * @param String $class
	  	  * @return void
	  	  */
	  	 public function __construct() {

				set_error_handler('Remoting::ErrorHandler');
				ob_start(array($this, 'captureErrors'));

				$this->createRenderer('AJAXRenderer');
	  	 }

		 /**
	      * Loads a Remoting stub for the specified class and outputs it in JSON format
	      * to the client. If no class is specified a Remoting stub will be generated
	      * for the extension class.
	   	  *  
	   	  * @param $class The class to remote
	   	  * @return void
	   	  * @throws RemotingException
	   	  */
	  	 public function index($class = null) {

	  	 		$clazz = ($class) ? $class : $this;

	  	 		$c = new ReflectionClass($clazz);
	  	 		$this->class = $c->getName();

	  	 		$this->createStub();
	  	 }

	  	 /**
	  	  * Alias for index method. Clearly indicates what action is taking
	  	  * place, rather than looking at a call to the 'index' method in
	  	  * the head of the html page. 
	  	  *
	  	  * @param String $class The name of the class to expose via remoting
	  	  * @return void
	  	  */
	  	 public function load($class) {
	  	 	$this->index($class);
	  	 }

	  	 /**
	  	  * Sets the name of the class to remote
	  	  * 
	  	  * @param String $class The class name to remote
	  	  * @return void
	  	  */
	  	 protected function setClass($class) {

	  	 		   $this->class = $class;
	  	 }

	  	 /**
	  	  * Returns the name of the class being remoted
	  	  * 
	  	  * @return The name of the class being remoted
	  	  */
	  	 protected function getClass() {

	  	 		   return $this->class;
	  	 }

	  	 /**
	   	  * Returns the current session id. If a session is not active a new
	   	  * session is created and the id is returned.
	   	  * 
	   	  * @return String Session id for the current request
	  	  */
	  	 public function getSessionId() {

	  		   $sessionId = Scope::getSessionScope()->getSessionId();

	  		   Log::debug('Remoting::getSessionId Returning session id \'' . $sessionId . '\'.');

	  		   return $sessionId;
	  	 }

		 /**
	  	  * Destroys the session used for stateful remoting
	 	  *  
	   	  * @param String $sessionId The id of the session to destroy
	      * @return void
	      */
	     public function destroySession($sessionId) {

	  		    $session = Scope::getSessionScope()->setSessionId($sessionId);
	  		    $session->destroy(); 
	     }

	     /**
	      * Generates a client-side stub for the specified model
	      * 
	      * @param String $class The name of the class to generate the client-side stub for
	      * @param boolean $typeCast Optional flag which tells the JavaScript generator
	      *        to type cast constructor fields as defined in the models PHP-doc comment.
	      * @return void
	      */
	     protected function registerModel($class, $typeCast = false) {

	     	// PHP namespace support
            $namespace = explode('\\', $class);
            $className = array_pop($namespace);
            $namespace = implode('.', $namespace);

	     	$js = '';

	     	// Create namespace
	     	$jsNamespace = '';
	     	if($namespace) {

	     	   $doDeclare = true;
	     	   foreach(explode('.', $namespace) as $name) {

	     	   	    $jsNamespace = (!$jsNamespace) ? $name : $jsNamespace . '.' . $name;
	     	   	    if($jsNamespace) {

	     	   	       // Make sure namespace was not generated from another model
		     	       foreach($this->models as $model)
		     	   	      if(strpos($model, "{$jsNamespace} = {};") !== false)
		     	   	         $dontGenerateNamespace = true;

		     	   	   if(!isset($dontGenerateNamespace)) {

		     	   	      $js .= ($doDeclare) ? "var {$jsNamespace} = {};\n" : "{$jsNamespace} = {};\n";
		     	   	      $doDeclare = false;
		     	   	   }
	     	   	    }
	     	   }
	     	   
	     	   $js .= "\n";
	     	}

	        // If the requested class has already been generated, abort.
   			foreach($this->models as $model) {

   			  if( ( ($jsNamespace) ?
   			        strpos($model, "{$jsNamespace}.{$className} = function") !== false :
   			        strpos($model, "function {$className}") !== false
   			      )
   			  ) return;
   			}

	     	// Create constructor
	     	if($jsNamespace) {

	     		$js .= "// {$jsNamespace}.{$className} Model\n";
	     		$js .= "{$jsNamespace}.{$className} = function() {\n";
	     	}
	     	else {

	     		$js .= "// {$className} Model\n";
	     		$js .= "function {$className}() {\n";
	     	}

	     	// Create constructor properties
	     	$clazz = new ReflectionClass($class);
	     	$properties = $clazz->getProperties();
	     	foreach($properties as $property) {

	     		if(strpos($property->name, 'interceptedTarget') !== false) continue;

	     		$value = 'null';

	     		if($typeCast) {

	     			$type = DocBlockParser::getPropertyType($property);
	     			if($type == 'array') $value = '[]';
	     			if($type == 'object' || DocBlockParser::isUserSpaceObject($type)) {

	     				$value = 'new ' . str_replace('\\', '.', $type) . '()';

	     				// Auto-generate model stub for the object if not already generated
	     				if(!isset($this->models[0])) $this->registerModel($type, true);

	     				foreach($this->models as $model)
	     				   if((strpos($model, "function {$type}") === false) ||
   		         			  (strpos($model, ".{$type} = function") === false))
	     				      $this->registerModel($type, true);
	     			}
	     		}

	     		$js .= "\tthis.{$property->getName()} = {$value};\n";  
	     	}
	     	$js .= "};\n";

	     	// Create getters/setters
	     	foreach($properties as $property) {

	     		$propertyName = $property->getName();
	     		
	     		if(strpos($propertyName, 'interceptedTarget') !== false) continue;
	     		
	     		$getter = 'get' . ucfirst($propertyName);
	     		$setter = 'set' . ucfirst($propertyName);

	     		// Setter
	     		$js .= ($jsNamespace) ?  "{$jsNamespace}.{$className}.prototype.{$setter} = function({$propertyName}) {\n" :
	     					"{$className}.prototype.{$setter} = function({$propertyName}) {\n";
	     		$js .= "\tthis.{$propertyName} = {$propertyName};\n";
	     		$js .= "};\n";

	     		// Getter
	     		$js .= ($jsNamespace) ? "{$jsNamespace}.{$className}.prototype.{$getter} = function() {\n" :
	     					"{$className}.prototype.{$getter} = function() {\n";
	     		$js .= "\treturn this.{$propertyName};\n";
	     		$js .= "};\n";
	     	}

	     	array_push($this->models, $js);
	     }

		 /**
		  * Creates a dynamic javascript proxy stub/interface used for remoting standard PHP classes.
		  * The generated code is output to the client in JSON format, intended for consumption from
		  * an HTML script src tag.
		  * 
	  	  * @return void
	  	  * @throws RemotingException
		  */
		 protected function createStub() { 

		  		   try {
		  		 		  $clazz = new AnnotatedClass($this->class);
	
		  		 		  // Create javascript object w/ matching constructor parameters
		  		 		  $constructor = $clazz->getConstructor();
		  		 		  if($constructor) {

		  		 		  	  $js = '// ' . $this->class . " Service Class\n";
		  		 			  $js .= 'function ' . $this->class . '(';
		  		 			  $params = $constructor->getParameters();
		  		 			  for($i=0; $i<count($params); $i++) {
		  		 				
		  		 				   $js .= $params[$i]->getName();
		  		 				   $js .= ($i+1 < count($params)) ? ', ' : '';
		  		 			  }
		  		 			  $js .= ") {\n";
		  		 			  for($i=0; $i<count($params); $i++)
		  		 				   $js .= 'this.' . $params[$i]->getName() . ' = ' . $params[$i]->getName() . ";\n";
	
		  		 			  $js .= "};\n";
		  		 		  }
		  		 		  else
		  		 			  $js = 'function ' . $this->class . "() { };\n";

		  		 		  // create methods
		  		 		  $methods = $clazz->getMethods();
		  		 		  for($i=0; $i<count($methods); $i++) {
	
	  		 				   if($methods[$i]->isAnnotated() && $methods[$i]->hasAnnotation('RemoteMethod')) {

	  		 				   	   // create function
		  		 				   $js .= $this->class . '.prototype.' . $methods[$i]->getName() . ' = function(';
		  		 				   $params = $methods[$i]->getParameters();
		  		 				   for($j=0; $j<count($params); $j++) {

		  		 				 	 	$js .= $params[$j]->getName();
		  		 				 	 	$js .= (($j+1) < count($params)) ? ', ' : ', callback, exceptionHandler';
		  		 				   }
		  		 				   if(!count($params)) $js .= 'callback, exceptionHandler';
		  		 				   $js .= ") {\n";
		  		 				   // function body
		  		 				   $js .= "\treturn AgilePHP.Remoting.invoke(this, '" . $methods[$i]->getName() . "', arguments);\n";
		  		 				   // function closure
	  		 				 	   $js .= "};\n";
	  		 				   }
		  		 		  }

		  		 		  // Remoting internals - store class name
		  		 		  $js .= $this->class . ".prototype._class = '" . $this->class . "';\n\n";

	  		 			  // Add model stubs
	  		 			  foreach($this->models as $model)
	  		 			  	$js .= $model . "\n";

		  		 		  echo $js;
		  		 }
		  		 catch(Exception $e) {

		  		 		throw new RemotingException($e->getMessage(), $e->getCode());
		  		 }
		  }

		  /**
	       * Invokes a non-persistent/stateful instance of the requested class/method
	       * passing in arguments if any were defined. This non-stateful approach is
	       * how most RPC web services work.
	       * 
	   	   * @return mixed Returns the result of the invocation
	   	   * @throws RemotingException
	       */
	      public function invoke() {

	    	     $request = Scope::getRequestScope();

	    	     $stateful = $request->getSanitized('stateful');
	    	     if($stateful) $this->invokeStateful();

	    	     $class = $request->getSanitized('class');
	    	     $method = $request->getSanitized('method');
	    	     $constructorArgs = $this->decode($request->getSanitized('constructorArgs'));
	    	     $args = $this->decode($request->getSanitized('parameters'));

	    	     // Security, Security, Security...
	    	     $clazz = new AnnotatedClass($class);
	    	     $methods = $clazz->getMethods();
	    	     for($i=0; $i<count($methods); $i++)
	  		 		 if($methods[$i]->getName() == $method && !$methods[$i]->hasAnnotation('RemoteMethod'))
	  		 		 	 throw new RemotingException('No hacking please...');

	  		     Log::debug('Remoting::invoke Invoking class \'' . $class . '\', method \'' . $method .
	  		 	   	 '\', constructorArgs \'' . print_r($constructorArgs, true) . '\', args \'' . print_r($args, true) . '\'.');

	  		     try {
		  	           $clazz = new ReflectionClass($class);
		  	           $instance = $constructorArgs ? $clazz->newInstanceArgs((array)$constructorArgs) : $clazz->newInstance();
		  		       $m = $clazz->getMethod($method);

		  		       $this->getRenderer()->render($args ? $m->invokeArgs($instance, (array)$args) : $m->invoke($instance));
	  		     }
	  		     catch(Exception $e) {

	  		 		    throw new RemotingException($e->getMessage(), $e->getCode());
	  		     }
	      }

		  /**
		   * Overloads the getBaseJS method defined in BaseController to return the client side AgilePHP
		   * library with default AgilePHP.Remoting.controller value pre-defined according to the
		   * controller which invoked the call.
		   * 
		   * @param bool $debug True to enable client side AgilePHP debugging.
		   * @return void
		   */
		  public function getBaseJS($debug = false) {
	
		  		 $js = file_get_contents(AgilePHP::getFrameworkRoot() . DIRECTORY_SEPARATOR . 'AgilePHP.js');
	
		  		 if($debug) $js .= "\nAgilePHP.setDebug(true);";
	
		  		 $js .= "\nAgilePHP.setRequestBase('" . AgilePHP::getRequestBase() . "');";
		  		 $js .= "\nAgilePHP.Remoting.setController('" . MVC::getController() . "');";
	
		  		 header('content-type: text/javascript');
		  		 echo (extension_loaded('jsmin')) ? jsmin($js) : $js;
		  }

		  /**
		   * Decodes JSON formatted POST variables into a PHP object.
		   * 
		   * @param String $data The client side JSON object to parse
		   * @return stdClass The JSON decoded stdClass object
		   * @throws RemotingException if the received data does not unmarshall into a PHP object
		   */
		  private function decode($data) {

		  		  if(!$data) return;

		  		  Log::debug('Remoting::decode ' . $data);

		  		  $o = json_decode(htmlspecialchars_decode(stripslashes(urldecode($data))));
		  		  if(!is_object($o)) throw new RemotingException('Malformed data');

		  		  return $o;
		  }

		  /**
		   * Parses each PHP output buffer for php fatal error and converts to RemotingException if present.
		   * 
		   * @param string $buffer PHP output buffer
		   * @return void
		   * throws RemotingException
		   */
		  public function captureErrors($buffer) {
	
				 $matches = array();
				 $errors = '';
	
				 if(preg_match('/(error<\/b>:)(.+)(<br)/', $buffer, $regs)) {
	
				 	 $err = preg_replace("/<.*?>/","",$regs[2]);
				 	 $o = new stdClass;
				 	 $o->_class = 'RemotingException';
				 	 $o->message = $err;
				 	 $o->trace = debug_backtrace();

			         //$buffer = json_encode(array('_class' => 'RemotingException', 'message' => $err, 'trace' => debug_backtrace()));
			         $buffer = json_encode($o);
			     }
			     return $buffer;
		  }
	
		  /**
		   * Custom PHP error handling function which throws an RemotingException instead of echoing.
		   * 
		   * @param Integer $errno Error number
		   * @param String $errmsg Error message
		   * @param String $errfile The name of the file that caused the error
		   * @param Integer $errline The line number that caused the error
		   * @return false
		   * @throws FrameworkException
		   */
	 	  public static function ErrorHandler($errno, $errmsg, $errfile, $errline) {
	
	 	  		 $entry = PHP_EOL . 'Number: ' . $errno . PHP_EOL . 'Message: ' . $errmsg . 
	 	  		 		  PHP_EOL . 'File: ' . $errfile . PHP_EOL . 'Line: ' . $errline;
	
	 	  		 throw new RemotingException($errmsg, $errno, $errfile, $errline);
		  }
	
		  /**
		   * Flush PHP output buffer and restore error handler
		   */
		  public function __destruct() {

		  		 ob_end_flush();
		  		 restore_error_handler();
		  }
}
?>