<?php

namespace App\BinaryTree;

/**
 * @see https://github.com/mirahman/PHP-Data-Structure-and-Algorithms/blob/master/DS/Tree/Classes/
 */
class BinaryNode implements NodeInterface
{
    /**
     * @var mixed
     */
    protected $data;

    protected ?NodeInterface $left;
    protected ?NodeInterface $right;
    protected ?NodeInterface $parent;

    /**
     * @param mixed $data
     */
    public function __construct($data = null, NodeInterface $parent = null)
    {
        $this->data = $data;
        $this->parent = $parent;
        $this->left = null;
        $this->right = null;
    }

    public function min(): ?NodeInterface
    {
        $node = $this;

        while ($node->left !== null) {
            $node = $node->left;
        }

        return $node;
    }

    public function max(): ?NodeInterface
    {
        $node = $this;

        while ($node->right !== null) {
            $node = $node->right;
        }

        return $node;
    }

    public function successor(): ?NodeInterface
    {
        $node = $this;
        if ($node->right !== null) {
            return $node->right->min();
        }

        return null;
    }

    public function predecessor(): ?NodeInterface
    {
        $node = $this;
        if ($node->left !== null) {
            return $node->left->max();
        }

        return null;
    }

    public function parent(): ?NodeInterface
    {
        return $this->parent;
    }

    public function right(): ?NodeInterface
    {
        return $this->right;
    }

    public function left(): ?NodeInterface
    {
        return $this->left;
    }

    /**
     * @return mixed
     */
    public function data()
    {
        return $this->data;
    }

    public function setParent(?NodeInterface $node): void
    {
        $this->parent = $node;
    }

    public function setRight(?NodeInterface $node): void
    {
        $this->right = $node;
    }

    public function setLeft(?NodeInterface $node): void
    {
        $this->left = $node;
    }
}
