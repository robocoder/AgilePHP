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
 * Utility class / helper which creates a PHP class file with getters
 * and setters for the specified fields. Optionally creates an ORM
 * DomainModel.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp.util
 */
class ModelGenerator {

	private $namespace;
	private $className;
	private $fields = array();
	private $domainModel = false;
	private $includePhpTags = false;

	/**
	 * Initializes the ModelGenerator
	 *
	 * @param String $namespace The PHP namespace to generate. Accepts Java-style
	 *        dot notation or PHP backslash style.
	 * @param String $className The PHP class name to generate
	 * @param array<String> $fields List of field names
	 * @param boolean $domainModel True to generate a DomainModel
	 *        implementation, false to generate a standard PHP class
	 * @return void
	 */
	public function __construct($namespace = null, $className = null,
			 array $fields = array(), $domainModel = false, $includePhpTags = false) {

		$namespace = str_replace('.', '\\', $namespace);
		$this->namespace = $namespace;
		$this->className = $this->toCamelCase($className);
		$this->fields = $fields;
		$this->domainModel = $domainModel;
		$this->includePhpTags = $includePhpTags;
	}

	/**
	 * Sets the generated namespace. Accepts either Java-style dot notation
	 * or PHP backslash style.
	 * 
	 * @param string $namespace The class namespace
	 * @return void
	 */
	public function setNamespace($namespace) {

	    $namespace = str_replace('.', '\\', $namespace);
		$this->namespace = $namespace;
	}

	/**
	 * Sets the generated class name
	 * 
	 * @param String $name The PHP class name to generate
	 * @return void
	 */
	public function setClassName($name) {
		$this->className = $name;
	}

	/**
	 * Sets the generated class fields / properties
	 * 
	 * @param array<String> $fields The list of field names
	 * @return void
	 */
	public function setFields(array $fields) {
		$this->fields = $fields;
	}

	/**
	 * Tells the ModelGenerator to create a DomainModel implementation
	 * instead of a standard PHP class. 
	 *
	 * @param boolean $flag True to generate a DomainModel implementation,
	 * 		  false to create a standard PHP class.
	 * @return void
	 */
	public function setDomainModel($flag) {
		$this->domainModel;
	}

	/**
	 * Tells the ModelGenerator to include the opening and closing PHP tags
	 * in the generated source code.
	 * 
	 * @param boolean $flag True to include PHP opening and closing tags, false otherwise
	 * @return void 
	 */
	public function setIncludePhpTags($flag) {
		$this->includePhpTags = $flag;
	}

	/**
	 * Generates the model and returns the PHP code
	 * 
	 * @return The generated model
	 */
	public function createModel() {

		$lineBreak = PHP_EOL;

		$className = $this->className;
		$fields = $this->fields;

		// Create constructor arguments and getters/setters
		$constructorArgs = '';
		$constructorBody = '';
		$setters = array();
		$getters = array();
		for($i=0; $i<count($fields); $i++) {

			// Convert field to camel case
			$field = $this->toCamelCase($fields[$i]);

			$constructorArgs .= '$' . $field . ' = null';
			$constructorBody .= "        \$this->{$field} = \${$field};{$lineBreak}";

			$setter = 'set' . ucfirst($field);
			$getter = 'get' . ucfirst($field);

			array_push($setters, "    public function {$setter}(\$$field) {{$lineBreak}        \$this->{$field} = \$$field;{$lineBreak}    }");
			array_push($getters, "    public function {$getter}() {{$lineBreak}        return \$this->{$field};{$lineBreak}    }");

			if(($i+1) < count($fields))
			   $constructorArgs .= ', ';
		}

		// Begin class
		$code = ($this->namespace) ? "namespace {$this->namespace};${lineBreak}{$lineBreak}" : '';

		if($this->includePhpTags) $code .= "<?php{$lineBreak}";

		$code .= ($this->domainModel) ?
			"class {$className} extends DomainModel {{$lineBreak}" :
			"class {$className} {{$lineBreak}";

		// Fields / properties
		foreach($fields as $field)
		    $code .= "{$lineBreak}    private \${$field};";

		// Constructor
		$code .= "{$lineBreak}{$lineBreak}    public function __construct({$constructorArgs}) {{$lineBreak}{$constructorBody}    }{$lineBreak}{$lineBreak}"; 

        // Getters and setters
		for($i=0; $i<count($setters); $i++) {

			$code .= $setters[$i] . $lineBreak . $lineBreak;
			$code .= $getters[$i] . $lineBreak;

			if(($i+1) <count($setters))
        	  $code .= $lineBreak;
		}

		// End class
		$code .= '}';

		if($this->includePhpTags) $code .= "{$lineBreak}?>";

		return $code;
	}

	/**
	 * Converts the specified field name from underscore format to camel case format.
	 *
	 * @param String $field The field name to convert to camel case
	 * @return void
	 */
	public function toCamelCase($field) {

		$pieces = explode('_', $field);
	    $pieces = $pieces ? array_map('ucfirst', $pieces) : array($field);
	    $pieces[0] = lcfirst($pieces[0]);

	    return implode('', $pieces);
	}
}
?>