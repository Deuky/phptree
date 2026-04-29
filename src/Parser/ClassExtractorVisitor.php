<?php

namespace PhpTree\Parser;

use PhpParser\Error;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpTree\Parser\Data\RawClassData;
use RuntimeException;
use function file_get_contents;


/**
 * @internal
 */
final class ClassExtractorVisitor extends NodeVisitorAbstract
{
    private ?RawClassData $rawClassData = null;
    private string $currentNamespace = '';

    public function __construct(private readonly string $filePath) {}

    public function enterNode(Node $node): null|int|Node
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->currentNamespace = $node->name !== null
                ? $node->name->toString()
                : '';

            return null;
        }

        if ($node instanceof Class_) {
            $name = $node->name?->toString() ?? '';
            $fqcn = $this->currentNamespace !== ''
                ? $this->currentNamespace . '\\' . $name
                : $name;

            $extends = null;
            if ($node->extends !== null) {
                $extends = $node->extends->toString();
            }

            $implements = array_map(
                fn(Node\Name $n): string => $n->toString(),
                $node->implements,
            );

            $this->rawClassData = new RawClassData(
                name: $name,
                fqcn: $fqcn,
                namespace: $this->currentNamespace,
                filePath: $this->filePath,
                type: 'class',
                isAbstract: $node->isAbstract(),
                isFinal: $node->isFinal(),
                extends: $extends,
                implements: $implements,
            );

            return null;
        }

        if ($node instanceof Interface_) {
            $name = $node->name->toString();
            $fqcn = $this->currentNamespace !== ''
                ? $this->currentNamespace . '\\' . $name
                : $name;

            $extends = array_map(
                fn(Node\Name $n): string => $n->toString(),
                $node->extends,
            );

            $this->rawClassData = new RawClassData(
                name: $name,
                fqcn: $fqcn,
                namespace: $this->currentNamespace,
                filePath: $this->filePath,
                type: 'interface',
                isAbstract: false,
                isFinal: false,
                extends: $extends !== [] ? implode(', ', $extends) : null,
                implements: [],
            );

            return null;
        }

        if ($node instanceof Trait_) {
            $name = $node->name->toString();
            $fqcn = $this->currentNamespace !== ''
                ? $this->currentNamespace . '\\' . $name
                : $name;

            $this->rawClassData = new RawClassData(
                name: $name,
                fqcn: $fqcn,
                namespace: $this->currentNamespace,
                filePath: $this->filePath,
                type: 'trait',
                isAbstract: false,
                isFinal: false,
                extends: null,
                implements: [],
            );

            return null;
        }

        if ($node instanceof Enum_) {
            $name = $node->name->toString();
            $fqcn = $this->currentNamespace !== ''
                ? $this->currentNamespace . '\\' . $name
                : $name;

            $implements = array_map(
                fn(Node\Name $n): string => $n->toString(),
                $node->implements,
            );

            $this->rawClassData = new RawClassData(
                name: $name,
                fqcn: $fqcn,
                namespace: $this->currentNamespace,
                filePath: $this->filePath,
                type: 'enum',
                isAbstract: false,
                isFinal: false,
                extends: null,
                implements: $implements,
            );

            return null;
        }

        return null;
    }

    public function getRawClassData(): ?RawClassData
    {
        return $this->rawClassData;
    }
}