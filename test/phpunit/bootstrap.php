<?php

$test = realpath( dirname( __FILE__ ) . '/../' );
$src = realpath( dirname( __FILE__ ) . '/../../src' );

require_once $src . DIRECTORY_SEPARATOR . 'AgilePHP.php';

$agilephp = AgilePHP::getFramework( $test . DIRECTORY_SEPARATOR . 'agilephp.xml' );
$agilephp->setWebRoot( $test );
$agilephp->setFrameworkRoot( $src );
$agilephp->handleErrors();

require_once $test . '/phpunit/BaseTest.php';
?>