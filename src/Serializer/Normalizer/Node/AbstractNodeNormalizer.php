<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node;
use PhpTree\Internal\FileGetContents;
use PhpParser\Node\Stmt\ClassMethod;
use PhpTree\Resolver\DocBlockResolver;

abstract class AbstractNodeNormalizer
{
    public readonly string $name;
    public readonly ?string $description;
    public readonly TypeNodeNormalizer $type;
    
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

    protected function initType(): TypeNodeNormalizer
    {
    	return new TypeNodeNormalizer($this->node);
    }
}