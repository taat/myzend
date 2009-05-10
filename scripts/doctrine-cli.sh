#!/usr/bin/env php
<?php

// use development database settings
define('APPLICATION_ENV', 'development');

// application.php is almost like index.php, but without running
require_once 'doctrine-cli.php';
$application->bootstrap('doctrine');

// run Doctrine CLI
$cli = new Doctrine_Cli(Zend_Registry::get('doctrine_config'));
$cli->run($_SERVER['argv']);

?>
