<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node;
use PhpTree\Resolver\TypeResolver;

class TypeNodeNormalizer
{
    public readonly null|string $type;
    public readonly array $types;

    public function __construct(
        public readonly Node $node
    )
    {
        $this->types = ($node->type ?? []) ? [$node->type] : ($node->types ?? []);
        $this->type = TypeResolver::resolve($this->node);
    }

    public function __toString(): string
    {
        return $this->type;
    }
}