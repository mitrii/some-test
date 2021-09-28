<?php

use App\File\FileReaderInterface;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\FilesystemOperator;
use League\MimeTypeDetection\MimeTypeDetector;
use Symfony\Component\Console\Application;

return [

    // Flysystem
    FilesystemAdapter::class => static function() {
        return new League\Flysystem\Local\LocalFilesystemAdapter("/");
    },
    FilesystemOperator::class => static function(FilesystemAdapter $adapter) {
        return new League\Flysystem\Filesystem($adapter);
    },
    MimeTypeDetector::class => DI\create(\League\MimeTypeDetection\ExtensionMimeTypeDetector::class),

    FileReaderInterface::class => static function(string $filepath, FilesystemOperator $fs) {
        return new \App\File\CsvFileReader($filepath, $fs);
    },

    // BST
    \App\BinaryTree\BinarySearchTreeInterface::class => DI\factory(function() {
        return new \App\BinaryTree\BinarySearchTree(
            new \App\Comparison\PriceGreaterComparison,
            new \App\Comparison\PriceLessComparison,
        );
    }),

];

