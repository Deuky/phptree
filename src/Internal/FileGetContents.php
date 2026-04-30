<?php

namespace PhpTree\Internal;

use function file_get_contents;

class FileGetContents
{
	public function __construct(public readonly string $fileName){}

	public function __toString(): string
	{
		$content = file_get_contents($this->fileName);

        if ($content === false) {
            throw new \RuntimeException(sprintf('Impossible de lire le fichier : %s', $this->fileName));
        }

		return $content;
	}
}