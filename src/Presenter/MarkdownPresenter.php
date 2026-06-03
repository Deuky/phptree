<?php

namespace PhpTree\Presenter;

use PhpTree\Serializer\Normalizer\NodeNormalizer;
use PhpTree\Serializer\Normalizer\Node\MethodNodeNormalizer;
use PhpTree\Serializer\Normalizer\Node\ParameterNodeNormalizer;

class MarkdownPresenter extends AbstractPresenter
{

    public function render(NodeNormalizer ...$nodes): void
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
            '# PHPTree — ' . basename($this->maskPath),
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
        $lines[] = '### ' . $node->name . ' <font size=3 style="float: right">'. $this->renderClassBadge($node).'</font>';

        // Extends
        if ($node->extends ?? null) {
            $lines[] = '`' . $node->extends . '`';
        }

        // Implements
        if ($node->implements ?? null) {
            $implList = implode('` `', $node->implements);
            $lines[]  = '`' . $implList . '`';
        }

        // Description
        if ($node->description !== null && $node->description !== '') {
            $lines[] = '> ' . strtr($node->description, [PHP_EOL => "<br>"]);
        }

        // Badge type + modificateurs
        $lines[] = '';

        if ($node->constants) {
            $lines[] = '| Constant | Type | Visibilité | Valeur | Description |';
            $lines[] = '|---|---|---|---|---|';
            foreach ($node->constants as $constant) {
                $lines[] = implode(' | ', [
                    $constant->name,
                    trim($constant->type) ?: '<font color="blue">`mixed`</font>',
                    $constant->visibility,
                    $constant->value,
                    $constant->description ?? null
                ]);
            }
            $lines[] = '';
            $lines[] = '';
        }

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

        $typeLabel = [];
        if ($node->isAbstract) {
            $typeLabel[] = '<font color=orange>`abstract`</font>';
        }

        if ($node->isFinal) {
            $typeLabel[] = '<font color=orange>`final`</font>';
        }

        $typeLabel[] = match($node->type) {
            'enum' => '<font color=green>`'.$node->type.'`</font>',
            'trait' => '<font color=orange>`'.$node->type.'`</font>',
            'interface' => '<font color=blue>`'.$node->type.'`</font>',
            'class' => '<font color=black>`'.$node->type.'`</font>',
            default => '`'.$node->type.'`'
        };
        $parts[] = implode (' ', $typeLabel);

        return implode(' | ', $parts);
    }

    private function renderMethodRow(MethodNodeNormalizer $method): string
    {
        $name        = $method->name . ' '. ($method->isStatic ? ' _(static)_' : '');
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
            $fullType = ($allTypes ? '`'.$allTypes.($param->isVariadic ? '[]' :'').'`': '<font color="blue">`mixed`</font>');

            $sig = trim($fullType . ' ' . $name);

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