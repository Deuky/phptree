# phptree

**CLI PHP qui scanne un répertoire (ou un fichier) et génère un arbre de fonctionnalités** : classes, méthodes, fonctions, etc., extraits par analyse statique du code source.

## Fonctionnement

`phptree` parcourt récursivement un répertoire PHP, parse chaque fichier avec [nikic/php-parser](https://github.com/nikic/PHP-Parser) (analyse AST, pas d'exécution du code), puis restitue la structure extraite (classes, méthodes, fonctions...) sous forme d'un arbre, dans le format de sortie de votre choix.

Le CLI est construit avec [Symfony Console](https://symfony.com/doc/current/components/console.html) et se distribue sous forme d'un exécutable PHAR autonome (`phptree.phar`) ou d'une image Docker.

## Installation

### Via Composer

```bash
composer require phptree/phptree
./vendor/bin/phptree scan src/
```

### Via le PHAR compilé

```bash
php build.php          # génère phptree.phar
chmod +x phptree.phar
./phptree.phar scan src/
```

### Via Docker

```bash
docker pull ghcr.io/deuky/phptree/unit-alpine:8.4-0.0.2
```

## Utilisation

```bash
phptree scan <directory> [options]
```

La commande par défaut est `scan` (vous pouvez donc aussi écrire simplement `phptree <directory>`).

### Argument

| Argument | Description |
|---|---|
| `directory` | Répertoire ou fichier PHP à scanner (obligatoire) |

### Options

| Option | Raccourci | Description |
|---|---|---|
| `--relative=<path>` | | Répertoire racine utilisé pour générer des chemins relatifs en sortie |
| `--format=<fmt>` | `-f` | Format de sortie : `console`, `json` (défaut), `markdown`, `sqlite`, `html`, `csv` |
| `--output=<file>` | `-o` | Écrit le résultat dans un fichier au lieu de la sortie standard |
| `--exclude=<dirs>` | | Répertoires à exclure, séparés par des virgules |
| `--quiet` | `-q` | Masque les avertissements (incohérences de docblock, erreurs non fatales) |

### Exemples

```bash
# Scan simple, sortie JSON sur stdout
phptree scan src/

# Sortie Markdown dans un fichier, avec chemins relatifs
phptree scan src/ --format markdown --relative . --output arbre.md

# Exclure les dossiers de tests et vendor
phptree scan . --exclude tests,vendor
```

## Formats de sortie

- **console** : affichage lisible dans le terminal
- **json** *(défaut)* : structure exploitable par d'autres outils
- **markdown** : documentation prête à intégrer
- **html** : rapport navigable
- **csv** : export tabulaire
- **sqlite** : base de données interrogeable

## Docker

Le `Dockerfile` définit plusieurs cibles de build (`base`, `skeleton`, `develop`, `source`, `unit`, `artifact`, `unit-alpine`), pilotées via `docker-compose.yml` et le `Makefile`. La cible `unit-alpine` produit une image minimale (PHP Alpine) ne contenant que le binaire `phptree` compilé, sans le code source ni les dépendances de développement — idéale pour une utilisation en CI/CD ou en production.

## Stack technique

- PHP ^8.3
- `symfony/console` ^7.0 — interface CLI
- `nikic/php-parser` ^5.0 — analyse statique / AST
- Tests : `pestphp/pest` ^3.0
- Distribution : PHAR (via `build.php`) ou image Docker

## Licence

MIT
