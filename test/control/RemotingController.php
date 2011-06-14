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
 * Responsible for processing all AgilePHP remoting calls.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.control
 */
class RemotingController extends Remoting {

	public function test() {

		$m2 = new TestModel();
		$m2->setField1('This is TestModel2 field 1');
		$m2->setField2('This is TestModel2 field 2');

		$m1 = new TestModel();
		$m1->setField1('This is TestModel1 field 1');
		$m1->setField2('This is TestModel1 field 2');
		$m1->setChild($m2);

		$group = new GroupOfModels(array($m1, $m2));

		echo JsonRenderer::render($group);
	}
}

class TestModel {
	
	private $field1;
	private $field2;
	private $child;
	
	public function __construct($field1 = null, $field2 = null, TestModel $child = null) {
		$this->field1 = $field1;
		$this->field2 = $field2;
		$this->child = $child;
	}

	public function setField1($value) {
		$this->field1 = $value;
	}
	
	public function getField1() {
		return $this->field1;
	}
	
	public function setField2($value) {
		$this->field2 = $value;
	}
	
	public function getField2() {
		return $this->field2;
	}
	
	public function setChild(TestModel $testModel) {
		$this->child = $testModel;
	}

	public function getChild() {
		return $this->child;
	}
}

class GroupOfModels {

	private $models = array();

	public function __construct(array $models = array()) {
		$this->models = $models;
	}

	public function setModels(array $models) {
		$this->models = $models;
	}

	public function getModels() {
		return $this->models;
	}
}
?>