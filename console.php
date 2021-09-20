#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use App\Command\DefaultCommand;
use Symfony\Component\Console\Application;

$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/config/di.php');
//$builder->enableCompilation(__DIR__ . '/runtime');
//$builder->writeProxiesToFile(true, __DIR__ . '/runtime/proxies');
$container = $builder->build();

// Symfony Console Application
$application = new Application('app', '1.0.0');

$command = $container->make(DefaultCommand::class, ['name' => DefaultCommand::getDefaultName()]);

$application->add($command);

$application->setDefaultCommand($command->getName(), true);
return $application->run();
