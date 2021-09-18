<?php

namespace App\Command;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\FilesystemReader;
use League\Flysystem\StorageAttributes;
use League\MimeTypeDetection\ExtensionMimeTypeDetector;
use Prophecy\Argument;
use React\EventLoop\LoopInterface;
use RegexIterator;
use SplObjectStorage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DefaultCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:default';

    private LoopInterface $loop;

    private FilesystemOperator $fs;

    protected function configure(): void
    {
        $this->addArgument('dir', InputArgument::REQUIRED, 'Path to cvs files directory');
    }

    public function __construct(string $name, LoopInterface $loop, FilesystemOperator $fs)
    {
        parent::__construct($name);
        $this->loop = $loop;
        $this->fs = $fs;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dir = realpath($input->getArgument('dir'));

        if ($dir === false) {
            $output->writeln('Invalid dir');
            return self::FAILURE;
        }

        $detector = new ExtensionMimeTypeDetector();

        $files = $this->fs->listContents($dir, FilesystemReader::LIST_DEEP)
            ->filter(function (StorageAttributes $attributes) use ($detector) {
                return $attributes->isFile() && $detector->detectMimeTypeFromPath($attributes->path()) === 'text/csv';
            })
            ->map(function (StorageAttributes $attributes) {return  $attributes->path();})
            ->toArray();


        $output->writeln(count($files));


        return Command::SUCCESS;
    }
}
