<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node;

class ClassNodeNormalizer extends AbstractNodeNormalizer
{
    const TYPE = "class";

    protected function initExtends(): string
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