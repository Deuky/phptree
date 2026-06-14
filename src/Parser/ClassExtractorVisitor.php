<?php

namespace PhpTree\Parser;

use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\NodeVisitorAbstract;
use RuntimeException;
use PhpTree\Internal\FileGetContents;
use PhpTree\Serializer\Normalizer\NodeNormalizer;
use PhpParser\Node\UseItem;


class ClassExtractorVisitor extends NodeVisitorAbstract
{
    private NodeNormalizer|null $nodeData = null;
    private string $currentNamespace = '';
    private array $useStatement = ["class" => [], "function" => []];

    public function __construct(
        private readonly FileGetContents $file
    ) {}

    /**
     * @see PhpParser\NodeVisitor 
     */
    public function enterNode(Node $node): null
    {
        $this->nodeData ??= match($node::class) {
            //Stmt\Use_::class  => $this->useNode($node),
            Stmt\Namespace_::class  => $this->namespaceNode($node),
            Stmt\Use_::class => $this->useNode($node),
            Stmt\Class_::class,
            Stmt\Interface_::class,
            Stmt\Trait_::class,
            Stmt\Enum_::class => new NodeNormalizer(
                                    $node, 
                                    file: $this->file, 
                                    namespace: $this->currentNamespace,
                                    useClasses: $this->useStatement['class'],
                                    useFunctions: $this->useStatement['function']
                                ),
            default => null
        };

        return null;
    }

    protected function useNode(Stmt\Use_ $node)
    {
        $type = match($node->type) {
            1 => "class",
            2 => "function",
            default => throw new \UnexpectedValueException()
        };

        $this->useStatement[$type] = array_reduce(
            $node->uses,
            function(array $c, UseItem $i) use ($type) {
                $name = $i->name->__toString();
                $c[$name] = $i->alias->name ?? null;
                return $c;
            },
            $this->useStatement[$type]
        );

        return null;
    }

    protected function namespaceNode(Stmt\Namespace_ $node)
    {
        $this->currentNamespace = (string) $node->name;

        return null;
    }

    public function getNodeData(): NodeNormalizer|null
    {
        return $this->nodeData;
    }
}