<?php

namespace PhpTree\Presenter;

use PhpTree\Serializer\Normalizer\NodeNormalizer;
use PhpTree\Internal\ClassifiedInternal;
use PhpTree\Formatter\NamespaceFormatter;
use PhpTree\Formatter\ClassFormatter;
use PhpTree\Formatter\MethodFormatter;

class ConsolePresenter extends AbstractPresenter
{
    public function render(array $nodes): void
    {
        $orderedGroups = new ClassifiedInternal(
            $nodes, 
            fn(NodeNormalizer $node) => $node->nodeNormalizer->namespace,
            sort: true
        );

        foreach ($orderedGroups as $namespace => $namespaceNodes) {
            if (!$namespaceNodes) {
                continue;
            }

            $this->writer->write(implode(PHP_EOL, ($this->namespaceFormatter(...$namespaceNodes))).PHP_EOL);
        }
    }

    public function namespaceFormatter(NodeNormalizer ...$nodes)
    {
        $sub = [];

        foreach ($this->classFormatter(...$nodes) as $class)
        {
            $sub = [
                ...$sub,
                ...$class,
                null,
            ];
        }

        return [
            new NamespaceFormatter(
                current($nodes)
            ),
            null,
            ...$sub
        ];
    }

    public function classFormatter(NodeNormalizer ...$nodes)
    {
        return 
            array_map(
                fn($node) => [
                    new ClassFormatter(
                        $node,
                        "  "
                    ),
                    ...$this->methodFormatter($node)
                ],
                $nodes
            )
        ;
    }

    public function methodFormatter(NodeNormalizer $node)
    {
        return array_map(
            fn($method) => new MethodFormatter($method, "    "),
            $node->methods
        );
    }

    protected function relativePath(string $absolutePath): string
    {
        throw new \RuntimeException('Not expected call on this class');
    }
}