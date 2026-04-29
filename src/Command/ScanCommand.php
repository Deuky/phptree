<?php

namespace PhpTree\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'scan',
    description: 'Scanne un répertoire PHP et génère un arbre de fonctionnalités',
)]
final class ScanCommand extends Command
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
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $directory = $input->getArgument('directory');

        $output->writeln(sprintf('<info>Scanning: %s</info>', $directory));
        $output->writeln('<comment>Phase 1 — WIP</comment>');

        return Command::SUCCESS;
    }
}