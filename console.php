#!/usr/bin/env php
<?php

use App\Command\DefaultCommand;
use Symfony\Component\Console\Application;

$container = require (__DIR__ . '/bootstrap.php');

// Symfony Console Application
$application = new Application('app', '1.0.0');

$command = $container->make(DefaultCommand::class, [
        'name' => DefaultCommand::getDefaultName(),
]);


$application->add($command);


$application->setDefaultCommand($command->getName(), true);
return $application->run();
