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
 * APC cache provider
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp.cache
 */
class ApcCacheProvider implements CacheProvider {

      /**
       * Creates a new instance of ApcCacheProvider
       *
       * @return void
       * @throws CacheException if APC is not installed on the server
       */
      public function __construct() {

             if(!function_exists('apc_store'))
                throw new CacheException('Alternative PHP Cache (APC) is not installed on the server');
      }

      /**
	   * (non-PHPdoc)
	   * @see src/cache/Caching#set($key, $value, $minutes)
	   */
      public function set($key, $value, $minutes = 0) {

             apc_store($key, serialize($value), $minutes);
      }

      /**
	   * (non-PHPdoc)
	   * @see src/cache/Caching#get($key)
	   */
      public function get($key) {

             return unserialize(apc_fetch($key));
      }

	  /**
	   * (non-PHPdoc)
	   * @see src/cache/Caching#get($key)
	   */
      public function delete($key) {

             return apc_delete($key);
      }

	  /**
	   * (non-PHPdoc)
	   * @see src/cache/Caching#exists($key)
	   */
      public function exists($key) {

             return apc_exists($key);
      }
}
?>