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
 * File system cache provider
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp.cache
 */
class FileCacheProvider implements CacheProvider {

      private $cache;
      private $CachedFile;

      /**
       * Creates a new FileSystemCache instance
       *
       * @return void
       * @throws CacheException if the .cache directory does not exist
       *         and can not be created.
       */
      public function __construct() {

             $this->cache = AgilePHP::getWebRoot() . DIRECTORY_SEPARATOR . '.cache';
             if(!file_exists($this->cache)) {

                if(!mkdir($this->cache))
                   throw new CacheException('Failed to create cache directory at \'' . $this->cache . '\'.');

                chmod($this->cache, 0777);
             }
      }

      /**
	   * (non-PHPdoc)
	   * @see src/cache/Caching#set($key, $value, $minutes)
	   */
      public function set($key, $value, $minutes = 0) {

             $CachedFile = new CachedFile($value, $minutes);

             $h = fopen($this->cache . DIRECTORY_SEPARATOR . $key, 'w');
             fwrite($h, serialize($CachedFile));
             fclose($h);
      }

      /**
	   * (non-PHPdoc)
	   * @see src/cache/Caching#get($key)
	   */
      public function get($key) {

             $file = $this->cache . DIRECTORY_SEPARATOR . $key;
             if(!file_exists($file)) return false;

             $CachedFile = unserialize(file_get_contents($file));

             if($minutes = $CachedFile->getMinutes()) {

                $minutes = $minutes * 60;

                if(time() - $minutes < filemtime($file))
                   return $CachedFile->getData();
             }

             return $CachedFile->getData();
      }

	  /**
	   * (non-PHPdoc)
	   * @see src/cache/Caching#get($key)
	   */
      public function delete($key) {

             unlink($this->cache . DIRECTORY_SEPARATOR . $key);
      }

	  /**
	   * (non-PHPdoc)
	   * @see src/cache/Caching#exists($key)
	   */
      public function exists($key) {

             return file_exists($this->cache . DIRECTORY_SEPARATOR . $key);
      }
}

/**
 * Represents a cached file. Stores the cached data and its expiration value.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp.cache
 */
class CachedFile {

      private $data;
      private $minutes;

      /**
       * Creates a new CachedFile instance
       *
       * @param mixed $data The data to cache
       * @param int $minutes The number of minutes the cached data is considered valid
       * @return void
       */
      public function __construct($data, $minutes = 0) {

             $this->data = $data;
             $this->minutes = $minutes;
      }

      /**
       * Sets the data to be cached
       *
       * @param mixed $data The data to cache
       * @return void
       */
      public function setData($data) {

             $this->data = $data;
      }

      /**
       * Returns the cached data
       *
       * @return mixed The cached data
       * @return void
       */
      public function getData() {

             return $this->data;
      }

      /**
       * Sets the number of minutes before considering the cached data stale
       *
       * @param int $expires The number of minutes to keep data cached
       * @return void
       */
      public function setMinutes($minutes) {

             $this->minutes = $minutes;
      }

      /**
       * Retrieves the number of minutes before considering the cached data stale
       *
       * @return int The number of minutes to keep data cached
       */
      public function getMinutes() {

             return $this->minutes;
      }
}
?>