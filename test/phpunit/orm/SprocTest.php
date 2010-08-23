<?php
/**
 * @package com.makeabyte.agilephp.test.orm
 */
class SprocTest extends PHPUnit_Framework_TestCase {

	  /**
	   * @test
	   */
	  public function sprocTests() {

	         /* Test Stored Procedures That Contain Business Logic (CRUD Operations) */

	         // persist
	         $role = new SPRole();
	         $role->setName('sproctesting');
	         $role->setDescription('this is a unit testing role');
	         $return = $role->persist();
	         $id = $return->getId();
	         PHPUnit_Framework_Assert::assertGreaterThan(0, $id, 'Failed to persist new role');

	         // merge
	         $role2 = new SPRole();
	         $role2->setId($id);
	         $role2->setName('sproctesting2');
	         $role2->setDescription('this is the new and improved unit testing role');
	         $role2->merge();

	         $role2->clear();
	         PHPUnit_Framework_Assert::assertType('null', $role2->getId(), 'Failed to clear role2');
	         
	         $role2->setId($id);
	         $role2->get();
	         PHPUnit_Framework_Assert::assertGreaterThan(0, $role2->getId(), 'Failed to get role2');

	         // delete
	         $role3 = new SPRole();
	         $role3->setId($id);
	         $role3->delete();

	         $role3->clear();
	         PHPUnit_Framework_Assert::assertType('null', $role3->getId(), 'Failed to clear role3');

	         $role3->get();
	         PHPUnit_Framework_Assert::assertEquals(null, $role3->getId(), 'Failed to delete role3');

	         $role4 = new SPRole();
	         $return = $role4->find();
	         PHPUnit_Framework_Assert::assertEquals(2, count($return), 'Failed to find roles (role4)');

			 /* Test "relational" Stored Procedures */
	         $user = new User();
	         $users = $user->find();
	         PHPUnit_Framework_Assert::assertEquals(2, count($users), 'Failed to get count = 2 at $user->find()');
	         PHPUnit_Framework_Assert::assertEquals('admin', $users[0]->getUsername(), 'Failed to get admin user');
	         PHPUnit_Framework_Assert::assertEquals('admin', $users[0]->getRole()->getName(), 'Failed to get admin user admin role');
	         PHPUnit_Framework_Assert::assertEquals('test', $users[1]->getUsername(), 'Failed to get test user');
	         PHPUnit_Framework_Assert::assertEquals('test', $users[1]->getRole()->getName(), 'Failed to get test user test role');
	  }
}
?>