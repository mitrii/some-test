<?php

namespace App\File;

use League\Flysystem\FilesystemOperator;
use League\Flysystem\StorageAttributes;

class CsvFileReader implements FileReaderInterface
{
    private string $filepath;
    private FilesystemOperator $fs;

    public function __construct(string $filepath, FilesystemOperator $fs)
    {
        $this->filepath = $filepath;
        $this->fs = $fs;
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     */
    public function read(): \Generator
    {
        $row = 0;
        if (($handle = $this->fs->readStream($this->filepath))) {
            while (($cols = fgetcsv($handle, 1000, ",")) !== FALSE) {

                // skip empty row
                if ($cols === null) {
                    continue;
                }

                $row++;

                // get csv header
                if ($row === 1) {
                    $colsHeader = array_flip($cols);
                    continue;
                }

                // generate assoc array of row
                $data = [];
                foreach ($colsHeader as $header => $key) {
                    $data[$header] = $cols[$key];
                }

                yield $data;
            }
            fclose($handle);
        }
    }
}
