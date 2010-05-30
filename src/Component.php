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

	  private static $instance;

	  private $name;
	  private $version;
	  private $enabled;
	  private $params = array();

	  /**
	   * Component constructor parses component.xml and populates base component properties.
	   * 
	   * @return void
	   */
	  public function __construct() {

	  		 $class = get_class( $this );
	  		 $componentXml = 'components' . DIRECTORY_SEPARATOR . $class . DIRECTORY_SEPARATOR . 'component.xml';
 			 if( !file_exists( $componentXml ) )
 				 throw new AgilePHP_Exception( $componentXml . ' does not exist' );

 			 $xml = simplexml_load_file( $componentXml );
 			 /**
			  * @todo Validate the component.xml file against the component.dtd
			  * 
 			  * $dom = new DOMDocument();
 			  * $dom->Load( $componentXml );
			  * if( !$dom->validate() );
			  *	   throw new AgilePHP_PersistenceException( 'component.xml Document Object Model validation failed.' );
			  */
 			 $properties = array();
 			 $types = array();
 			 foreach( $xml->component->param as $param ) {

 					  $properties[(string)$param->attributes()->name] = (string)$param->attributes()->value;
 					  $types[(string)$param->attributes()->name] = (string)$param->attributes()->type;
 			 }

 			 $this->name = (string)$xml->component->attributes()->name;
 			 $this->version = (string)$xml->component->attributes()->version;
 			 $this->enabled = (string)$xml->component->attributes()->enabled;

 			 foreach( $xml->component->param as $param ) {

 					  $cp = new ComponentParam();
 					  $cp->setName( (string)$param->attributes()->name );
 					  $cp->setType( (string)$param->attributes()->type );
 					  $cp->setValue( (string)$param->attributes()->value );

 					  array_push( $this->params, $cp );
 			 }
	  }

	  /**
	   * Sets the name of the component
	   * 
	   * @param string $name The friendly name of the component
	   * @return void
	   */
	  protected function setName( $name ) {

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
	  protected function setVersion( $version ) {

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
	  protected function setEnabled( $enabled ) {

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
	   * Dispatches a request to the specified controller / action / parameters
	   * using the component controller directory as a sandbox.
	   * 
	   * @param BaseController $controller The controller instance responsible for the dispatched action
	   * @param string $action The controller's action method responsible for the dispatched action
	   * @param array $parameters An array of parameters to pass into the specified controller / action combo
	   * @return mixed The return from the invoked call
	   */
	  protected function dispatch( BaseController $controller, $method, array $parameters = null ) {

	  			$controllerCls = get_class( $controller );
	  			$namespace = explode( '\\', $controllerCls );
	  			$controllerName = array_pop( $namespace );

	  			$component = get_class( $this );

	  		  	$f = AgilePHP::getFramework()->getWebRoot() . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . $component . DIRECTORY_SEPARATOR . 'control';
		  	  	$it = new RecursiveDirectoryIterator( $f );
			  	foreach( new RecursiveIteratorIterator( $it ) as $file ) {

			   	       	 if( substr( $file, -1 ) != '.' && substr( $file, -2 ) != '..' ) {

			   	       	   	 $pieces = explode( DIRECTORY_SEPARATOR, $file );
			   	      	   	 $item = array_pop( $pieces ); 

			   	      	   	 if( $item == $controllerName . '.php' ) {

				 		   	   	 __autoload( $controllerName );
				 		       	 if( !$parameters ) return $controller->$method();
					  		 	 return call_user_func_array( array( $controller, $method ), $parameters );
				 		   	 }
				       	 }
			  	}

	  		  	throw new AgilePHP_Exception( 'The requested component controller \'' . $controllerName . '\' could not be found.' );
	  }
}

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
	  public function __construct( $name = null, $type = null, $value = null ) {

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
	  public function setName( $name ) {

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
	  public function setType( $type ) {

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
	  public function setValue( $value ) {

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