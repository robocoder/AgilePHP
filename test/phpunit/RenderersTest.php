<?php
/**
 * @package com.makeabyte.agilephp.test.mvc
 */
class RenderersTest extends PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function PHTMLRenderer() {

        $url = 'http://localhost/test/index.php?XDEBUG_SESSION_START=1&KEY=agilephp';
        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $response = curl_exec( $ch );
        $matches = preg_match( '/This is the default PHTML renderer/', $response );
        if( !$matches ) PHPUnit_Framework_Assert::fail( 'Failed to get expected PHTMLRenderer response' );
        curl_close( $ch );
    }

    /**
     * @test
     */
    public function AJAXRendererJSON() {

        $url = 'http://localhost/test/index.php/AJAXController/?XDEBUG_SESSION_START=1&KEY=agilephp';
        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $response = curl_exec( $ch );

        $matches = preg_match( '/{"result":"Some text from the AJAXController"}/', $response );
        if( !$matches ) PHPUnit_Framework_Assert::fail( 'Failed to get expected AJAXRenderer JSON response' );
        curl_close( $ch );
    }
     
    /**
     * @test
     */
    public function AJAXRendererXML() {

        $expected = '<?xml version="1.0" encoding="UTF-8" ?><Result><prop1>test1</prop1><prop2>test2</prop2></Result>';
        $url = 'http://localhost/test/index.php/AJAXController/xml/?XDEBUG_SESSION_START=1&KEY=agilephp';
        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $response = curl_exec( $ch );
        PHPUnit_Framework_Assert::assertEquals( $expected, $response, 'Failed to get expected AJAXRenderer XML response' );
        curl_close( $ch );
    }

    private function getMockObject() {

        $role = new Role();
        $role->setName('test');
        	
        $role2 = new Role();
        $role2->setName('newtest');
        	
        $roles = array($role, $role2);

        $user = new User();
        $user->setUsername('test');
        $user->setPassword('123abc');
        $user->setCreated(date('c', strtotime('now')));
        $user->setLastLogin(date('c', strtotime('now')));
        $user->setEmail('root@localhost');
        $user->setRole($role);
        $user->setRoles($roles);

        return $user;
    }
}
?>