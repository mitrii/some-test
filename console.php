#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use App\Command\DefaultCommand;
use Symfony\Component\Console\Application;

// Symfony Console Application
$application = new Application('app', '1.0.0');

// ReactPHP
$loop = React\EventLoop\Loop::get();

// Flysystem
$adapter = new League\Flysystem\Local\LocalFilesystemAdapter("/");
$filesystem = new League\Flysystem\Filesystem($adapter);

$command = new DefaultCommand(DefaultCommand::getDefaultName(), $loop, $filesystem);

$application->add($command);

$application->setDefaultCommand($command->getName(), true);
return $application->run();
