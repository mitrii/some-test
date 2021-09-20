<?php

namespace App\Command;

use App\BinaryTree\BinarySearchTree;
use App\BinaryTree\BinarySearchTreeInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\FilesystemReader;
use League\Flysystem\StorageAttributes;
use League\MimeTypeDetection\ExtensionMimeTypeDetector;
use League\MimeTypeDetection\MimeTypeDetector;
use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DefaultCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:default';

    private LoopInterface $loop;

    private FilesystemOperator $fs;

    protected array $counts = [];

    private BinarySearchTreeInterface $tree;
    private MimeTypeDetector $mtDetector;

    protected function configure(): void
    {
        $this->addArgument('dir', InputArgument::REQUIRED, 'Path to cvs files directory');
        $this->addOption('rows', 'r', InputOption::VALUE_REQUIRED, 'Rows count in result', 1000);
        $this->addOption('ids', 'i', InputOption::VALUE_REQUIRED, 'Max IDs duplicates', 5);
        $this->addOption('processes', 'p', InputOption::VALUE_OPTIONAL, 'Number of processes', 4);
    }

    public function __construct(
        string $name,
        LoopInterface $loop,
        FilesystemOperator $fs,
        MimeTypeDetector $mtDetector,
        BinarySearchTreeInterface $tree
    )
    {
        parent::__construct($name);
        $this->loop = $loop;
        $this->fs = $fs;

        $this->tree = $tree;
        $this->mtDetector = $mtDetector;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dir = realpath($input->getArgument('dir'));
        $rowsCount = (int) $input->getOption('rows');
        $idsCount = (int) $input->getOption('ids');

        $procs = $input->getOption('processes');

        if ($dir === false) {
            $output->writeln('Invalid dir');
            return self::FAILURE;
        }

        $files = $this->fs->listContents($dir, FilesystemReader::LIST_DEEP)
            ->filter(function (StorageAttributes $attributes) {
                return $attributes->isFile() && $this->mtDetector->detectMimeTypeFromPath($attributes->path()) === 'text/csv';
            });


        /**
         * @var StorageAttributes $file
         */
        foreach ($files as $file)
        {

            $row = 0;
            if (($handle = $this->fs->readStream($file->path()))) {
                while (($cols = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if ($cols === null) {
                        continue;
                    }

                    $row++;
                    if ($row === 1) {
                        $colsHeader = array_flip($cols);
                        continue;
                    }

                    $data = [];
                    foreach ($colsHeader as $header => $key)
                    {
                        $data[$header] = $cols[$key];
                    }

                    $this->tree->insert($data);
                }
                fclose($handle);
            }

            /*
            $childProcess = new Process(PHP_BINARY .  . $file->path() );
            $childProcess->start();

            $childProcess->stdout->on('cols', function ($chunk) {
                echo $chunk;
            });

            $childProcess->on('exit', function($exitCode, $termSignal) {
                echo 'Process exited with code ' . $exitCode . PHP_EOL;
            });
            */
        }

        $output->writeln('ID,Price');
        $count = 0;
        foreach ($this->tree->traverse($this->tree->getRoot()) as $data)
        {
            $this->counts[$data['ID']] = isset($this->counts[$data['ID']]) ? $this->counts[$data['ID']] + 1 : 1;
            /**
             * @var array{ID: int, Price: float} $data
             */

            if ($this->counts[$data['ID']] <= $idsCount) {
                $count++;
                $output->writeln(sprintf('%d,%01.1f', $data['ID'], $data['Price']));
            }

            if ($count === $rowsCount) {
                break;
            }
        }

        return Command::SUCCESS;
    }
}
