<?php

namespace PhpTree\Output;

use PhpTree\Serializer\Normalizer\NodeNormalizer;
use PhpTree\Serializer\Normalizer\Node\MethodNodeNormalizer;
use PhpTree\Serializer\Normalizer\Node\ParameterNodeNormalizer;
use PhpTree\Interface\OutputInterface;
use PhpTree\Internal\ClassifiedInternal;
use PhpTree\Render\NamespaceRender;
use PhpTree\Render\ClassRender;
use PhpTree\Render\MethodRender;

class ConsoleOutput implements OutputInterface
{
    public function render(array $nodes, ?string $outputPath): void
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

            echo implode(PHP_EOL, ($this->namespaceRender(...$namespaceNodes))).PHP_EOL;
        }
    }

    public function namespaceRender(NodeNormalizer ...$nodes)
    {
        $sub = [];

        foreach ($this->classRender(...$nodes) as $class)
        {
            $sub = [
                ...$sub,
                ...$class,
                null,
            ];
        }

        return [
            new NamespaceRender(
                current($nodes)
            ),
            null,
            ...$sub
        ];
    }

    public function classRender(NodeNormalizer ...$nodes)
    {
        return 
            array_map(
                fn($node) => [
                    new ClassRender(
                        $node,
                        "  "
                    ),
                    ...$this->methodRender($node)
                ],
                $nodes
            )
        ;
    }

    public function methodRender(NodeNormalizer $node)
    {
        return array_map(
            fn($method) => new MethodRender($method, "    "),
            $node->methods
        );
    }
}