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
 * Mailer attachment model
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.mailer
 */
class MailerAttachment {

    private $file;
    private $name;
    private $contentType;

    /**
     * Initialzes the email attachment
     *
     * @param String $file The location on the file system relative to the web root where the attachment is located
     * @param String $name The attachment name displayed to the recipient
     * @param String $contentType A valid MIME content-type
     * @return void
     */
    public function __construct($file = null, $name = null, $contentType = 'application/octet-stream') {
        $this->file = $file;
        $this->name = $name;
        $this->contentType = $contentType;
    }

    /**
     * Sets the location on disk where the attachment is located
     *
     * @param String $file The location on disk relative to the web root where the attachment lives
     * @return void
     */
    public function setFile($file) {
        $this->file = $file;
    }

    /**
     * Gets the location on disk where the attachment is located
     * 
     * @return The location on disk relative to the web root where the attachment lives.
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * The display name of the attachment which the recipient sees
     *
     * @param String $name The attachment name
     * @return void
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Gets the attachment display name
     * 
     * @return The attachment name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the MIME content type of the attachment
     * 
     * @param String $type The content type
     * @return void
     */
    public function setContentType($type) {
        $this->contentType = $type;
    }

    /**
     * Gets the MIME content type of the attachment
     * 
     * @return The MIME content type
     */
    public function getContentType() {
        return $this->contentType;
    }
}
?>