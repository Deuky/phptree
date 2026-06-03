<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node\Param;
use PhpParser\Node\Expr;
use PhpParser\Node\NullableType;
use PhpTree\Resolver\ParameterResolver;
use PhpTree\Resolver\DocBlockResolver;

class ParameterNodeNormalizer extends AbstractObjectItemNodeNormalizer
{
    public readonly string $name;
    public readonly bool $isNullable;
    public readonly bool $hasDefault;
    public readonly bool $isVariadic;
    public readonly ?string $defaultValue;
    public readonly ?Expr $default;
    public readonly bool $readonly;
    public readonly ?bool $static;

    public function __construct(
        Param $node,
        ...$args
    )
    {
        parent::__construct($node);

        $this->isVariadic   = $node->variadic;
        $this->isNullable   = $this->initIsNullable();
        $this->hasDefault   = $node->default !== null;
        $this->readonly     = $node->isReadOnly();
        
        if ($this->readonly) {
            $this->static = false;
        } else {
            $this->static = null;
        }

        if ($this->hasDefault) {
            $this->default = $node->default;
            $this->defaultValue = ParameterResolver::resolve($this->default);
        } else {
            $this->default = null;
            $this->defaultValue = null;
        }
    }

    private function initIsNullable(): bool
    {
        return $this->node->type
            ? $this->node instanceof NullableType
            : false;
    }

    public function initName(): string
    {
        return '$' . (string) $this->node->var->name;
    }
}