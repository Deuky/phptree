<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpTree\Resolver\ParameterResolver;

/**
 * @property Property $node
 */
class PropertyNodeNormalizer extends AbstractObjectVariableNodeNormalizer
{
    public readonly bool $static;

    public function __construct(
        Property $node,
        public readonly PropertyProperty $prop,
    ) {
        parent::__construct($node);

        $this->static = $node->isStatic();
    }

    public function initName(): string
    {
        return '$' . (string) $this->prop->name;
    }

    public function initDefault(): ?string
    {
        return ParameterResolver::resolve($this->prop->default);
    }
}