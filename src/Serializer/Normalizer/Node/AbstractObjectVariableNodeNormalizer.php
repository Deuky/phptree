<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpTree\Resolver\ParameterResolver;

class AbstractObjectVariableNodeNormalizer extends AbstractObjectItemNodeNormalizer
{
    public readonly bool $readonly;
    public readonly ?string $default;

    public function __construct(...$args)
    {
        parent::__construct(...$args);

        $this->default = $this->initDefault();
        $this->readonly = $this->initReadonly();
    }

    public function initDefault(): ?string
    {
        return ParameterResolver::resolve($this->node->default);
    }

    public function initReadonly(): bool
    {
        return $this->node->isReadOnly();
    }
}