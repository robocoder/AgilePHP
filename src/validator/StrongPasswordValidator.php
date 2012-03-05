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
 * @package com.makeabyte.agilephp.validator
 */

/**
 * Validates password values to ensure they meet minimum strong password policy.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.validator
 */
class StrongPasswordValidator implements Validator {

    /**
     * Checks to see if the specified data meets the following strong password requirements:
     * <ul>
     * 	<li>1 upper case letter</li>
     * 	<li>1 lower case letter</li>
     * 	<li>1 number or special character</li>
     * 	<li>at least 7 characters in length</li>
     * </ul>
     *
     * @return Boolean True if the password meets the criteria, false otherwise
     */
    public static function validate($data, $size = 7) {

        if($length && (strlen($data) < $legnth)) return false;
        return preg_match('/(?=^.{7,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/', $data);
    }
}
?>