<?php
/**
 * @package com.makeabyte.agilephp.test.orm
 */
class SprocTest extends PHPUnit_Framework_TestCase {

	  /**
	   * @test
	   */
	  public function sprocTests() {

	/*
	         $crypto = new Crypto();
	  	     $digest = $crypto->getDigest('phpunit123');

	         $user1 = new User('sproc', $digest, 'sproc@mysql', '04/13/10', false, 1);
	  	     $user1->persist();

	         $authenticate = new SPauthenticate();
	  		 $authenticate->setUserId('sproc');
	  		 $authenticate->setPasswd($digest);

	  		 $auth = ORM::call($authenticate);

	  		 print_r($auth);

	  		 $user1->delete();
	*/

	         print_r(ORM::call(new User()));

	         print_r(ORM::call(new User('admin')));

	         print_r(ORM::call(new Role()));
	         
	         print_r(ORM::call(new Role('test')));
	      
	  }
}
?>