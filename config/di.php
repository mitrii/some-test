<?php

use League\Flysystem\FilesystemAdapter;
use League\Flysystem\FilesystemOperator;
use League\MimeTypeDetection\MimeTypeDetector;

return [
    // ReactPHP
    \React\EventLoop\LoopInterface::class => DI\factory('React\EventLoop\Loop::get'),

    // Flysystem
    FilesystemAdapter::class => static function() {
        return new League\Flysystem\Local\LocalFilesystemAdapter("/");
    },
    FilesystemOperator::class => static function(FilesystemAdapter $adapter) {
        return new League\Flysystem\Filesystem($adapter);
    },
    MimeTypeDetector::class => DI\create(\League\MimeTypeDetection\ExtensionMimeTypeDetector::class),

    // BST
    \App\BinaryTree\BinarySearchTreeInterface::class => DI\factory(function() {
        return new \App\BinaryTree\BinarySearchTree(
            Closure::fromCallable(new \App\Comparison\PriceGreaterComparison()),
            Closure::fromCallable(new \App\Comparison\PriceLessComparison()),
        );
    }),

];

