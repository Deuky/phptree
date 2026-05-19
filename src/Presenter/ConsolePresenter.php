<?php

namespace PhpTree\Presenter;

use PhpTree\Serializer\Normalizer\NodeNormalizer;
use PhpTree\Interface\PresenterInterface;
use PhpTree\Interface\WriterInterface;
use PhpTree\Internal\ClassifiedInternal;
use PhpTree\Formater\NamespaceFormater;
use PhpTree\Formater\ClassFormater;
use PhpTree\Formater\MethodFormater;

class ConsolePresenter implements PresenterInterface
{
    public function __construct(
        public readonly WriterInterface $writer,
        ...$args
    ){}

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

            $this->writer->write(implode(PHP_EOL, ($this->namespaceFormater(...$namespaceNodes))).PHP_EOL);
        }
    }

    public function namespaceFormater(NodeNormalizer ...$nodes)
    {
        $sub = [];

        foreach ($this->classFormater(...$nodes) as $class)
        {
            $sub = [
                ...$sub,
                ...$class,
                null,
            ];
        }

        return [
            new NamespaceFormater(
                current($nodes)
            ),
            null,
            ...$sub
        ];
    }

    public function classFormater(NodeNormalizer ...$nodes)
    {
        return 
            array_map(
                fn($node) => [
                    new ClassFormater(
                        $node,
                        "  "
                    ),
                    ...$this->methodFormater($node)
                ],
                $nodes
            )
        ;
    }

    public function methodFormater(NodeNormalizer $node)
    {
        return array_map(
            fn($method) => new MethodFormater($method, "    "),
            $node->methods
        );
    }
}