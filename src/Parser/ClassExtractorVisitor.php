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
    private RawClassData|NodeNormalizer|null $nodeData = null;
    private string $currentNamespace = '';

    public function __construct(private readonly FileGetContents $file) {}

    /**
     * @see PhpParser\NodeVisitor 
     */
    public function enterNode(Node $node): null
    {
        $this->nodeData ??= match($node::class) {
            Stmt\Namespace_::class => $this->namespaceNode($node),
            Stmt\Class_::class,
            Stmt\Interface_::class,
            Stmt\Trait_::class,
            Stmt\Enum_::class => new NodeNormalizer(
                                $node, 
                                file: $this->file, 
                                namespace: $this->currentNamespace
                            ),
            Stmt\Use_::class,
            Stmt\Return_::class,
            Stmt\ClassMethod::class,
            Stmt\EnumCase::class,
            Node\Scalar\String_::class,
            Node\Name\FullyQualified::class,
            Node\Expr\New_::class,
            Node\Name::class,
            Node\Param::class,
            Node\Expr\Variable::class,
            Node\Expr\ConstFetch::class,
            Node\UseItem::class,
            Node\Identifier::class => null,
            default => throw new \Exception('node not allow : '.$node::class)
        };

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