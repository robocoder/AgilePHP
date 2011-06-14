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
 * @package com.makeabyte.agilephp.data
 */

/**
 * Abstract model that provides data rendering to extension classes.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.data
 */
abstract class DataModel extends BaseModel {

    /**
     * Returns a XML representation of the extension model
     *
     * @return string The XML representation of the extension class
     */
    public function toXml() {

        $namespace = explode('\\', get_class($this));
        $class = array_pop($namespace);

        return XmlRenderer::render($this, $class, $class . 's');
    }

    /**
     * Returns a JSON representation of the extension model
     *
     * @return string The JSON representation of the extension class
     */
    public function toJson() {

        return JsonRenderer::render($this);
    }

    /**
     * Returns a YAML representation of the extension model
     *
     * @return string The YAML representation of the extension class
     */
    public function toYaml() {

        return YamlRenderer::render($this);
    }
}
?>