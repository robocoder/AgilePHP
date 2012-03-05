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
 * @package com.makeabyte.agilephp.util
 */

/**
 * Utility / helper class to assist in working with objects
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp.util
 */
class ClassUtils {

    /**
     * Creates an accessor method from the $property parameter. The $property
     * will be returned with the prefix 'get' and the first letter of the property
     * uppercased.
     *
     * @param string $property The name of the property to convert to an accessor method name
     * @return string The accessor string
     */
    public static function toAccessor($property) {
        return 'get' . ucfirst($property);
    }

    /**
     * Creates a mutator method from the $property parameter. The $property
     * will be returned with the prefix 'set' and the first letter of the property
     * uppercased.
     *
     * @param string $property The name of the property to convert to a mutator method name
     * @return string The mutator string
     */
    public static function toMutator($property) {
        return 'set' . ucfirst($property);
    }

    /**
     * Checks the object for the presence of property values.
     *
     * @param Object $o The object to inspect
     * @return boolean True if the class is empty, false if any property contain values.
     */
    public static function isEmpty($o) {

        $class = new ReflectionClass($o);
        $properties = $class->getProperties();
        foreach($properties as $property) {

            if($property->name == 'interceptedTarget') continue;
            $accessor = self::toAccessor($property->name);
            if($o->$accessor()) return false;
        }

        return true;
    }

    /**
     * Compares object $a with $b.
     *
     * NOTE: This function assumes the object adheres to bean-style convention.
     *
     * @param object $a The first object
     * @param object $b The second object
     * @return boolean True if the objects test positive, false if the objects do not match
     */
    public static function compare($a, $b) {

        try {

            $classA = new ReflectionClass($a);
            $classB = new ReflectionClass($b);

            if($classA->getName() !== $classB->getName())
            throw new Exception('object class names dont match');

            $propsA = $classA->getProperties();
            $propsB = $classB->getProperties();

            if(!count($propsA) || !count($propsB))
            throw new Exception('object property count doesnt match');

            for($i=0; $i<count($propsA); $i++) {

                if($propsA[$i]->name !== $propsB[$i]->name)
                throw new Exception('object property names dont match');

                $accessor = 'get' . ucfirst($propsA[$i]->name);
                if($a->$accessor() !== $b->$accessor())
                throw new Exception('object property values dont match');
            }
        }
        catch(Exception $e) {

            Log::debug('ClassUtils::compare ' . $e->getMessage());
            return false;
        }

        return true;
    }
    
    /**
     * Copies the values from object $a to $b.
     *
     * @param DomainModel $a The first object
     * @param DomainModel $b The second object
     * @return DomainModel The same instance of object $b with its properties set as defined in object $a
     */
    public static function copy(DomainModel $a, DomainModel $b) {

        $classA = new ReflectionClass($a);
        $classB = new ReflectionClass($b);

        if($classA->getName() !== $classB->getName())
        throw new Exception('object class names dont match');

        $propsA = $classA->getProperties();
        $propsB = $classB->getProperties();

        if(!count($propsA) || !count($propsB))
        throw new Exception('object property count doesnt match');

        for($i=0; $i<count($propsA); $i++) {

            if($propsA[$i]->name !== $propsB[$i]->name)
            throw new Exception('object property names dont match');

            $accessor = 'get' . ucfirst($propsA[$i]->name);
            $mutator = 'set' . ucfirst($propsB[$i]->name);
            $b->$mutator($a->$accessor());
        }

        return $b;
    }
}
?>