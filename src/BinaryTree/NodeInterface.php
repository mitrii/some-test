<?php

namespace App\BinaryTree;

interface NodeInterface
{
    /**
     * @param mixed $data
     */
    public function __construct($data = null, NodeInterface $parent = null);

    /**
     * @return mixed
     */
    public function data();

    public function parent(): ?NodeInterface;
    public function setParent(?NodeInterface $node): void;

    public function right(): ?NodeInterface;
    public function setRight(?NodeInterface $node): void;

    public function left(): ?NodeInterface;
    public function setLeft(?NodeInterface $node): void;

    public function min(): ?NodeInterface;

    public function max(): ?NodeInterface;

    public function successor(): ?NodeInterface;

    public function predecessor(): ?NodeInterface;

    public function delete(): void;
}
