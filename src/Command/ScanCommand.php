<?php

namespace PhpTree\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PhpTree\Scanner\DirectoryScanner;
use PhpTree\Parser\PhpFileParser;
use PhpTree\Resolver\ListResolver;
use InvalidArgumentException;
use PhpTree\Internal\FileGetContents;

#[AsCommand(
    name: 'scan',
    description: 'Scanne un répertoire PHP et génère un arbre de fonctionnalités',
)]
class ScanCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument(
                name: 'directory',
                mode: InputArgument::REQUIRED,
                description: 'Répertoire à scanner',
            )
            ->addOption(
                name: 'format',
                shortcut: 'f',
                mode: InputOption::VALUE_REQUIRED,
                description: 'Format de sortie (console, json, markdown, sqlite, html, csv)',
                default: 'console',
            )
            ->addOption(
                name: 'output',
                shortcut: 'o',
                mode: InputOption::VALUE_REQUIRED,
                description: 'Fichier de sortie (optionnel)',
            )
            ->addOption(
                name: 'exclude',
                mode: InputOption::VALUE_REQUIRED,
                description: 'Répertoires à exclure, séparés par des virgules',
            )
            ->addOption(
                name: 'quiet',
                shortcut: 'q',
                mode: InputOption::VALUE_NONE,
                description: 'Mute les avertissements (incohérences docblock, erreurs non fatales)',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $directory = $input->getArgument('directory');
        $quiet     = (bool) $input->getOption('quiet');
        $excludes  = array_map(
            fn($exclude) => ltrim($exclude, ".".DIRECTORY_SEPARATOR),
            ListResolver::resolve($input->getOption('exclude'))
        );

        $realPathDirectory = realpath($directory);

        if ($realPathDirectory === false || !is_dir($realPathDirectory)) {
            throw new InvalidArgumentException(
                sprintf('Répertoire invalide ou introuvable : %s', $directory),
            );
        }

        $scanner = new DirectoryScanner(excludes: $excludes);
        $files = $scanner->scan($realPathDirectory);
        $cFiles = 0;

        $parser      = new PhpFileParser();
        $nodes       = [];
        $parseErrors = [];

        try {
            foreach ($files as $file) {
                $cFiles ++;
                $node = $parser->parse(new FileGetContents($file->getPathname()));
                if ($node !== null) {
                    $nodes[] = $node;
                }
            }
        } catch (Throwable $t){
            if (!$quiet) {
                $output->getErrorOutput()->writeln(sprintf('<comment>[warning] %s</comment>', $t->message));
            }
        }

        $output->writeln(sprintf(
            '<info>Scan terminé : %d fichier(s), %d classe(s) extraite(s)</info>',
            $cFiles,
            count($nodes),
        ));

        foreach ($nodes as $nodeNormalizer) {
            $output->writeln(sprintf('  [%s] %s', $nodeNormalizer->type, $nodeNormalizer->fqcn));
        }

        return Command::SUCCESS;
    }
}