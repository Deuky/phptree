<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpTree\Trait\Constant;
use PhpParser\Node\Name;
use function array_map;

class EnumNodeNormalizer extends AbstractObjectNodeNormalizer
{
	use Constant;

	protected function initImplements(): array
	{
		return array_map(
            fn(Name $n): string => $n->toString(),
            $this->node->implements,
        );
	}
}