<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node;
use PhpTree\Resolver\DocBlockResolver;
use PhpTree\Resolver\TypeResolver;

abstract class AbstractNodeNormalizer
{
    public readonly string $name;
    public readonly ?string $description;
    public readonly ?string $type;
    
    public function __construct(
        public readonly Node $node, 
    )
    {
    	$this->name 		= $this->initName();
    	$this->description 	= $this->initDescription();
        $this->type         = $this->initType();
    }

    protected function initDescription(): ?string
    {
        return DocBlockResolver::extractDescription($this->node);
    }

    protected function initName(): string
    {
        return (string) $this->node->name;
    }

    protected function initType(): ?string
    {
    	return TypeResolver::resolve($this->node);
    }
}