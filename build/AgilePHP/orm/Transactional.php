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
 * @package com.makeabyte.agilephp.orm
 */

/**
 * Class and method level Interceptor responsible for transactional SQL persistence.
 * Class level annotations begin a transaction when the class is constructed and commit/rollback
 * the transaction when the class is destructed. Likewise, method level annotations begin a
 * transaction just before the method is invoked and commit/rollback the transaction when the
 * method has completed. If an exception is encountered, a ROLLBACK is automatically issued.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.orm
 */
#@Interceptor 
class Transactional {

      #@AroundInvoke
      public function beginTransaction(InvocationContext $ic) {

             ORMFactory::getDialect()->beginTransaction();
             return $ic->proceed();
      }

      #@AfterInvoke
      public function commit(InvocationContext $ic) {

             ORMFactory::getDialect()->commit();
             return $ic->proceed();
      }
}
?>