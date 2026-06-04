<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node\Param;

class ParameterNodeNormalizer extends AbstractObjectVariableNodeNormalizer
{
    public readonly bool $variadic;
    public readonly ?bool $static;

    public function __construct(
        Param $node,
        ...$args
    )
    {
        parent::__construct($node);

        $this->variadic     = $node->variadic;
        $this->static       = $this->readonly ? false : null;
    }

    public function initName(): string
    {
        return '$' . (string) $this->node->var->name;
    }
}