<?php

namespace PhpTree\Serializer\Normalizer;

use PhpParser\Node as NodeInterface;
use PhpParser\Node\Stmt;

class NodeNormalizer
{
	const array NODE_CLASS_MAPPER = [
		Stmt\Class_::class => Node\ClassNodeNormalizer::class,
		Stmt\Trait_::class => Node\TraitNodeNormalizer::class,
		Stmt\Interface_::class => Node\InterfaceNodeNormalizer::class,
		Stmt\Enum_::class => Node\EnumNodeNormalizer::class,
	];

	protected array $normalizer;
	public readonly Node\AbstractNodeNormalizer $nodeNormalizer;
	
	public function __construct(
		public readonly NodeInterface $node,
		...$args
	)
	{
        $normalizerClassName = $this::NODE_CLASS_MAPPER[$node::class];
        $this->nodeNormalizer = new $normalizerClassName(
        	$node, 
        	...$args
        );
	}

	public function __get(string $name): mixed
	{
		return $this->nodeNormalizer->{$name};
	}
}
