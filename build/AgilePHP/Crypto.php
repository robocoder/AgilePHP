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
 * Provides one way hashing and encryption
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp
 */
class Crypto {

	  private static $instance;
	  private $algorithm;
	  private $iv;
	  private $key;

	  /**
	   * Initalizes the Crypto component with the hashing algorithm defined in agilephp.xml
	   * 
	   * @return void
	   */
	  public function __construct() {

	  		 $agilephp_xml = AgilePHP::getWebRoot() . DIRECTORY_SEPARATOR . 'agilephp.xml';
	  		 $xml = simplexml_load_file($agilephp_xml);

	  	     if($xml->crypto) {

	  		  	 $this->setAlgorithm((string)$xml->crypto->attributes()->algorithm);

	  		  	 if($xml->crypto->attributes()->iv)
	  		  	 	 $this->iv = (string)$xml->crypto->attributes()->iv;
	  		  	 	 
	  		  	 if($xml->crypto->attributes()->key)
	  		  	 	 $this->key = (string)$xml->crypto->attributes()->key;
	  	     }
	  }

	  /**
	   * Returns a singleton instance of Crypto
	   * 
	   * @return Singleton instance of Crypto
	   * @static
	   */
	  public static function getInstance() {

	  	     if(self::$instance == null)
	  	         self::$instance = new self;

	  	     return self::$instance;
	  }

	  /**
	   * Sets the algorithm for the Crypto component for use with getDigest.
	   * 
	   * @param String $algorithm The algorithm to perform the hashing operation with.
	   * 					      NOTE: getSupportedHashAlgorithms() will return a list of
	   * 						        algorithms available on the server.
	   * @return void
	   * @throws FrameworkException If passed a hashing name not available in
	   * 							getSupportedHashAlgorithms().
	   */
	  public function setAlgorithm($algorithm) {

	  		 if(in_array($algorithm, $this->getSupportedHashAlgorithms()))
	  		 	 $this->algorithm = $algorithm;

	  		 if(!$this->algorithm)
	  		 	 throw new FrameworkException('Unsupported hashing algorithm \'' . $algorithm . '\'.');
	  }

	  /**
	   * Returns the algorithm the Crypto component is configured to perform
	   * a hashing operation with using the 'getDigest()' method.
	   * 
	   * @return The name of the hashing algorithm
	   */
	  public function getAlgorithm() {

	  		 return $this->algorithm;
	  }

	  /**
	   * Returns the iv as configured in agilephp.xml for the crypto component if one was defined.
	   * NOTE: The iv must be base64 encoded
	   * 
	   * @return String The base64 decoded iv configured in agilephp.xml
	   */
	  public function getIV() {

	  		 return base64_decode($this->iv);
	  }

	  /**
	   * Returns the key as configured in agilephp.xml for the crypto component if one was defined.
	   * 
	   * @return String The iv configured in agilephp.xml for the crypto component
	   */
	  public function getKey() {

	  		 return $this->key;
	  }

	  /**
	   * Returns the hashed $data. This operation requires either a valid configuration
	   * in agilephp.xml for the Crypto component or you must manually set the algorithm
	   * with a call to 'setAlgorithm()'.
	   * 
	   * @param mixed $data The data to hash
	   * @return String The hashed string
	   */
	  public function getDigest($data) {

	  		 return $this->hash($this->getAlgorithm(), $data);
	  }

	  /**
	   * Returns a hashed MD5 string.
	   * 
	   * @param mixed $data The data to hash
	   * @return String The hashed MD5 string
	   */
	  public function md5($data) {

	  		 return md5($data);
	  }

	  /**
	   * Returns an SHA1 hashed string.
	   * 
	   * @param $data The data to hash
	   * @return String The hashed SHA1 string
	   */
	  public function sha1($data) {

	  		 return hash('sha1', $data);
	  }

	  /**
	   * Returns an SHA256 hashed string.
	   * 
	   * @param mixed $data The data to hash
	   * @return String The hashed SHA256 string
	   */
	  public function sha256($data) {

	  		 return hash('sha256', $data);
	  }

	  /**
	   * Returns an SHA384 hashed string.
	   * 
	   * @param mixed $data The data to hash
	   * @return String The hashed SHA384 string
	   */
	  public function sha384($data) {

	  		 return hash('sha384', $data);
	  }

	  /**
	   * Returns an SHA512 hashed string.
	   * 
	   * @param $data The data to hash
	   * @return String The hashed SHA512 string
	   */
	  public function sha512($data) {

	  		 return hash('sha512', $data);
	  }

	  /**
	   * Returns an CRC32 hashed string.
	   * 
	   * @param mixed $data The data to hash
	   * @return String The hashed CRC32 string
	   */
	  public function crc32($data) {

	  		 return hash('crc32', $data);
	  }

	  /**
	   * Returns the hashed $data parameter according to the defined $algorithm
	   * parameter.
	   * String
	   * @param String $algorithm The algorithm to hash the defined data with. NOTE: You can get
	   * 						  a list of supported algorithms on the server with a call to
	   * 	    				  getSupportedHashAlgorithms().
	   * @param mixed $data The data to hash
	   * @return String The hashed SHA1 string
	   */
	  public function hash($algorithm, $data) {

	  		 return hash($algorithm, $data);
	  }

	  /**
	   * Returns an array of supported hashing algorithms on the current
	   * PHP enabled web server.
	   * 
	   * @return array An array of supported hashing algorithms available to PHP.
	   */
	  public function getSupportedHashAlgorithms() {

	  		 return hash_algos();
	  }

	  /* Cryptography */

	  /**
	   * Creates an IV suitable for the specified cipher using the MCRYPT_MODE_CBC module.
	   * NOTE: You can use this method to create an IV for use in agilephp.xml for the Crypto
	   * 	   component to use. Simply base64_encode the return value and place it in the crypto
	   * 	   components iv attribute in agilephp.xml.
	   * 
	   * @param CONST $cipher The cipher to use. This depends on the encryption algorithm you are using:
	   * 					  encrypt/decrypt_3des     = MCRYPT_TripleDES
	   * 					  encrypt/decrypt_blowfish = MCRYPT_BLOWFISH
	   * 					  encrypt/decrypt_aes256   = MCRYPT_RIJNDAEL_256
	   * 					  Defaults to MCRYPT_TripleDES
	   *  
	   * @return String The initial value (iv) as created by mcrypt_create_iv.
	   */
	  public function createIV($cipher = MCRYPT_TripleDES) {

	  		 return mcrypt_create_iv(mcrypt_get_block_size($cipher, MCRYPT_MODE_CBC), MCRYPT_DEV_RANDOM);
	  }

	  /**
	   * Encrypts the specified data using Triple DES.
	   * 
	   * @param String $iv The IV/salt
	   * @param String $key The secret key used to encrypt the data
	   * @param mixed $data The data to encrypt
	   * @return byte[] Triple DES encrypted string
	   */
	  public function encrypt_3des($iv, $key, $data) {

	  		 $size = mcrypt_get_key_size(MCRYPT_TripleDES, MCRYPT_MODE_CBC);
	  		 if(strlen($key) > $size)
	  		 	 $key = substr($key, 0, $size);

	  		 return mcrypt_cbc(MCRYPT_TripleDES, $key, $data, MCRYPT_ENCRYPT, $iv);
	  }

	  /**
	   * Decrypts Triple DES data 
	   * @param String $iv The IV/salt
	   * @param String $key The secret key used to encrypt the data
	   * @param mixed $data The data to encrypt
	   * @return byte[] Plain text, decrypted data if a proper key was supplied
	   */
	  public function decrypt_3des($iv, $key, $data) {

	  		 $size = mcrypt_get_key_size(MCRYPT_TripleDES, MCRYPT_MODE_CBC);
	  		 if(strlen($key) > $size)
	  		 	 $key = substr($key, 0, $size);

	  		 return trim(mcrypt_cbc(MCRYPT_TripleDES, $key, $data, MCRYPT_DECRYPT, $iv));
	  }

	  /**
	   * Encrypts the specified data using Blowfish
	   * 
	   * @param String $iv The IV/salt
	   * @param String $key The secret key used to encrypt the data
	   * @param mixed $data The data to encrypt
	   * @return byte[] Blowfish encrypted string
	   */
	  public function encrypt_blowfish($iv, $key, $data) {

	  		 $size = mcrypt_get_key_size(MCRYPT_BLOWFISH, MCRYPT_MODE_CBC);
	  		 if(strlen($key) > $size)
	  		 	 $key = substr($key, 0, $size);

	  		 return mcrypt_cbc(MCRYPT_BLOWFISH, $key, $data, MCRYPT_ENCRYPT, $iv);
	  }

	  /**
	   * Decrypts a string previously encrypted with encrypt_blowfish
	   * 
	   * @param String $iv The IV/salt
	   * @param String $key The secret key used to encrypt the data
	   * @param mixed $data The data to encrypt
	   * @return byte[] Plain text, decrypted data if a proper key was supplied
	   */
	  public function decrypt_blowfish($iv, $key, $data) {

	  		 $size = mcrypt_get_key_size(MCRYPT_BLOWFISH, MCRYPT_MODE_CBC);
	  		 if(strlen($key) > $size)
	  		 	 $key = substr($key, 0, $size);

	  		 return trim(mcrypt_cbc(MCRYPT_BLOWFISH, $key, $data, MCRYPT_DECRYPT, $iv));
	  }

	  /**
	   * Encrypts the specified data using AES 256 encryption
	   * 
	   * @param String $key The secret key used to encrypt the data
	   * @param mixed $data The data to encrypt
	   * @return byte[] AES 256 encrypted data
	   */
	  public function encrypt_aes256($iv, $key, $data) {

	  		 $size = mcrypt_get_key_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
	  		 if(strlen($key) > $size)
	  		 	 $key = substr($key, 0, $size);

	  		 return mcrypt_cbc(MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_ENCRYPT, $iv);
	  }

	  /**
	   * Decrypts the specified data which was previously encrypted using AES 256
	   * 
	   * @param String $key The secret key used to encrypt the data
	   * @param mixed $data The data to encrypt
	   * @return byte[] AES 256 decrypted data if a proper key was supplied
	   */
	  public function decrypt_aes256($iv, $key, $data) {

	  		 $size = mcrypt_get_key_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
	  		 if(strlen($key) > $size)
	  		 	 $key = substr($key, 0, $size);

	  		 return trim(mcrypt_cbc(MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_DECRYPT, $iv));
	  }
}
?>