<?php
error_reporting(E_ALL);
require('src/Codevat/Set.php');
require('src/Codevat/SetTests.php');

$suite = new \Codevat\SetTests();
$suite->runTests();
