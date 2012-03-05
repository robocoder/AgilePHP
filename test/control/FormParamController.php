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
 * @package com.makeabyte.agilephp.test.control
 */

/**
 * Tests the #@FormParam interceptor (sets the annotated property with the
 * corresponding form field value).
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.control
 */
class FormParamController extends BaseController {

    #@FormParam(required = true, displayName = 'First Name', validator = 'AgilePHPNameValidator')
    public $name;

    #@FormParam(name = 'email', validator = 'EmailWithDnsCheckValidator')
    public $email;

    #@FormParam(name = 'comments', sanitize = false)
    public $comments;

    #@Logger
    public $logger;


    /**
     * (non-PHPdoc)
     * @see src/mvc/BaseController#index()
     */
    public function index() {
        $this->render('form-param-example');
    }

    /**
     * Displays the submitted form values set by the #@FormParam interceptor
     *
     * @return void
     */
    public function process() {

        echo '<hr>';
        echo 'First name: ' . $this->name . '<br>';
        echo 'Email: ' . $this->email . '<br>';
        echo 'Comments: ' . $this->comments . '<br>';

        $this->logger->debug('RequestParamController::process Name = ' . $this->name . ', email = ' . $this->email . ', comments: ' . $this->comments);
    }
}
?>