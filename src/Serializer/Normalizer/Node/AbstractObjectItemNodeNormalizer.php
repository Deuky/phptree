<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node;
use PhpTree\Resolver\VisibilityResolver;

abstract class AbstractObjectItemNodeNormalizer extends AbstractNodeNormalizer
{
    public readonly ?string $visibility;

    public function __construct(
        ...$args
    )
    {
        parent::__construct(...$args);

        $this->visibility 	= VisibilityResolver::resolve($this->node);
    }
}
