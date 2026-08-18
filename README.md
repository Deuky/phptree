# phptree
**PHP CLI that scans a directory (or a file) and generates a tree of features**: classes, methods, functions, etc., extracted through static analysis of the source code.

## How it works
`phptree` recursively traverses a PHP directory, parses each file with [nikic/php-parser](https://github.com/nikic/PHP-Parser) (AST analysis, no code execution), then outputs the extracted structure (classes, methods, functions...) as a tree, in the output format of your choice.

The CLI is built with [Symfony Console](https://symfony.com/doc/current/components/console.html) and is distributed as a standalone PHAR executable (`phptree.phar`) or as a Docker image.

## Installation

### Via Composer
```bash
composer require phptree/phptree
./vendor/bin/phptree scan src/
```

### Via the compiled PHAR
```bash
php build.php          # generates phptree.phar
chmod +x phptree.phar
./phptree.phar scan src/
```

### Via Docker
```bash
docker pull ghcr.io/deuky/phptree/unit-alpine:8.4-0.0.2
```

## Usage
```bash
phptree scan <directory> [options]
```
The default command is `scan` (so you can also simply write `phptree <directory>`).

### Argument
| Argument | Description |
|---|---|
| `directory` | Directory or PHP file to scan (required) |

### Options
| Option | Shortcut | Description |
|---|---|---|
| `--relative=<path>` | | Root directory used to generate relative paths in the output |
| `--format=<fmt>` | `-f` | Output format: `console`, `json` (default), `markdown`, `sqlite`, `html`, `csv` |
| `--output=<file>` | `-o` | Writes the result to a file instead of standard output |
| `--exclude=<dirs>` | | Directories to exclude, comma-separated |
| `--quiet` | `-q` | Hides warnings (docblock inconsistencies, non-fatal errors) |

### Memory Usage Limitation
To increase the memory_limit, use the PHP arguments directly:
```bash
php -d memory_limit=<size> phptree ...
```

### Examples
```bash
# Simple scan, JSON output on stdout
phptree scan src/
# Markdown output to a file, with relative paths
phptree scan src/ --format markdown --relative . --output tree.md
# Exclude test and vendor folders
phptree scan . --exclude tests,vendor
```

## Output formats
- **json** *(default)*: structure usable by other tools

## Docker
The `Dockerfile` defines several build targets (`base`, `skeleton`, `develop`, `source`, `unit`, `artifact`, `unit-alpine`), driven via `docker-compose.yml` and the `Makefile`. The `unit-alpine` target produces a minimal image (PHP Alpine) containing only the compiled `phptree` binary, without the source code or development dependencies — ideal for use in CI/CD or production.

## Tech stack
- PHP ^8.3
- `symfony/console` ^7.0 — CLI interface
- `nikic/php-parser` ^5.0 — static analysis / AST
- Tests: `pestphp/pest` ^3.0
- Distribution: PHAR (via `build.php`) or Docker image

## License
MIT