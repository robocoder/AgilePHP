<?php

$test = realpath(dirname(__FILE__) . '/../');
$src = realpath(dirname(__FILE__) . '/../../src');

require_once $src . DIRECTORY_SEPARATOR . 'AgilePHP.php';

AgilePHP::setFrameworkRoot($src);
AgilePHP::init($test . DIRECTORY_SEPARATOR . 'agilephp.xml');
AgilePHP::setWebRoot($test);
AgilePHP::setDefaultTimezone('America/New_York');
AgilePHP::handleErrors();
?>