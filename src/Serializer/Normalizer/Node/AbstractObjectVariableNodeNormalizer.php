<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpTree\Resolver\ParameterResolver;

class AbstractObjectVariableNodeNormalizer extends AbstractObjectItemNodeNormalizer
{
    public readonly bool $readonly;
    public readonly bool $hasDefault;
    public readonly ?string $default;

    public function __construct(...$args)
    {
        parent::__construct(...$args);

        $this->default = $this->initDefault();
        $this->hasDefault = $this->initHasDefault();
        $this->readonly = $this->initReadonly();
    }

    public function initDefault(): ?string
    {
        return ParameterResolver::resolve($this->node->default);
    }

    public function initHasDefault(): bool
    {
        return (bool) 
            (
                isset($this->default)
                    ? $this->default
                    : $this->initDefault()
            );
    }

    public function initReadonly(): bool
    {
        return $this->node->isReadOnly();
    }
}