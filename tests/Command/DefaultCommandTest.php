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

        $adapter = new LocalFilesystemAdapter('/');
        $filesystem = new Filesystem($adapter);

        $application->addCommands([new DefaultCommand(
            self::COMMAND_NAME,
            $filesystem,
            new ExtensionMimeTypeDetector(),
            new BinarySearchTree(
                Closure::fromCallable(new \App\Comparison\PriceGreaterComparison()),
                Closure::fromCallable(new \App\Comparison\PriceLessComparison()),
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

    public function testSimpleExecute()
    {
        $this->commandTester->execute(['dir' => './tests/storage/simple', '--rows' => 5, '--ids' => 2]);

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

    public function testComplexExecute()
    {
        $this->commandTester->execute(['dir' => './tests/storage/complex', '--rows' => 1000, '--ids' => 5]);

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
