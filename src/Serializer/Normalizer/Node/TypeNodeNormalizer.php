<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node;
use PhpTree\Resolver\TypeResolver;
use PhpParser\Node\Name\FullyQualified;

class TypeNodeNormalizer
{
    public readonly array $types;

    public function __construct(
        public readonly ?Node $node,
    )
    {
        $this->types = ($node->type ?? []) ? [$node->type] : ($node->types ?? []);
    }

    public function __toString(): string
    {
        return TypeResolver::resolve($this->node) ?? '';
    }
}