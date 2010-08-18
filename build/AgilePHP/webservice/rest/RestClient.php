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
 * @package com.makeabyte.agilephp.webservice.rest
 */

/**
 * Responsible for requesting resources from a remote REST service.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.webservice.rest
 */
class RestClient {

	  private $curl;
	  private $resource;
	  private $headers = array();
	  private $responseCode;

	  /**
	   * Constructs a new RestClient instance.
	   * 
	   * @param string $endpoint A URL pointing to a REST service resource.
	   * @param string $useragent Optional User-Agent header value to include in the request
	   * @return void
	   */
	  public function __construct($endpoint, $useragent = null) {

	  		 $this->curl = curl_init($endpoint);
	  		 curl_setopt($this->curl, CURLOPT_USERAGENT, (($useragent) ? $useragent : 'AgilePHP RestClient'));
			 curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
			 curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
			 curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
	  }

	  /**
	   * Sets optional HTTP headers to include in the REST service request.
	   * 
	   * @param array $headers An array of HTTP headers to include in the rest.
	   * @return void
	   */
	  public function setHeaders(array $headers) {

	  		 $this->headers = $headers;
	  }
	  
	  /**
	   * Returns the HTTP resonse status code returned by the REST service.
	   * 
	   * @return int The HTTP response status code returned by the REST service
	   */
	  public function getResponseCode() {

	  		 return $this->responseCode;
	  }
	  
	  /**
	   * Sets optional HTTP basic authentication credentials if required by the REST service.
	   * 
	   * @param string $username The username to supply to the REST service.
	   * @param string $password The password used to authenticate the specified user.
	   * @return void
	   */
	  public function authenticate($username, $password) {

	  		 curl_setopt($this->curl, CURLOPT_USERPWD, "$username:$password");
	  }

	  /**
	   * Performs an HTTP GET to a REST service resource.
	   * 
	   * @return mixed The response from the REST service call.
	   */
	  public function get() {

	  		 curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);

	  		 $response = curl_exec($this->curl);

	  		 $this->responseCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
	  		 if($this->responseCode > 206) throw new RestClientException($this->responseCode);

	  		 return $response;
	  }

	  /**
	   * Performs an HTTP POST to a REST service resource.
	   * 
	   * @param string $data The data to supply as the request body
	   * @return mixed The response from the REST service call.
	   */
	  public function post($data) {

	  		 curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
	  		 curl_setopt($this->curl, CURLOPT_POST, true);
	  		 curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);

	  		 $response = curl_exec($this->curl);

	  		 $this->responseCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
	  		 if($this->responseCode > 206) throw new RestClientException($this->responseCode);

	  		 return $response;
	  }

	  /**
	   * Performs an HTTP PUT to a REST service resource.
	   * 
	   * @param string $data The data to supply as the request body.
	   * @return mixed The response from the REST service call.
	   */
	  public function put($data) {

	  		 curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PUT');
	  		 curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
			 curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);

			 $response = curl_exec($this->curl);

	  		 $this->responseCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
	  		 if($this->responseCode > 206) throw new RestClientException($this->responseCode);

	  		 return $response;
	  }

	  /**
	   * Performs an HTTP DELETE to a REST service resource.
	   * 
	   * @return mixed The response from the REST service call.
	   */
	  public function delete() {

	  		 //curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
	  		 curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE');

	  		 $response = curl_exec($this->curl);

	  		 $this->responseCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
	  		 if($this->responseCode > 206) throw new RestClientException($this->responseCode);

	  		 return $response;
	  }

	  /**
	   * Cleanup
	   * 
	   * @return void
	   */
	  public function __destruct() {

	  		 curl_close($this->curl);
	  }
}
?>