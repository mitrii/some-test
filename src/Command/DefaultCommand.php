<?php

namespace App\Command;

use App\BinaryTree\BinarySearchTreeInterface;
use App\File\CsvFileReader;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\FilesystemReader;
use League\MimeTypeDetection\MimeTypeDetector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DefaultCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:default';

    private FilesystemOperator $fs;

    protected array $counts = [];

    private BinarySearchTreeInterface $tree;
    private MimeTypeDetector $mtDetector;


    protected function configure(): void
    {
        $this->addArgument('dir', InputArgument::REQUIRED, 'Path to cvs files directory');
        $this->addOption('rows', 'r', InputOption::VALUE_REQUIRED, 'Rows count in result', 1000);
        $this->addOption('ids', 'i', InputOption::VALUE_REQUIRED, 'Max IDs duplicates', 5);
        $this->addOption('threads', 't', InputOption::VALUE_OPTIONAL, 'Number of threads', 4);
    }

    public function __construct(
        string $name,
        FilesystemOperator $fs,
        MimeTypeDetector $mtDetector,
        BinarySearchTreeInterface $tree
    )
    {
        parent::__construct($name);
        $this->fs = $fs;

        $this->tree = $tree;
        $this->mtDetector = $mtDetector;
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dir = realpath($input->getArgument('dir'));
        $rowsCount = (int) $input->getOption('rows');
        $idsCount = (int) $input->getOption('ids');

        $threads = (int) $input->getOption('threads');

        if ($dir === false) {
            $output->writeln('Invalid dir');
            return self::FAILURE;
        }

        $starttime = microtime(true);


        \Co\run(function($dir) use ($threads){
            $filesChan = new \Swoole\Coroutine\Channel($threads);
            $linesChan = new \Swoole\Coroutine\Channel($threads);

            go(static function ($dir, $fs, $mtDetector) use ($filesChan) {
                foreach ($fs->listContents($dir, FilesystemReader::LIST_DEEP) as $attrs) {
                    if (!$attrs->isFile() || $mtDetector->detectMimeTypeFromPath($attrs->path()) !== 'text/csv') {
                        continue;
                    }
                    $filesChan->push(['filename' => $attrs->path()]);
                }
                $filesChan->push(false);
            }, $dir, $this->fs, $this->mtDetector);


            go(static function ($fs) use ( $filesChan, $linesChan) {
                while(1) {
                    $data = $filesChan->pop();
                    if ($data === false) {
                        $filesChan->close();
                        $linesChan->push(false);
                        break;
                    }

                    $filereader = new CsvFileReader($data['filename'], $fs);
                    foreach ($filereader->read() as $lineData)
                    {
                        $linesChan->push($lineData);
                    }
                }
            }, $this->fs);



            go(static function ($tree) use ($linesChan) {
                while(1) {
                    $data = $linesChan->pop();
                    if ($data === false) {
                        $linesChan->close();
                        break;
                    }
                    $tree->insert($data);
                }
            }, $this->tree);
        }, $dir);


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
