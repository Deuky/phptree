<?php

namespace PhpTree\Parser;

use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\NodeVisitorAbstract;
use RuntimeException;
use PhpTree\Internal\FileGetContents;
use PhpTree\Serializer\Normalizer\NodeNormalizer;


class ClassExtractorVisitor extends NodeVisitorAbstract
{
    private NodeNormalizer|null $nodeData = null;
    private string $currentNamespace = '';
    private array $useStatement = [];

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
            Stmt\Class_::class,
            Stmt\Interface_::class,
            Stmt\Trait_::class,
            Stmt\Enum_::class => new NodeNormalizer(
                                    $node, 
                                    file: $this->file, 
                                    namespace: $this->currentNamespace
                                ),
            default => null
        };

        return null;
    }

    protected function useNode(Stmt\Use_ $node)
    {
        foreach ($node->uses as $use){
            
        }

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