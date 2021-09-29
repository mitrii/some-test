<?php

namespace App\BinaryTree;

use Closure;

/**
 * @see https://github.com/mirahman/PHP-Data-Structure-and-Algorithms/blob/master/DS/Tree/Classes/
 */
class BinarySearchTree implements BinarySearchTreeInterface
{
    public ?BinaryNode $root;

    protected $greaterCallback;

    protected $smallerCallback;

    /**
     * @param callable|null $greaterCallback
     * @param callable|null $smallerCallback
     */
    public function __construct(callable $greaterCallback = null, callable $smallerCallback = null)
    {
        $this->greaterCallback = $greaterCallback ?? static function(?int $a, ?int $b): bool { return $a > $b; };
        $this->smallerCallback = $smallerCallback ?? static function(?int $a, ?int $b): bool { return $a < $b; };
    }

    public function getRoot(): ?NodeInterface
    {
        return $this->root ?? null;
    }

    public function isEmpty(): bool
    {
        return !isset($this->root);
    }

    public function search($data)
    {
        if ($this->isEmpty()) {
            return false;
        }

        $node = $this->root;

        while ($node !== null) {
            if (($this->greaterCallback)($data, $node->data())) {
                $node = $node->right();
                continue;
            }

            if (($this->smallerCallback)($data, $node->data())) {
                $node = $node->left();
                continue;
            }

            break;
        }

        return $node;
    }

    /**
     * @param mixed $data
     * @return NodeInterface|null
     */
    public function insert($data): ?NodeInterface
    {
        if ($this->isEmpty()) {
            $node = new BinaryNode($data);
            $this->root = $node;
            return $node;
        }

        $node = $this->root;

        while ($node !== null) {

            if (($this->greaterCallback)($data, $node->data())) {
                if ($node->right() !== null) {
                    $node = $node->right();
                } else {
                    $node->setRight(new BinaryNode($data));
                    $node = $node->right();
                    break;
                }
            } elseif (($this->smallerCallback)($data, $node->data())) {
                if ($node->left() !== null) {
                    $node = $node->left();
                } else {
                    $node->setLeft(new BinaryNode($data));
                    $node = $node->left();
                    break;
                }
            } else {
                break;
            }
        }

        return $node;
    }

    public function traverse(?NodeInterface $node): \Generator
    {
        if ($node !== null) {
            if ($node->left() !== null) {
                yield from $this->traverse($node->left());
            }
            yield $node->data();
            if ($node->right() !== null) {
                yield from $this->traverse($node->right());
            }
        }
    }
}
