<?php

namespace PhpTree\Enum;

use PhpParser\Node\Stmt;

enum NodeTypeEnum: string
{
	const MAPPER = [
		Stmt\Class_::class => "class",
		Stmt\Trait_::class => "trait"
	];

	case _trait = Stmt\Trait_::class;
	case _class = Stmt\Class_::class;

	public static function fromMapper($key): static 
	{
		return static::try(static::MAPPER[$key]);
	}
}