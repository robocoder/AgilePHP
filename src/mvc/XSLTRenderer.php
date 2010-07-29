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
 * @package com.makeabyte.agilephp.mvc
 */

/**
 * Provides base implementation for XSLT transformations
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.mvc
 */
class XSLTRenderer extends BaseRenderer {

      /**
	   * Renders a view by performing an XSLT transformation using string literal
	   * XSL and XML values.
	   *
	   * @param string $xsl XSL document
	   * @param string $xml XML string used to apply data to the XSL template
	   * @param boolean $declaration Optional flag used to specify whether or not an xml doctype
	   *                declaration should be included in the transformation. Defaults to false (no declaration).
	   * @return void
       */
      public function render($xsl, $xml = '', $declaration = false) {

      	 	 $dom = new DomDocument();
			 $dom->loadXML($xsl);

			 $xp = new XSLTProcessor();
			 $xsl = $xp->importStylesheet($dom);

			 $doc = new DomDocument();
			 $doc->loadXML($xml);

			 $xslt = $xp->transformToXml($doc);

			 if(!$declaration) $xslt = preg_replace('/<\?xml.*\?>/', '', $xslt);

			 print $xslt;
	  }

	  /**
	   * Renders a view by performing an XSLT transformation. The XSL document
	   * is read in from the web application view directory.
	   *
	   * @param string $xsl The name of the XSL document located in the web app view directory
	   * @param string $xml XML string used to apply data to the XSL template
	   * @return void
       */
      public function renderXsl($xsl, $xml='') {

      	 	 $dom = new DomDocument();
			 $dom->load(AgilePHP::getWebRoot() . '/view/' . $xsl . '.xsl');

			 $xp = new XSLTProcessor();
			 $xsl = $xp->importStylesheet($dom);

			 $doc = new DomDocument();
			 $doc->loadXML($xml);

			 $xslt = $xp->transformToXml($doc);

			 print $xslt;
	  }

	  /**
	   * Renders the specified XML document with a <xsl:stylesheet> element which
	   * uses the specified $xsl parameter in its href attribute. This transformation
	   * is peformed by the client browser rather than PHP.
	   *
	   * @param string $xsl A valid href attribute location pointing to an XSL document that the
	   * 				    client will use to transform the XML data into HTML.
	   * @param string $xml XML string used to apply data to the XSL template
	   * @return void
       */
      public function clientTransform($xsl, $xml='') {

      	     $out = '<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">';
      		 $out = '<?xml version="1.0" encoding="ISO-8859-1"?>' . PHP_EOL;
      		 $out .= '<?xml-stylesheet type="text/xsl" href="' . $xsl . '"?>' . PHP_EOL;
      		 $out .= $xml;

      		 header('content-type: text/xml');
      		 print $out;
	  }

	  /**
	   * Performs an XSLT transformation and returns the rendered HTML using the specified
	   * XSL and XML string values.
	   *
	   * @param String $xsl XSL string template used to create HTML
	   * @param String $xml XML string used to apply data to the XSL template
	   * @param boolean $declaration Optional flag used to specify whether or not an xml doctype
	   *                declaration should be included in the transformation. Defaults to false (no declaration).
	   *
	   * @return The rendered HTML from the XSLT transformation
       */
      public function transform($xsl, $xml = '', $declaration = false) {

    	     set_error_handler('XSLTRenderer::ErrorHandler');

      	 	 $dom = new DomDocument();
	 		 $dom->loadXML($xsl);

			 $xp = new XSLTProcessor();
			 $xsl = $xp->importStylesheet($dom);

			 $doc = new DomDocument();
			 try {
			 		$doc->loadXML($xml);
			 }
			 catch(FrameworkException $e) {

			 	   $doc->loadXML(addslashes($xml));
			 }

			 $xslt = $xp->transformToXml($doc);

			 restore_error_handler();

			 if(!$declaration) $xslt = preg_replace('/<\?xml.*\?>/', '', $xslt);

			 return $xslt;
	  }

	  /**
	   * Performs an XSLT transformation and returns the rendered HTML using the specified
	   * XSL view and XML string values.
	   *
	   * @param String $xsl XSL view name as it lives in the web app view directory
	   * @param String $xml  XML string used to apply data to the XSL template
	   * @return string The transformed HTML
       */
      public function transformXsl($xsl, $xml='') {

             set_error_handler('XSLTRenderer::ErrorHandler');

      	 	 $dom = new DomDocument();
			 $dom->load(AgilePHP::getWebRoot() . '/view/' . $xsl . '.xsl');

			 $xp = new XSLTProcessor();
			 $xsl = $xp->importStylesheet($dom);

			 $doc = new DomDocument();
             try {
			 		$doc->loadXML($xml);
			 }
			 catch(FrameworkException $e) {

			 	   $doc->loadXML(preg_replace('/\0/', '', $xml)); // serialized objects contain C \0 line terminators
			 }

			 $xslt = $xp->transformToXml($doc);

			 restore_error_handler();

			 return $xslt;
	  }

	  /**
	   * loadXml reports an error instead of throwing an exception when the xml is not well formed. This
	   * is a custom PHP error handling function which throws an FrameworkException instead of reporting
	   * a PHP error.
	   *
	   * @param Integer $errno Error number
	   * @param String $errmsg Error message
	   * @param String $errfile The name of the file that caused the error
	   * @param Integer $errline The line number that caused the error
	   * @return false
	   * @throws FrameworkException
	   */
 	  public static function ErrorHandler($errno, $errmsg, $errfile, $errline) {

 	  	     if($errno == E_WARNING && (substr_count($errmsg, 'DOMDocument::loadXML()') > 0))
	    	    throw new FrameworkException($errmsg);

	         return false;
	  }
}
?>