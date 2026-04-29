<?php
 
namespace PhpTree\Parser\Data;
 
/**
 * Données brutes extraites par le parser AST pour une classe.
 * Sera transformée en ClassNode en Phase 2.
 */
final readonly class RawClassData
{
    public function __construct(
        public string $name,
        public string $fqcn,
        public string $namespace,
        public string $filePath,
        public string $type,        // class | interface | trait | enum
        public bool $isAbstract,
        public bool $isFinal,
        public ?string $extends,
        public array $implements,   // string[]
        public array $methods = [], // RawMethodData[] — rempli en tâche 1.3
        public array $uses = [],    // string[] use statements — rempli en tâche 1.3
        public ?string $description = null, // rempli en tâche 1.4
    ) {}
}
 