PHP_EXEC ?= php
COMPOSER_EXEC ?= composer
OUTPUT ?= /usr/bin/phptree

build: vendor

compile: phptree.phar

install: /usr/bin/phptree

phptree.phar:
	$(PHP_EXEC) -d phar.readonly=0 build.php $(OUTPUT);

${OUTPUT}:
	chmod 755 phptree.phar;
	mv phptree.phar $@;

vendor:
	$(COMPOSER_EXEC) install --no-scripts