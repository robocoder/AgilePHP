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
 * @package com.makeabyte.agilephp.data.renderer
 */

/**
 * Transforms data to a JSON string
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.data.renderer
 */
class JsonRenderer implements DataRenderer {

      /**
	   * Transforms the specified PHP data to JSON. json_encode does not encode
	   * private fields within objects, so here we make use PHP 5.3+
	   * ReflectionProperty::setAccessible to access the private/protected properties.
	   * 
	   * @param mixed $data An array or object to transform into JSON
	   * @param string $name An optional class name. Defaults to null
	   * @param boolean $isChild Used internally for recursion logic
	   * @return The JSON encoded data
	   */
      public static function render($data, $name = null, $isChild = false) {

             $json = '';

	  		  if(is_array($data)) {

	  		  	 $i=0;
	  		  	 if($name && $name != 'stdClass') $json .= '"' . $name . '" : ';

	  		  	 if(!isset($data[0])) {

	  		  	  	 $json .= '[]';
	  		  	  	 return $json;
	  		  	 }

	  		  	 $json .= '[ ';
	  		  	 foreach($data as $key => $value) {

	  		  	  		$i++;
	  		  	  		if(is_object($value)) {

	  		  	  		   $class = new ReflectionClass($value);
	  		  	  		   $json .= self::render($value, $class->getName());
	  		  	  		}
	  		  	  		elseif(is_array($value))
	  		  	  			$json .= self::render($value, $name);

	  		  	  		else
	  		  	  	 	   $json .= json_encode(utf8_encode($value));

	  		  	  	 	$json .= ($i < count($data)) ? ', ' : '';
	  		  	  }
	  		  	  $json .= ' ]';
	  		  	  //if($name && $name != 'stdClass') $json .= ' }';
	  		  }

	  		  // Format objects (that have private fields)
	  		  else if(is_object($data)) {

		  		  $class = new ReflectionClass($data);
		  		  $className = $class->getName();

		  		  // stdClass has public properties
		  		  if($class->getName() == 'stdClass')
		  		  	 return json_encode($data);

	  		  	  // @todo Interceptors are still being somewhat intrusive to reflection operations
	  		      if(method_exists($data, 'getInterceptedInstance')) {

	  		     	 $className = preg_replace('/_Intercepted/', '', $className);
	  		     	 $data = $data->getInterceptedInstance();
	  		     	 $class = new ReflectionClass($data);
	  		      }

	  		      $node = ($name) ? $name : $className;
	  		      $json .= ($isChild) ? '"' . $node . '" : { ' : ' { "' . $node . '" : { ';
	  		      
		  		  $properties = $class->getProperties();
			  	  for($i=0; $i<count($properties); $i++) {
	
			  		   $property = $properties[$i];
	
			  		   $context = null;
			  		   if($property->isPublic())
			  		   	  $context = 'public';
	
			  		   else if($property->isProtected())
		  		 		   	   $context = 'protected';
	
		  		 	   else if($property->isPrivate())
			  		 		  $context = 'private';

			  		   $value = null;
			  		   if($context != 'public') {

	  		 		  	  $property->setAccessible(true);
			  		 	  $value = $property->getValue($data);
			  		 	  $property->setAccessible(false);
			  		   }
			  		   else {

			  		   	  $value = $property->getValue($data);
			  		   }

			  		   if(is_object($value) || is_array($value))
			  		   	  $json .= self::render($value, $property->getName(), true) . ' ';

			  		   else
			  		   		$json .= '"' . $property->getName() . '" : ' . json_encode(utf8_encode($value));

			  		   $json .= (($i+1) < count($properties)) ? ', ' : '';
			  	  }
			  	  $json .= ($isChild) ? '} ' : ' } }';
		  	  }

		  	  else
		  	  	  $json = json_encode(utf8_encode($data));

	  		  return $json;
      }
}
?>