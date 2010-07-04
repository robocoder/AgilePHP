<?php
/**
 * Responsible for Identity package tests
 * 
 * @package com.makeabyte.agilephp.test.identity
 */
class IdentityTest extends PHPUnit_Framework_TestCase {

	  /**
	   * @test
	   */
	  public function defaultConfigurationTests() {

	  	     PHPUnit_Framework_Assert::assertType('User', Identity::getModel(), 'Failed to get default \'User\' Identity model');

	  	     $persisted = ORM::find(new User('identity-phpunit'));
	  	     if(isset($persisted[0])) ORM::delete(new User('identity-phpunit'));

	  	     Identity::setUsername('identity-phpunit');
	  	     Identity::setPassword('phpunit');
	  	     Identity::setEmail('root@localhost');
	  	     Identity::setCreated('now');
	  	     Identity::setRole(new Role('test'));
	  	     Identity::register();

	  	     try {
	  	     		PHPUnit_Framework_Assert::assertTrue(Identity::login('identity-phpunit', 'phpunit'), 'Failed to authenticate \'identity-phpunit\' test user');
	  	     }
	  	     catch(AccessDeniedException $e) {

	  	     		if(!preg_match('/Account Disabled/', $e->getMessage()))
	  	     			PHPUnit_Framework_Assert::fail('Failed to assert account is disabled');
	  	     }

	  	     Identity::setPassword('new-password');
	  	     Identity::merge();

	  	     $persisted = ORM::find(new User('identity-phpunit'));
	  	     PHPUnit_Framework_Assert::assertNotNull($persisted[0], 'Failed to look up identity-phpunit test user');
	  	     PHPUnit_Framework_Assert::assertEquals('b8b9f8f23992ebc2617febc03d92ecb1763fba7b77a5d053b69c416bad18a369', $persisted[0]->getPassword(), 'Failed to update identity-phpunit test user password');

	  	     Identity::delete();
	  	     $persisted = ORM::find(new User('identity-phpunit'));
	  	     PHPUnit_Framework_Assert::assertNull( $persisted[0], 'Failed to delete identity-phpunit test user');

	  	     PHPUnit_Framework_Assert::assertEquals('DefaultAuthenticator', IdentityManagerFactory::getManager()->getAuthenticator(), 'Failed to get DefaultAuthenticator');
	  	     PHPUnit_Framework_Assert::assertEquals('BasicRegistrationMailer', IdentityManagerFactory::getManager()->getRegistrationMailer(), 'Failed to get BasicRegistrationMailer');
	  	     PHPUnit_Framework_Assert::assertEquals('BasicResetPasswdMailer', IdentityManagerFactory::getManager()->getResetPasswdMailer(), 'Failed to get BasicResetPasswdMailer');
	  	     PHPUnit_Framework_Assert::assertEquals('BasicForgotPasswdMailer', IdentityManagerFactory::getManager()->getForgotPasswdMailer(), 'Failed to get BasicForgotPasswdMailer');

	  	     Identity::addRole(new Role('foo'));
	  	     Identity::addRole(new Role('bar'));
	  	     Identity::addRole(new Role('baz'));

	  	     $roles = Identity::getRoles();

	  	     PHPUnit_Framework_Assert::assertEquals('foo', $roles[0]->getName(), 'Failed to add foo role');
	  	     PHPUnit_Framework_Assert::assertEquals('bar', $roles[1]->getName(), 'Failed to add bar role');
	  	     PHPUnit_Framework_Assert::assertEquals('baz', $roles[2]->getName(), 'Failed to add baz role');
	  	     
	  	     Identity::revokeRole(new Role('bar'));

	  	     PHPUnit_Framework_Assert::assertEquals(2, count(Identity::getRoles()), 'Failed to revoke bar role');
	  	     
	  	     PHPUnit_Framework_Assert::assertEquals(true, Identity::hasRole(new Role('foo')), 'Failed to locate added role foo');
	  }
}
?>