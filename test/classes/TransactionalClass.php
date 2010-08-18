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
 * @package com.makeabyte.agilephp.test.classes
 */

/**
 * A class used by the test package to test #@Transacational interceptions.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.classes
 */

#@Transactional
class TransactionalClass {

      public function doQueryThatResultsInException() {

             ORM::query('INSERT INTO roles(namesssssssss, description) VALUES("transactional-test", "Transactional test users");');
             ORM::query('INSERT INTO users(username, password, email, created, enabled) VALUES("transactional-test-account", "123", "root@localhost", "' . date('Y m d', strtotime('now')) . '", "1");');
      }

      public function doQueryThatCompletes() {

             ORM::query('INSERT INTO roles(name, description) VALUES("transactional-test2", "Transactional test users");');
             ORM::query('INSERT INTO users(username, password, email, created, enabled) VALUES("transactional-test-account2", "123", "root@localhost", "' . date('Y m d', strtotime('now')) . '", "1");');
      }
}
?>