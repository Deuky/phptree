<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\Stmt\Class_;
use PhpTree\Resolver\ParameterResolver;
use PhpTree\Resolver\DocBlockResolver;
use PhpTree\Resolver\VisibilityResolver;

class PropertyNodeNormalizer extends AbstractObjectItemNodeNormalizer
{
    public readonly TypeNodeNormalizer $type;
    public readonly bool $static;
    public readonly bool $readonly;
    public readonly ?string $defaultValue;
    public readonly ?string $description;

    public function __construct(
        Property $node,
        public readonly PropertyProperty $prop,
    ) {
        parent::__construct($node);

        $this->type         = new TypeNodeNormalizer($node);
        $this->static       = $node->isStatic();
        $this->readonly     = $node->isReadonly();
        $this->defaultValue = $prop->default !== null
                                ? ParameterResolver::resolve($prop->default)
                                : null;
        $this->description = DocBlockResolver::extractDescription($node);
    }

    public function initName(): string
    {
        return '$' . (string) $this->prop->name;
    }
}