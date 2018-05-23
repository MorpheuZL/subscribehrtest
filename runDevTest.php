<?php

require_once "SubscribeHrTest.php";

echo Helpers::getDate()." - Running Subscribe HR Dev Test".PHP_EOL;

$devTest = new SubscribeHrTest($argv[1]);

$devTest->runTest();
