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
 * Internationalization using the GNU gettext library.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 */
class i18n {

	  private static $instance;

	  private $locale;
	  private $domain = 'messages';
	  
	  /**
	   * Initalizes the language/locale based on HTTP langauge header
	   * 
	   * @return void
	   */
	  private function __construct() {

	  		  if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {

	  		  	  $httpLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
	  		 	  $pieces = explode(';', $httpLang);
	  		 	  $dirty = (count($pieces) > 0) ? substr($pieces[0], 0, 5) : null;
	  		 	  $locale = str_replace('-', '_', $dirty);

	  		 	  if(file_exists(AgilePHP::getWebRoot() . 
	  		 	  			DIRECTORY_SEPARATOR . 'locale' . $this->getLocale())) {

	  		 	  	  $this->setLocale($locale);
	  		 	  	  $this->setDomain($this->domain);
	  		 	  }
	  		  }
	  }

	  /**
	   * Returns a singleton instance of i18n
	   * 
	   * @return i18n Singleton instance of i18n
	   * @static
	   */
	  public static function getInstance() {

	  		 if(self::$instance == null)
	  		 	 self::$instance = new self;

	  		 return self::$instance;
	  }

	  /**
	   * Sets the messaging domain. This is the name of your .PO/.MO files.
	   * 
	   * @param String $domain The messaging domain. Defaults to 'messages'.
	   * @return void
	   */
	  public function setDomain($domain) {

	  		 $this->domain = $domain;

	  		 bindtextdomain($domain, AgilePHP::getWebRoot() . DIRECTORY_SEPARATOR . 'locale');
			 textdomain($domain);
	  }

	  /**
	   * Returns the messaging domain.
	   * 
	   * @return String The messaging domain
	   */
	  public function getDomain() {

	  		 return $this->domain;
	  }

	  /**
	   * Sets the language locale.
	   * 
	   * @param $locale The two letter_TWO LETTER language local. (ie. en_US, en_ES, ...)
	   * @return void
	   */
	  public function setLocale($locale) {

	  		 $this->locale = $locale;
	  		 setlocale(LC_ALL, $locale);
	  }

	  /**
	   * Returns the language locale being used to translate
	   * 
	   * @return string The language locale
	   */
	  public function getLocale() {

	  		 return $this->locale;
	  }

	  /**
	   * Performs language translation based on the configured locale.
	   * 
	   * @param String $text The text to translate
	   * @return string The translated text
	   * @static
	   */
	  public static function translate($text) {

	  		 return _($text);
	  }
}
?>