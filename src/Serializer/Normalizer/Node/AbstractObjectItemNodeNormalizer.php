<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node;
use PhpTree\Resolver\VisibilityResolver;

abstract class AbstractObjectItemNodeNormalizer
{
    public readonly string $name;
    public readonly string $visibility;

    public function __construct(
        public readonly Node $node
    )
    {
    	$this->name 		= $this->initName();
        $this->visibility 	= VisibilityResolver::resolve($node);
    }

    public function initName(): string
    {
        return (string) $this->node->name;
    }
}
