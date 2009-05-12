#!/usr/bin/env php
<?php

// use development database settings
define('APPLICATION_ENV', 'cli');

// application.php is almost like index.php, but without running
require_once '../application/application.php';
$application->bootstrap('doctrine');

// run Doctrine CLI
$cli = new Doctrine_Cli(Zend_Registry::get('doctrine_config'));
$cli->run($_SERVER['argv']);

?>
