<?php

namespace PhpTree\Presenter;

use PhpTree\Interface\PresenterInterface;
use PhpTree\Interface\WriterInterface;
use PhpTree\Serializer\Normalizer\NodeNormalizer;
use PhpTree\Serializer\Normalizer\Node\MethodNodeNormalizer;
use PhpTree\Serializer\Normalizer\Node\ParameterNodeNormalizer;

class MarkdownPresenter implements PresenterInterface
{
    public function __construct(
        public readonly string $maskPath,
        public readonly WriterInterface $writer,
    ) {}

    public function render(array $nodes): void
    {
        $classCount  = count($nodes);
        $methodCount = array_sum(
            array_map(fn(NodeNormalizer $n): int => count($n->methods), $nodes)
        );

        // Groupement par namespace
        $byNamespace = [];
        foreach ($nodes as $node) {
            $byNamespace[$node->namespace][] = $node;
        }
        ksort($byNamespace);

        $byNamespaceKeys = array_keys($byNamespace);

        // Header global
        $this->writer->write(
            '# PHPTree — ' . $this->relativePath($this->maskPath),
            '> Généré le ' . date('Y-m-d') . ' | ' . $classCount . ' classe(s) | ' . $methodCount . ' méthode(s)',
            '',

            '## Sommaire',
            '',
            array_reduce(
                $byNamespaceKeys,
                function($carry, $key) use ($byNamespace){
                    $value = $byNamespace[$key];

                    $nsAnchor = $this->anchor($key ?: '(global)');
                    $carry[]  = '- [' . ($key ?: '(global)') . '](#' . $nsAnchor . ')';
                    foreach ($value as $node) {
                        $classAnchor = $this->anchor($node->name);
                        $carry[]     = '  - [' . $node->name . '](#' . $classAnchor . ')';
                    }

                    return $carry;
                },
                [],
            ),

            '',
            '---',
            '',

            array_reduce(
                $byNamespaceKeys,
                function($carry, $key) use ($byNamespace){
                    $value = $byNamespace[$key];

                    $carry[] = '## ' . ($key ?: '(global)');
                    $carry[] = '';


                    foreach ($value as $node) {
                        $carry[] = $this->renderClass($node);
                    }

                    $carry[] = '---';
                    $carry[] = '';

                    return $carry;
                },
                [],
            ),


        );
    }

    private function renderClass(NodeNormalizer $node): array
    {
        $lines = [];

        // Titre classe
        $lines[] = '### ' . $node->name;

        // Description
        if ($node->description !== null && $node->description !== '') {
            $lines[] = '> ' . strtr($node->description, [PHP_EOL => "<br>"]);
        }

        // Badge type + modificateurs
        $lines[] = '';
        $lines[] = $this->renderClassBadge($node);
        $lines[] = '';

        // Tableau méthodes
        if ($node->methods) {
            $lines[] = '| Méthode | Visibilité | Paramètres | Retour | Throws | Description |';
            $lines[] = '|---|---|---|---|---|---|';
            foreach ($node->methods as $method) {
                $lines[] = $this->renderMethodRow($method);
            }
        } else {
            $lines[] = '_Aucune méthode._';
        }

        $lines[] = '';

        return $lines;
    }

    private function renderClassBadge(NodeNormalizer $node): string
    {
        $parts = [];

        $typeLabel = '';
        if ($node->isAbstract) {
            $typeLabel .= 'abstract ';
        }
        if ($node->isFinal) {
            $typeLabel .= 'final ';
        }
        $typeLabel .= $node->type;
        $parts[] = '`' . $typeLabel . '`';

        // Extends
        if (!empty($node->extends)) {
            $parts[] = 'extends `' . $node->extends . '`';
        }

        // Implements
        if (!empty($node->implements)) {
            $implList = implode('`, `', $node->implements);
            $parts[]  = 'implements `' . $implList . '`';
        }

        return implode(' | ', $parts);
    }

    private function renderMethodRow(MethodNodeNormalizer $method): string
    {
        $name        = '`' . $method->name . '`' . ($method->isStatic ? ' _(static)_' : '');
        $visibility  = $method->visibility;
        $params      = $this->renderParameters($method->parameters);
        $returnType  = trim($method->returnType)
            ? '`' . strtr($method->returnType, ['|' => '\|']) . '`'
            : '';
        $throws      = !empty($method->throws)
            ? implode(', ', array_map(fn(string $t): string => '`' . $t . '`', $method->throws))
            : '';
        $description = $method->description ?? '';

        // Échappement des pipes Markdown dans les cellules
        $params      = str_replace('|', '\\|', $params);
        $description = str_replace('|', '\\|', strtr($description, [PHP_EOL => '<br>']));

        return '| ' . implode(' | ', [
            $name,
            $visibility,
            $params,
            $returnType,
            $throws,
            $description,
        ]) . ' |';
    }

    /**
     * test
     */
    private function renderParameters(array $parameters): string
    {
        if (empty($parameters)) {
            return '';
        }

        $parts = array_map(function (ParameterNodeNormalizer $param): string {
            $type     = (string) $param->type ?: '';
            $name     = $param->name;
            $nullable = $param->isNullable && !str_starts_with($type, '?') ? 'null|' : '';
            $allTypes = explode('|', $nullable . $type);
            $allTypes = implode('|', array_unique($allTypes));
            $fullType = '`'.($allTypes ?: "mixed").'`';

            $sig = trim($fullType. ' ' . $name);

            if ($param->hasDefault) {
                $sig .= ' = ' . (
                    $param->defaultValue === null ? '' :
                    match($param->defaultValue) {
                        "''" => '<font color="orange">`empty`</font>',
                        'null' => '<font color="red">`null`</font>',
                        default => '`'.$param->defaultValue.'`'
                    }
                );
            }

            return $sig;
        }, $parameters);

        return implode('<br>', $parts);
    }

    private function relativePath(string $absolutePath): string
    {
        $base = rtrim($this->maskPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        return str_starts_with($absolutePath, $base)
            ? substr($absolutePath, strlen($base))
            : basename($absolutePath);
    }

    /**
     * Génère une ancre Markdown compatible GitHub :
     * lowercase, espaces → tirets, suppression des caractères non alphanumériques (sauf tirets et backslash converti).
     */
    private function anchor(string $text): string
    {
        $text = strtolower($text);
        $text = str_replace(['\\', '_'], '-', $text);
        $text = preg_replace('/[^a-z0-9\-]/', '', $text);
        $text = preg_replace('/-+/', '-', $text);
        return trim($text, '-');
    }
}