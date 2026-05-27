<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Const_;
use PhpTree\Resolver\ParameterResolver;
use PhpTree\Resolver\VisibilityResolver;

class ConstantNodeNormalizer extends AbstractObjectPropertyNodeNormalizer
{
    public readonly string $name;
    public readonly ?string $value;
    public readonly string $visibility;
    public readonly string $type;

    public function __construct(
        public readonly ClassConst $node,
        public readonly Const_ $const,
    ) {
        $this->name       = (string) $const->name;
        $this->value      = ParameterResolver::resolve($const->value);
        $this->visibility = VisibilityResolver::resolve($node);
        $this->type       = new TypeNodeNormalizer($node);
    }
}