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
 * @package com.makeabyte.agilephp.mailer
 */

/**
 * Represents an email template stored on the file system
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.mailer
 */
class MailerTemplate {

    private $content;
    private $macros = array();
    
    /**
     * Initialzes the email template
     * 
     * @param String $content The email template content
     * @param array $macros A list of MailerMacro instances to expand within the template
     * @return void
     */
    public function __construct($content = null, array $macros = array()) {
        $this->content = $content;
        $this->macros = $macros;
    }
    
    /**
     * The email template. This is the actual template content used as
     * the outgoing message body.
     *
     * @param String $content The template body
     * @return void
     */
    public function setContent($content) {
        $this->content = $content;
    }
    
    /**
     * Returns the template body content.
     * 
     * @return The email template content
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Sets the list of MailerMacro instances to expand within the template
     * 
     * @param array $macros The MailerMacro instances to expand within the template
     * @return void
     * @throws MailerException if any of the specified macros are not an instance of MailerMacro
     */
    public function setMacros(array $macros) {

        foreach($macros as $macro)
           if(!$macro instanceof MailerMacro)
              throw new MailerException('Macro must be an instance of MailerMacro');

        $this->macros = $macros;
    }

    /**
     * Returns the list of MailerMacro instances to expand within the template
     * 
     * @return An array of MailerMacro instances
     */
    public function getMacros() {
        return $this->macros;
    }

    /**
     * Pushes a MailerMacro instance onto the stack
     *
     * @param MailerMacro $macro The MailerMacro instance to add
     * @return void
     */
    public function addMacro(MailerMacro $macro) {
        array_push($this->macros, $macro);
    }

	/**
     * Loads the specified template path from the file system
     * 
     * @param String $filename Relative path from the web root where the template file exists.
     * @return void
     * @throws MailerException if the specified email template can not be located
     */
    public function load($filename) {

        $path = AgilePHP::getWebRoot() . DIRECTORY_SEPARATOR . $filename;
        if(!file_exists($path))
           throw new MailerException('Unable to locate the specified template (\'' . $path . '\').');

        $this->content = file_get_contents($path);
    }

    /**
     * Expands the macros inside of the template. This operation essentially performs
     * a global find/replace within the template, replacing MailerMacro::name with MailerMacro::value.
     * 
     * @return void
     */
    public function expandMacros() {

        foreach($this->macros as $macro)
           $this->content = str_replace('{' . $macro->getName() . '}', $macro->getValue(), $this->content);
    }
}
?>