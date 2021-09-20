<?php

namespace App\BinaryTree;

interface ComparisonInterface
{
    /**
     * @param mixed $a
     * @param mixed $b
     * @return bool
     */
    public function __invoke($a, $b): bool;
}
