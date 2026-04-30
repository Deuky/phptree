<?php

namespace PhpTree\Parser;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\EnumCase;
use PhpParser\NodeVisitorAbstract;
use RuntimeException;
use PhpTree\Internal\FileGetContents;
use PhpTree\Serializer\Normalizer\NodeNormalizer;


class ClassExtractorVisitor extends NodeVisitorAbstract
{
    private RawClassData|NodeNormalizer|null $rawClassData = null;
    private string $currentNamespace = '';

    public function __construct(private readonly FileGetContents $file) {}

    /**
     * @see PhpParser\NodeVisitor 
     */
    public function enterNode(Node $node): null
    {
        $this->rawClassData ??= match($node::class) {
            Namespace_::class => $this->namespaceNode($node),
            Class_::class,
            Interface_::class,
            Trait_::class,
            Enum_::class => new NodeNormalizer(
                                $node, 
                                file: $this->file, 
                                namespace: $this->currentNamespace
                            ),
            Use_::class,
            Return_::class,
            ClassMethod::class,
            EnumCase::class,
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

    protected function namespaceNode(Namespace_ $node)
    {
        $this->currentNamespace = (string) $node->name;

        return null;
    }

    public function getRawClassData(): NodeNormalizer|RawClassData|null
    {
        return $this->rawClassData;
    }
}