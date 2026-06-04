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
    public readonly bool $static;
    public readonly bool $abstract;
    public readonly bool $constructor;
    public readonly array $parameters;
    public readonly array $throws;

    public function __construct(
        ClassMethod $node
    )
    {
        parent::__construct($node);
        $this->static   = $node->isStatic();
        $this->abstract = $node->isAbstract();
        $this->constructor = $this->name === '__construct';

        $this->parameters = $this->initParameters();

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