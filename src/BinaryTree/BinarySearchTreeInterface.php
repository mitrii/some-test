<?php

namespace App\BinaryTree;

interface BinarySearchTreeInterface
{
    public function getRoot(): ?NodeInterface;

    public function isEmpty(): bool;

    /**
     * @param mixed $data
     * @return NodeInterface|false|null
     */
    public function search($data);

    /**
     * @param mixed $data
     * @return NodeInterface|null
     */
    public function insert($data): ?NodeInterface;

    public function traverse(?NodeInterface $node): \Generator;
}
