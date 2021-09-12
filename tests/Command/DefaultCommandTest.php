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
        $this->commandTester->execute(['directory' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'storage']);

        $this->assertEquals(
            <<<CSV
ID,Price
1,0.1
1,0.1
2,0.1
3,0.5
CSV,
            trim($this->commandTester->getDisplay()));
    }
}
