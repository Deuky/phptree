<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Expr;
use PhpTree\Resolver\ParameterResolver;

/**
 * @property Property $node
 */
class PropertyNodeNormalizer extends AbstractObjectItemNodeNormalizer
{
    public readonly bool $static;
    public readonly bool $readonly;
    public readonly ?Expr $default;
    public readonly ?string $defaultValue;

    public function __construct(
        Property $node,
        public readonly PropertyProperty $prop,
    ) {
        parent::__construct($node);

        $this->static       = $node->isStatic();
        $this->readonly     = $node->isReadonly();
        $this->default      = $prop->default;
        $this->defaultValue = ParameterResolver::resolve($this->default);
    }

    public function initName(): string
    {
        return '$' . (string) $this->prop->name;
    }
}