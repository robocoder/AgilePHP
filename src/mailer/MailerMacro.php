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
 * Represents a name/value pair which is later used to replace the specified
 * 'name' values within an email template with their corresponding 'value'.
 * For example, the macro {name} with a value of 'John Doe' would look something
 * like this:
 *
 * <code>
 * // Template value
 * $myTemplate = '<span id="greeting">Hello, {name}!</span>';
 * 
 * // Macro definition
 * $macro1 = new EmailMacro('name', 'John Doe');
 * 
 * // Rendered output
 * // <span id="greeting">Hello, John Doe!</span>
 * </code>
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.mailer
 */
class MailerMacro {

    private $name;
    private $value;

    /**
     * Initializes the MailerMacro instance
     *
     * @param String $name The macro name
     * @param String $value The macro value
     */
    public function __construct($name = null, $value = null) {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Sets the macro name
     * 
     * @param String $name The mail macro name
     * @return void
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Gets the macro name
     * 
     * @return The macro name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the macro value
     * 
     * @param String $value The macro value
     * @return void
     */
    public function setValue($value) {
        $this->value = $value;
    }

    /**
     * Gets the macro value
     * 
     * @return The macro value
     */
    public function getValue() {
        return $this->value;
    }
}
?>