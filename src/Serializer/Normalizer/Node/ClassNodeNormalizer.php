<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node;
use PhpTree\Trait\Constant;
use PhpTree\Trait\Property;
use function array_map;

class ClassNodeNormalizer extends AbstractObjectNodeNormalizer
{
    use Constant, Property;

    public readonly array $properties;

    protected function initExtends(): ?string
    {
        return ((string) $this->node->extends) ?: null;
    }

    protected function initIsAbstract(): bool
    {
        return $this->node->isAbstract();
    }

    protected function initIsFinal(): bool
    {
        return $this->node->isFinal();
    }

    protected function initImplements(): array
    {
        return array_map(
            fn(Node\Name $n): string => $n->toString(),
            $this->node->implements,
        );
    }
}