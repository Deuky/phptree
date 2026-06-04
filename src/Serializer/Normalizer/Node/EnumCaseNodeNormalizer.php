<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node\Stmt\EnumCase;
use PhpParser\Node\Stmt\Enum_;
use PhpTree\Resolver\ParameterResolver;

class EnumCaseNodeNormalizer extends AbstractNodeNormalizer
{
    public readonly ?string $value;

    public function __construct(
        EnumCase $node,
        public readonly Enum_ $prop
    ) 
    {
        parent::__construct($node);

        $this->value = ParameterResolver::resolve($node->expr);
    }

    public function initType(): ?string
    {
        return $this->prop->scalarType->name ?? null;
    }
}