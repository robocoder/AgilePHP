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
	   * Renders a view by performing an XSLT transformation. The XSL document
	   * is passed in as a string.
	   * 
	   * @param String $xsl XSL document
	   * @param String $xml Optional XML document
	   * @return void
       */
      public function render( $xsl, $xml = '' ) {
 
      	 	 $dom = new DomDocument();
			 $dom->loadXML( $xsl );

			 $xp = new XSLTProcessor();
			 $xsl = $xp->importStylesheet( $dom );

			 $doc = new DomDocument();
			 $doc->loadXML( $xml );

			 $xslt = $xp->transformToXml( $doc );

			 print $xslt;
	  }

	  /**
	   * Renders a view by performing an XSLT transformation. The XSL document
	   * is read in from the specified xsl view.
	   * 
	   * @param String $xsl XSL document located in the web app view directory
	   * @param String $xml XML document supplying the XSL data
	   * @return void
       */
      public function renderXsl( $xsl, $xml='' ) {

      	 	 $dom = new DomDocument();
			 $dom->load( AgilePHP::getFramework()->getWebRoot() . '/view/' . $xsl . '.xsl' );

			 $xp = new XSLTProcessor();
			 $xsl = $xp->importStylesheet( $dom );

			 $doc = new DomDocument();
			 $doc->loadXML( $xml );

			 $xslt = $xp->transformToXml( $doc );

			 print $xslt;
	  }

	  /**
	   * Renders the specified XML document with a <xsl:stylesheet> element which
	   * uses the specified $xsl parameter in its href attribute.
	   * 
	   * @param String $xsl A valid href attribute location pointing to an XSL document that the
	   * 				    client will use to transform the XML data into HTML.
	   * @param @string $xml The XML data document
	   * @return void
       */
      public function clientTransform( $xsl, $xml='' ) {

      	     $out = '<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">';
      		 $out = '<?xml version="1.0" encoding="ISO-8859-1"?>' . PHP_EOL;
      		 $out .= '<?xml-stylesheet type="text/xsl" href="' . $xsl . '"?>' . PHP_EOL;
      		 $out .= $xml;

      		 header( 'content-type: text/xml' );
      		 print $out;
	  }

	  /**
	   * Performs an XSLT transformation and returns the rendered HTML.
	   * 
	   * @param String $xsl XSL document
	   * @param String $xml Optional XML document
	   * 
	   * @return The rendered HTML from the XSLT transformation
       */
      public function transform( $xsl, $xml = '' ) {

    	     set_error_handler( 'XSLTRenderer::ErrorHandler' );

      	 	 $dom = new DomDocument();
	 		 $dom->loadXML( $xsl );

			 $xp = new XSLTProcessor();
			 $xsl = $xp->importStylesheet( $dom );

			 $doc = new DomDocument();
			 try {
			 		$doc->loadXML( $xml );
			 }
			 catch( FrameworkException $e ) {
			 	
			 	    $doc->loadXML( addslashes( $xml ) );
			 }

			 $xslt = $xp->transformToXml( $doc );

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
 	  public static function ErrorHandler( $errno, $errmsg, $errfile, $errline ) {

 	  	     if( $errno == E_WARNING && (substr_count( $errmsg, "DOMDocument::loadXML()" ) > 0 ) )
	    	     throw new FrameworkException( $errmsg );

	         return false;
	  }
}
?>