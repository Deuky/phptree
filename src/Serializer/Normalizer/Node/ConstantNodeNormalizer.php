<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Const_;
use PhpTree\Resolver\ParameterResolver;

class ConstantNodeNormalizer extends AbstractObjectItemNodeNormalizer
{
    public readonly ?string $value;

    public function __construct(
        ClassConst $node,
        public readonly Const_ $const,
    ) {
        parent::__construct($node);
        $this->value      = ParameterResolver::resolve($const->value);
    }

    public function initName(): string
    {
        return $this->const->name;
    }
}