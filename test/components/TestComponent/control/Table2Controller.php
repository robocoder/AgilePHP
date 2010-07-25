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
 * @package com.makeabyte.agilephp.test.component.TestComponent.control
 */
namespace TestComponent\control;

/**
 * Table2 controller in the TestComponent controller namespace
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.component.TestComponent.control
 */
class Table2Controller extends \ComponentModelActionController {

      private $model;
      private $componentName = 'TestComponent';

      public function __construct() { 

             $this->model = new \TestComponent\model\Table2();
             parent::__construct();
      }

      /**
       * (non-PHPdoc)
       * @see AgilePHP/mvc/BaseModelController#getModel()
       */
      public function getModel() { 

             return $this->model;
      }

      /**
       * (non-PHPdoc)
       * @see AgilePHP/mvc/ComponentModelActionController#getComponentName()
       */
      protected function getComponentName() {

                return $this->componentName;
      }
}
?>