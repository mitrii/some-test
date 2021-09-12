#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use App\Command\DefaultCommand;
use Symfony\Component\Console\Application;

$application = new Application('app', '1.0.0');
$command = new DefaultCommand();

$application->add($command);

$application->setDefaultCommand($command->getName(), true);
return $application->run();
