<?php

namespace PhpTree\Trait;

use PhpParser\Node;
use PhpParser\Node\Stmt\Property as StmtProperty;
use PhpTree\Serializer\Normalizer\Node\MethodNodeNormalizer;

use function array_filter, array_values;

trait Constructor
{
    public readonly Node $node;
    public readonly ?MethodNodeNormalizer $constructor;

	protected function initConstructor(): void
    {
        $this->constructor = current(array_filter($this->methods ?? [], fn($m) => $m->isConstructor)) ?: null;
    }

    protected function getConstructorProperties(): array
    {
        return array_filter(
            $this->constructor->parameters ?? [], fn($parameter) => $parameter->visibility !== null
        );
    }
}