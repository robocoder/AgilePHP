<?php
/**
 * @package com.makeabyte.agilephp.test.scope
 */
class ScopeTests extends PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function applicationScopeTests() {

        $application = Scope::getApplicationScope();
        $application->set('var1', 'test');

        PHPUnit_Framework_Assert::assertEquals('test', $application->get('var1'), 'Failed to get var1 from ApplicationScope');

        $application->destroy();

        PHPUnit_Framework_Assert::assertNull($application->get('var1'), 'Failed to destroy ApplicationScope');
    }

    /**
     * @test
     */
    public function requestScopeTests() {

        $request = Scope::getRequestScope();
        $request->set('var1', 'test');
        $request->set('code', '<?php echo "this is a test"; ?>');

        PHPUnit_Framework_Assert::assertEquals('test', $request->get('var1'), 'Failed to get var1 from RequestScope');
        PHPUnit_Framework_Assert::assertEquals('<?php echo "this is a test"; ?>', $request->get('code'), 'Failed to get code variable from RequestScope');
        PHPUnit_Framework_Assert::assertEquals(0, preg_match( '/<\?php/', $request->getSanitized('code')), 'Failed to get sanitized code from RequestScope');
    }

    /**
     * @test
     */
    public function sessionScopeTests() {

        $session = Scope::getSessionScope();
        $session->set('var1', 'test');

        PHPUnit_Framework_Assert::assertEquals('test', $session->get('var1'), 'Failed to get var1 from SessionScope');

        $session->destroy();

        PHPUnit_Framework_Assert::assertNull($session->get('var1'), 'Failed to destroy SessionScope');
    }
}
?>