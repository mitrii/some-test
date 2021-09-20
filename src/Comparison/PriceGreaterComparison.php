<?php

namespace App\Comparison;

use App\BinaryTree\ComparisonInterface;

class PriceGreaterComparison implements ComparisonInterface
{
    /**
     * @param array{ID: int, Price: float} $a
     * @param array{ID: int, Price: float} $b
     * @return bool
     */
    public function __invoke($a, $b): bool
    {
        if ($a['Price'] !== $b['Price']) {
            return $a['Price'] > $b['Price'];
        }

        return $a['ID'] >= $b['ID'];
    }
}
