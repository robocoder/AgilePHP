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

require_once 'cache/CacheProvider.php';

/**
 * Allows caching dynamic data that doesn't require real-time rendering.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * <code>
 * class MyClass {
 *
 * #@Cache
 * public function index() {
 *
 * 		  // A call that might perform some process intensive operation
 * 		  // or a database call thats only needed once (like building
 * 		  // a CMS that looks up form input types to build a contact page, etc,
 * 		  // and once the site is live they dont change)
 * }
 *
 * #@Cache(minutes = 5)
 * public function index() {
 *
 * 		  // A call that might perform some processing intensive action
 * 		  // or a database call thats not really needed for every page request
 * }
 * }
 * </code>
 */
#@Interceptor
class Cache {

	  /**
	   * Cache annotation argument containing the number of minutes to cache the intercepted content.
	   * Defaults to 0 (never expires).
	   *
	   * @var String The number of minutes to store the cached content
	   */
	  public $minutes = 0;

	  /**
	   * Boolean flag used to indicate whether or not output buffering should be used to capture HTML data.
	   *
	   * @var boolean True to cache data using output buffering, false to cache method return value. Default
	   *              is to capture a method return value.
	   */
	  public $html = false;

	  /**
	   * Creates a new Cache instance and initalizes object state
	   *
	   * @return void
	   */
	  public function __construct() {

	         $xml = AgilePHP::getConfiguration();
	         $provider = (isset($xml->caching)) ? (string)$xml->caching->attributes()->provider : 'FileCacheProvider';
	         $this->provider = new $provider();
	  }

	  /**
	   * Performs decisions on whether to return cached content or carry out
	   * real time processing invocation.
	   *
	   * @param InvocationContext $ic The invocation context instance responsible for the interception
	   * @return void
	   */
	  #@AroundInvoke
	  public function process(InvocationContext $ic) {

	  		 $key = md5(serialize($ic));

	  		 // Data not cached; process real-time, cache for next request, and return the data
	  		 if(!$this->provider->get($key))
	  		    return ($this->html) ? $this->cacheAndServeHtml($ic) : $this->cacheAndServe($ic);

	  		 // Return cached data
	  		 if($this->html)
	  		    echo $this->provider->get($key);
	  		 else
	  		    return $this->provider->get($key);
	  }

	  /**
	   * Executes the intercepted call, caches the return value and returns the result.
	   *
	   * @param InvocationContext $ic The intercepted InvocationContext
	   * @return void
	   */
	  private function cacheAndServe(InvocationContext $ic) {

	          $key = md5(serialize($ic));

        	  $clsName = get_class($ic->getTarget());
        	  $o = new $clsName();

        	  $class = new ReflectionClass($o);
        	  $m = $class->getMethod($ic->getMethod());
        	  $data = $ic->getParameters() ? $m->invokeArgs($o, $ic->getParameters()) : $m->invoke($o);

        	  $this->provider->set($key, $data, $this->minutes);

			  return $data;
	  }

	  /**
	   * Executes the intercepted call using HTML buffering, caches the output, and flushes the buffer.
	   *
	   * @param InvocationContext $ic The intercepted InvocationContext
	   * @return void
	   */
	  private function cacheAndServeHtml(InvocationContext $ic) {

	          $key = md5(serialize($ic));

	          ob_start();

        	  $clsName = get_class($ic->getTarget());
	          $o = new $clsName();

        	  $class = new ReflectionClass($o);
        	  $m = $class->getMethod($ic->getMethod());
        	  $data = $ic->getParameters() ? $m->invokeArgs($o, $ic->getParameters()) : $m->invoke($o);

        	  $data = ob_get_flush();
        	  $this->provider->set($key, $data, $this->minutes);

        	  return $data;
	  }
}
?>