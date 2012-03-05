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
 * Windows cache provider
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp.cache
 */
class WinCacheProvider implements CacheProvider {

    /**
     * Creates a new instance of WindowsCacheProvider
     *
     * @return void
     * @throws CacheException if Windows Cache extension is not installed on the server
     */
    public function __construct() {

        if(!function_exists('wincache_ucache_get'))
        throw new CacheException('WinCache extension is not installed on the server');
    }

    /**
     * (non-PHPdoc)
     * @see src/cache/Caching#set($key, $value, $minutes)
     */
    public function set($key, $value, $minutes = 0) {

        return wincache_ucache_set($key, serialize($value), $minutes);
    }

    /**
     * (non-PHPdoc)
     * @see src/cache/Caching#get($key)
     */
    public function get($key) {

        return unserialize(wincache_ucache_get($key));
    }

    /**
     * (non-PHPdoc)
     * @see src/cache/Caching#get($key)
     */
    public function delete($key) {

        return wincache_ucache_delete($key);
    }

    /**
     * (non-PHPdoc)
     * @see src/cache/Caching#exists($key)
     */
    public function exists($key) {

        return wincache_ucache_exists($key);
    }
}
?>