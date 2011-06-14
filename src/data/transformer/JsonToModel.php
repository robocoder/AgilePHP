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
 * @package com.makeabyte.agilephp.data.transformer
 */

/**
 * Transforms a JSON string data into a populated model. The transformation
 * process uses PHP-doc comments in the model to unmarshall the data, or the
 * presence of a _class field in the JSON indicating the name of the PHP class.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.data.transformer
 */
class JsonToModel implements DataTransformer {

    /**
     * Transforms the specified data into a populated model.
     *
     * NOTE: This transformation process expects a field/property named "_class"
     * to be present in JSON objects to be transformed. If this field is not
     * present, the object will not be converted to a model instance, but rather
     * a stdClass object.
     *
     * @param string $data The string data which represents the domain model
     * 					 and state to create.
     * @param string $modelName Optional model name used to transform a JSON object
     *        to its native PHP counterpart.
     * @return Object The model if specified, otherwise a stdClass object
     * @throws TransformException
     */
    public static function transform($data, $modelName = null) {

        $o = json_decode($data);

        if(is_object($o)) {

            // No class name - return stdClass instance
            if(!$modelName) return $o;

            // Transform the JSON into an instance of the specified model
            return self::unmarshall($o, $modelName);
        }
        elseif(is_array($o)) {

            // No class name - nothing to unmarshall
            if(!$modelName) return $o;

            // Unmarshall the JSON data to an array of models
            return self::unmarshallArray($o, $modelName);
        }

        throw new TransformException('JsonToModel::transform requires either array or object data type parameter.');
    }

    /**
     * Unmarshalls the remoting payload by stripping out the _class field
     * in JSON objects and convering them to their native PHP counterpart.
     *
     * @param stdClass $data The stdClass model to unmarshall
     * @return DataModel The unmarshalled stdClass instance
     * @throws RemotingException
     * @throws ReflectionException
     */
    private static function unmarshall($data, $modelName) {

        if(!is_object($data))
           throw new RemotingException('The data passed to unmarshall must be of type object');

        if(!$modelName) return $data;

        $model = new $modelName();
        $class = new ReflectionClass($model);
        foreach($class->getProperties() as $property) {

            $propName = $property->name;

            // The client model may have included fields the server model doesn't care about
            if(!isset($data->$propName)) continue;

            $value = $data->$propName;

            // Create setter method
            $setter = 'set' . ucfirst($propName);

            // Use introspection to get the setters parameter
            // from the PHP-doc comment if present  
            $method = $class->getMethod($setter);
            $parameters = $method->getParameters();
            $parameter = $parameters[0]; 

            // Parse the data type from the PHP-doc block
            $type = DocBlockParser::getParameterType($method, $parameter);

            // stdClass object
            if($type == 'object') $value = json_decode($value);

            // User defined data type - perform recursive transformation
            elseif(DocBlockParser::isUserSpaceObject($type))
                $value = self::unmarshall($value, $type);

            // Parse the data type from the array<GenericType> PHP-doc if present
            elseif($type == 'array') {

                $elementType = DocBlockParser::getParameterArrayType($method, $parameter);
                if($type == 'object') $value = json_decode($value);
                elseif(DocBlockParser::isUserSpaceObject($elementType)) $value = self::unmarshallArray($value, $elementType);
            }

            // Set the transformed value
            $model->$setter($value);
        }

        return $model;
    }

    /**
     * Unmarshall array element(s) to model instance(s) if applicable.
     *
     * @param array $array The array to unmarshall
     * @return array The unmarshalled array
     */
    private static function unmarshallArray(array $array, $modelName) {

        $newArray = array();

        foreach($array as $element) {

            if(is_object($element))
               array_push($newArray, self::unmarshall($element, $modelName));

            elseif(is_array($element))
               array_push($newArray, self::unmarshallArray($element, $modelName));

            else array_push($newArray, $element);
        }

        return $newArray;
    }
}
?>