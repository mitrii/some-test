<?php

namespace tests\Command;

use DateTime;
use App\Command\DefaultCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class DefaultCommandTest extends TestCase
{

    private ?CommandTester $commandTester;

    protected function setUp(): void
    {
        $application = new Application();
        $application->addCommands([new DefaultCommand()]);
        $command = $application->find('app:default');
        $this->commandTester = new CommandTester($command);
    }

    protected function tearDown(): void
    {
        $this->commandTester = null;
    }

    public function testExecute()
    {
        $this->commandTester->execute([]);

        $this->assertEquals('', trim($this->commandTester->getDisplay()));
    }
}
