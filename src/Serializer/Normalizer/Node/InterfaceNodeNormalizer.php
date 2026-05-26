<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node;
use PhpTree\Trait\Constant;

class InterfaceNodeNormalizer extends AbstractObjectNodeNormalizer
{
	use Constant;
	
	const TYPE = "interface";

	protected function initExtends(): string
	{
        return implode(', ', $this->extendsList);
	}
}