<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Param;
use PhpTree\Resolver\DocBlockResolver;

/**
 * @property ClassMethod $node
 */
class MethodNodeNormalizer extends AbstractObjectItemNodeNormalizer
{
    public readonly bool $isStatic;
    public readonly bool $isAbstract;
    public readonly bool $isConstructor;
    public readonly TypeNodeNormalizer $returnType;
    public readonly array $parameters;
    public readonly ?string $description;
    public readonly array $throws;

    public function __construct(
        ClassMethod $node
    )
    {
        parent::__construct($node);
        $this->isStatic   = $node->isStatic();
        $this->isAbstract = $node->isAbstract();
        $this->isConstructor = $this->name === '__construct';

        $this->returnType = new TypeNodeNormalizer($node->returnType);
        $this->parameters = $this->initParameters();

        $this->description = DocBlockResolver::extractDescription($node);
        $this->throws      = DocBlockResolver::extractThrows($node);
    }

    protected function initParameters(): array
    {
        return array_map(
            fn(Param $param): ParameterNodeNormalizer => new ParameterNodeNormalizer($param),
            $this->node->params,
        );
    }
}