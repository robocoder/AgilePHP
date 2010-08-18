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
 * @package com.makeabyte.agilephp.cache
 */

/**
 * Contract for cache providers
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp.cache
 */
interface CacheProvider {

    /**
     * Defines a new cache variable and value
     *
     * @param string $key The variable name
     * @param mixed $value The variable value
     * @param int $minutes The number of minutes to keep data cached. Defaults to 0 (never expires)
     * @return void
     */
    public function set($key, $value, $minutes = 0);

    /**
     * Retrieves a cache variable
     *
     * @param string $key The variable name
     * @return mixed The variable value
     */
    public function get($key);

    /**
     * Deletes a cache variable
     *
     * @param string $key The variable name
     * @return void
     */
    public function delete($key);

    /**
     * Checks whether or not the specified variable name exists in the cache
     *
     * @param string $key The variable name
     * @return boolean True if the variable exists, false otherwise
     */
    public function exists($key);
}
?>