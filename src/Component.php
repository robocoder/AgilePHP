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
 * Provides base logic for AgilePHP components. Read/transform component.xml
 * to native PHP objects upon construction and provides helper methods for
 * accessing configuration parameters and dispatching requests for component
 * front controller.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp
 * @abstract
 */
abstract class Component extends BaseController {

	  private $name;
	  private $type;
	  private $version;
	  private $enabled;
	  private $params = array();

	  /**
	   * Component constructor parses component.xml and populates base component properties.
	   *
	   * @return void
	   */
	  public function __construct() {

	         $class = preg_replace('/_Intercepted/', '', get_class($this));

	         // Retrieve the component.xml configuration (Use caching if enabled)
             if($cacher = AgilePHP::getCacher()) {

                $key = 'AGILEPHP_COMPONENT_XML_' . $class;
                if($cacher->exists($key))
                   $xml = simplexml_load_string($cacher->get($key));
             }
             if(!isset($xml)) {

    	  		 $componentXml = 'components' . DIRECTORY_SEPARATOR . $class . DIRECTORY_SEPARATOR . 'component.xml';
     			 if(!file_exists($componentXml))
     				throw new FrameworkException($componentXml . ' does not exist');

     			 $xml = simplexml_load_file($componentXml);
     			 if($cacher) $cacher->set($key, $xml->asXML());
             }

             // Set the component params (Use caching if enabled)
             if($cacher) {

                $key = 'AGILEPHP_COMPONENT_PARAMS_' . $class;
                if($cacher->exists($key))
                   $this->params = $cacher->get($key);
             }
             if(!isset($this->params[0]) && !$cacher) {

     			$dom = new DOMDocument();
     			$dom->Load($componentXml);
    			if(!@$dom->validate()) {

    			   $error = error_get_last();
    			   throw new FrameworkException('component.xml Document Object Model validation failed. ' . $error['message']);
    			}

     			$properties = array();
     			$types = array();
     			foreach($xml->component->param as $param) {

     					$properties[(string)$param->attributes()->name] = (string)$param->attributes()->value;
     					$types[(string)$param->attributes()->name] = (string)$param->attributes()->type;
     			}

     			$this->name = (string)$xml->component->attributes()->name;
     			$this->type = (string)$xml->component->attributes()->type;
     			$this->version = (string)$xml->component->attributes()->version;
     			$this->enabled = (string)$xml->component->attributes()->enabled;

     			foreach($xml->component->param as $param) {

     				    $cp = new ComponentParam();
     					$cp->setName((string)$param->attributes()->name);
     					$cp->setType((string)$param->attributes()->type);
     					$cp->setValue((string)$param->attributes()->value);

     					array_push($this->params, $cp);
     			}

     			if($cacher) $cacher->set($key, $this->params);
             }

             // Add orm configs to ORM Database object if present
 			 if(isset($xml->component->orm->table)) {

 			    // @todo Implement logic in Studio to check for conflicting table and
 			    //       model names during install to eliminate runtime validation overhead.

 			    $database = ORMFactory::getDialect()->getDatabase();
 			    foreach($xml->component->orm->table as $table)
 			         $database->addTable(new Table($table));
 			 }

 			 // Prepend component autoloader
 			 spl_autoload_register('Component::autoload', true, true);
	  }

	  /**
	   * Component autoloader responsible for loading classes from the component
	   * space (components/#componentName# for source components, phar://#componentName#
	   * for phar components).
	   *
	   * @param string $class The class to load
	   * @return void
	   */
	  public function autoload($class) {

	  	     // Use caching if enabled
             if($cacher = AgilePHP::getCacher()) {

                $key = 'AGILEPHP_COMPONENT_AUTOLOAD_' . $class;
                if($clazz = $cacher->get($key)) {

                   if(AgilePHP::getConfiguration()->annotations) {

                      new InterceptorFilter($class);
                      if(class_exists($class, false)) return;
                   }
                   require $clazz;
                   return;
                }
             }

             // PHP namespace support
             $namespace = explode('\\', $class);
             $className = array_pop($namespace);
             $namespace = implode(DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;

             $phar = 'phar://' . $namespace . $className . '.php';

             if(file_exists($phar)) {

                if(AgilePHP::getConfiguration()->annotations) {

                   new InterceptorFilter($phar);
                   if(class_exists($class, false)) return;
                }
                require $phar;
                return;
             }

             // Search component directory
             $component = AgilePHP::getWebRoot() . DIRECTORY_SEPARATOR . 'components' .
                             DIRECTORY_SEPARATOR . $this->name;
		  	 $it = new RecursiveDirectoryIterator($component);
			 foreach(new RecursiveIteratorIterator($it) as $file) {

		   	     	  $pieces = explode(DIRECTORY_SEPARATOR, $file);
			 		  if(array_pop($pieces) == $className . '.php') {

			 		     if($cacher) $cacher->set($key, $file->getPathname());

			 		     if(AgilePHP::getConfiguration()->annotations) {

                            new InterceptorFilter($class);
                            if(class_exists($class, false)) return;
                         }

		     	 		 require $file->getPathname();
		     	 		 return;
			 		  }
			 }
	  }

	  /**
	   * Sets the name of the component
	   *
	   * @param string $name The friendly name of the component
	   * @return void
	   */
	  protected function setName($name) {

	  		 	$this->name = $name;
	  }

	  /**
	   * Gets the name of the component
	   *
	   * @return string The name of the component
	   */
	  protected function getName() {

	  		 	return $this->name;
	  }

	  /**
	   * Sets the version of the component
	   *
	   * @param string $version The version of the component
	   * @return void
	   */
	  protected function setVersion($version) {

	  		 	$this->version = $version;
	  }

	  /**
	   * Gets the version of the component
	   *
	   * @return string The version of the component
	   */
	  protected function getVersion() {

	  		 	return $this->version;
	  }

	  /**
	   * Sets enabled flag indicating whether or not this component is enabled/disabled.
	   *
	   * @param boolean $enabled True or 1 to set enabled, false or 0 otherwise.
	   * @return void
	   */
	  protected function setEnabled($enabled) {

	  		 	$this->enabled = $enabled;
	  }

	  /**
	   * Returns whether or not this component is enabled
	   *
	   * @return boolean True if the component is enabled, false otherwise
	   */
	  protected function isEnabled() {

	  		 	return $this->enabled == true ? true : false;
	  }

	  /**
	   * Returns the configuration <param>'s for the component
	   *
	   * @return array Configuraiton params
	   */
	  protected function getParams() {

	            return $this->params;
	  }

	  /**
	   * Delegates component front controller actions to the specified controller / action.
	   *
	   * @param BaseController $controller The controller instance responsible for the delegation.
	   * @param string $action Optional delegate action method. Defaults to the front controller action name.
	   * @return void
	   */
	  protected function delegate(BaseController $controller, $action = null) {

	            $parameters = MVC::getParameters();
	            if($action) return call_user_func_array(array($controller, $action), $parameters);

	            if(isset($parameters[0])) {

	               $action = $parameters[0];
	               array_shift($parameters);
	            }
	            else {

	               $action =  MVC::getDefaultAction();
	               $parameters = array();
	            }

	            // Make sure requested action method exists
	            if(!method_exists($controller, $action))
		  	       throw new FrameworkException('The specified action \'' . $action . '\' does not exist.');

	  		  	return call_user_func_array(array($controller, $action), $parameters);
	  }
}

/**
 * Provides model for component.xml <param> element
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp
 */
class ComponentParam {

	  private $name;
	  private $type;
	  private $value;

	  /**
	   * Constructor for ComponentParam
	   *
	   * @param string $name The parameter name
	   * @param string $type The parameter data type
	   * @param string $value The parameter value
	   * @return void
	   */
	  public function __construct($name = null, $type = null, $value = null) {

	  		 $this->name = $name;
	  		 $this->type = $type;
	  		 $this->value = $value;
	  }

	  /**
	   * Sets the parameter name
	   *
	   * @param string $name The parameter name
	   * @return void
	   */
	  public function setName($name) {

	  		 $this->name = $name;
	  }

	  /**
	   * Gets the parameter name
	   *
	   * @return string The parameter name
	   */
	  public function getName() {

	  		 return $this->name;
	  }

	  /**
	   * Sets the parameter type
	   *
	   * @param string $type Sets the parameter data type
	   * @return void
	   */
	  public function setType($type) {

	  		 $this->type = $type;
	  }

	  /**
	   * Gets the parameter data type
	   *
	   * @return string The parameter data type
	   */
	  public function getType() {

	  		 return $this->type;
	  }

	  /**
	   * Sets the parameter value
	   *
	   * @param string $value The parameter value
	   * @return void
	   */
	  public function setValue($value) {

	  		 $this->value = $value;
	  }

	  /**
	   * Gets the parameter value
	   *
	   * @return string The parameter value
	   */
	  public function getValue() {

	  		 return $this->value;
	  }
}
?>