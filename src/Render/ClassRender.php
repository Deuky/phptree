<?php

namespace PhpTree\Render;

use PhpTree\Serializer\Normalizer\NodeNormalizer;

class ClassRender
{
    public readonly string $offset;

    public function __construct(
        public readonly NodeNormalizer $node,
        string $offset = ''
    )
    {
        $this->offset = $offset;
    }

    public function type($normalizer): string
    {
        $parts = [];

        if ($normalizer->isFinal) {
            $parts[] = 'final';
        }

        if ($normalizer->isAbstract) {
            $parts[] = 'abstract';
        }

        $parts[] = $normalizer->type;

        return implode(' ', $parts);
    }

    public function __toString()
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

        $return[] = implode (
                        ' ', 
                        array_filter([
                            $this->type($this->node),
                            $this->node->name,
                            $this->node->extends ? sprintf('extends %s', $this->node->extends) : '',
                            $this->node->implements !== [] ? sprintf('implements %s', implode(', ', $this->node->implements)) : '',
                        ])
                    );

        return implode(PHP_EOL, array_map(fn($v) => "{$this->offset}$v", $return));
    }
}