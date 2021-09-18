<?php

namespace tests\Command;

use DateTime;
use App\Command\DefaultCommand;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Loop;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class DefaultCommandTest extends TestCase
{
    private const COMMAND_NAME = 'app:default';

    private ?CommandTester $commandTester;

    protected function setUp(): void
    {
        $application = new Application();
        $loop = Loop::get();

        $adapter = new LocalFilesystemAdapter(__DIR__ . '/..');
        $filesystem = new Filesystem($adapter);

        $application->addCommands([new DefaultCommand(self::COMMAND_NAME, $loop, $filesystem)]);

        $command = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($command);
    }

    protected function tearDown(): void
    {
        $this->commandTester = null;
    }

    public function testExecute()
    {
        $this->commandTester->execute(['dir' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'storage']);

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

    public function testInvalidDir()
    {
        $this->commandTester->execute(['dir' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '__invalid_dir']);

        $this->assertEquals('Invalid dir', trim($this->commandTester->getDisplay()));
        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }
}
