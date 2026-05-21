<?php

namespace PhpTree\Formatter;

use PhpTree\Serializer\Normalizer\NodeNormalizer;

class NamespaceFormatter
{
    public readonly string $offset;

    public function __construct(
        public readonly NodeNormalizer $node,
        string $offset = ''
    ) {
        $this->offset = $offset;
    }

    public function __toString()
    {
        return sprintf(
            "%s## %s",
            $this->offset,
            $this->node->namespace ? : '(global)'
        );
    }
}