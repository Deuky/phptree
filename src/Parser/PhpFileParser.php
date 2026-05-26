<?php

namespace PhpTree\Parser;

use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpTree\Internal\FileGetContents;
use PhpTree\Serializer\Normalizer\NodeNormalizer;
use RuntimeException;

class PhpFileParser
{
    public function parse(FileGetContents $file): NodeNormalizer|null
    {
        $parser = (new ParserFactory())->createForNewestSupportedVersion();

        try {
            $ast = $parser->parse($file);
        } catch (Error $e) {
            throw new RuntimeException(sprintf(
                'Erreur de parsing dans %s : %s',
                $file,
                $e->getMessage(),
            ));
        }

        if ($ast === null) {
            return null;
        }

        $visitor = new ClassExtractorVisitor($file);
        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        return $visitor->getNodeData();
    }
}