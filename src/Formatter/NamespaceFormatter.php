<?php

namespace PhpTree\Formatter;

use PhpTree\Serializer\Normalizer\NodeNormalizer;

class NamespaceFormatter
{
    public function __construct(
        public readonly NodeNormalizer $node,
        public readonly string $offset = ''
    ) {}

    public function __toString()
    {
        return sprintf(
            "%s## %s",
            $this->offset,
            $this->node->namespace ? : '(global)'
        );
    }
}