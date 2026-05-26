<?php

namespace PhpTree\Formatter;

use PhpTree\Serializer\Normalizer\Node\MethodNodeNormalizer;
use PhpTree\Serializer\Normalizer\Node\ParameterNodeNormalizer;

class MethodFormatter
{
    public readonly string $offset;

    public function __construct(
        public readonly MethodNodeNormalizer $node,
        public readonly string $offset = ''
    ) { }

    public function signature(): string
    {
        $method = $this->node;

        $params = array_map(
            fn(ParameterNodeNormalizer $p): string => trim(
                $p->type . ' ' . $p->name .
                ($p->hasDefault ? ' = ' . ($p->defaultValue ?? 'null') : '')
            ),
            $method->parameters,
        );

        $signature = sprintf(
            '%s%s%s(%s)%s',
            $method->visibility . ' ',
            $method->isStatic ? 'static ' : '',
            $method->name,
            implode(', ', $params),
            $method->returnType->__toString() ? ": ".$method->returnType : '',
        );

        if ($method->throws !== []) {
            $signature .= " | ". sprintf('throws %s', implode(', ', $method->throws));
        }

        return $signature;
    }

    public function __toString(): string
    {
        $return = [];
        $description = $this->node->description;

        if ($description) {
            $return = array_reduce(
                array_map(
                    fn($description) => "* {$description}",
                    explode(PHP_EOL, $description)
                ),
                function($carry, $i) {
                    $carry[] = $i;
                    return $carry;
                },
                $return
            );
        }

        $return[] = $this->signature();

        return implode(PHP_EOL, array_map(fn($v) => "{$this->offset}$v", $return));
    }
}