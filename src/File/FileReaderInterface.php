<?php

namespace App\File;

interface FileReaderInterface
{
    public function read(): \Generator;
}
