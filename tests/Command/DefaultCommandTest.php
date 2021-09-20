<?php

namespace tests\Command;

use App\BinaryTree\BinarySearchTree;
use Closure;
use DateTime;
use App\Command\DefaultCommand;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\MimeTypeDetection\ExtensionMimeTypeDetector;
use League\MimeTypeDetection\MimeTypeDetector;
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

        $adapter = new LocalFilesystemAdapter('/');
        $filesystem = new Filesystem($adapter);

        $application->addCommands([new DefaultCommand(
            self::COMMAND_NAME,
            $loop,
            $filesystem,
            new ExtensionMimeTypeDetector(),
            new BinarySearchTree(
                Closure::fromCallable(new \App\BinaryTree\GreaterAssert()),
                Closure::fromCallable(new \App\BinaryTree\SmallerAssert()),
            )
        )]);

        $command = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($command);
    }

    protected function tearDown(): void
    {
        $this->commandTester = null;
    }

    public function testInvalidDir()
    {
        $this->commandTester->execute(['dir' =>  './tests/__invalid_dir']);

        $this->assertEquals('Invalid dir', trim($this->commandTester->getDisplay()));
        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }

    public function testExecute()
    {
        $this->commandTester->execute(['dir' => './tests/storage', '--rows' => 5, '--ids' => 2]);

        $this->assertEquals(
            <<<CSV
ID,Price
1,0.1
1,0.1
2,0.1
3,0.5
4,0.5
CSV,
            trim($this->commandTester->getDisplay()));
    }
}
