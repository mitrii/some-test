<?php

require __DIR__.'/vendor/autoload.php';

use App\Command\DefaultCommand;
use App\Command\WorkerCommand;
use Symfony\Component\Console\Application;

$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/config/di.php');
//$builder->enableCompilation(__DIR__ . '/runtime');
//$builder->writeProxiesToFile(true, __DIR__ . '/runtime/proxies');

return $builder->build();
