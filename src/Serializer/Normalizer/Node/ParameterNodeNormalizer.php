<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node\Param;
use PhpParser\Node\Expr;
use PhpParser\Node\NullableType;
use PhpTree\Resolver\ParameterResolver;
use PhpTree\Resolver\VisibilityResolver;
use PhpTree\Resolver\DocBlockResolver;

final class ParameterNodeNormalizer
{
    public readonly string $name;
    public readonly TypeNodeNormalizer $type;
    public readonly bool $isNullable;
    public readonly bool $hasDefault;
    public readonly ?string $defaultValue;
    public readonly ?Expr $default;
    public readonly bool $readonly;
    public readonly ?string $visibility;
    public readonly ?bool $static;
    public readonly ?string $description;

    public function __construct(public readonly Param $node)
    {
        $this->name         = '$' . (string) $node->var->name;
        $this->type         = new TypeNodeNormalizer($node->type);
        $this->isNullable   = $this->initIsNullable();
        $this->hasDefault   = $node->default !== null;
        $this->readonly     = $node->isReadOnly();
        $this->description  = DocBlockResolver::extractDescription($node);
        
        if ($this->readonly) {
            $this->visibility = VisibilityResolver::resolve($node);
            $this->static = false;
        } else {
            $this->visibility = null;
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
        if ($node = $this->node->type) {
            return $node instanceof NullableType;
        }

        return false;
    }
}