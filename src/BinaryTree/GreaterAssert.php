<?php

namespace App\BinaryTree;

class GreaterAssert
{
    /**
     * @param array{ID: int, Price: float} $a
     * @param array{ID: int, Price: float} $b
     * @return bool
     */
    public function __invoke(array $a, array $b): bool
    {
        if ($a['Price'] !== $b['Price']) {
            return $a['Price'] > $b['Price'];
        }

        return $a['ID'] >= $b['ID'];
    }
}
