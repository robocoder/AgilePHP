<?php

$test = realpath( dirname( __FILE__ ) . '/../' );
$src = realpath( dirname( __FILE__ ) . '/../../src' );

//echo 'Bootstrap is using webRoot: ' . $test . "\n";
//echo 'Bootstrap is using frameworkRoot: ' . $src . "\n";

require_once $src . DIRECTORY_SEPARATOR . 'AgilePHP.php';
require_once $test . '/phpunit/BaseTest.php';

$agilephp = AgilePHP::getFramework( $test . DIRECTORY_SEPARATOR . 'agilephp.xml' );
$agilephp->setWebRoot( $test );
$agilephp->setFrameworkRoot( $src );
$agilephp->handleErrors();
?>