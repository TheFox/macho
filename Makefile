
CFLAGS = -Wall -O3
RM = rm -rf
CHMOD = chmod
MKDIR = mkdir -p
VENDOR = vendor
PHPCS = vendor/bin/phpcs
PHPCS_STANDARD = vendor/thefox/phpcsrs/Standards/TheFox
PHPCS_OPTIONS = -v -s --colors --report=full --report-width=160 --standard=$(PHPCS_STANDARD)
PHPUNIT = vendor/bin/phpunit
PHPUNIT_TESTSUITE ?= osx
COMPOSER = ./composer.phar
COMPOSER_OPTIONS ?= --no-interaction

# Local installed PHPStan while supporting PHP 5.
# PHPStan requires PHP 7.
PHPSTAN = ~/.composer/vendor/bin/phpstan


.PHONY: all
all: install test examples/example1 examples/example2

.PHONY: install
install: $(VENDOR) config.sh

.PHONY: update
update: $(COMPOSER)
	$(COMPOSER) selfupdate
	$(COMPOSER) update

.PHONY: test
test: test_phpcs test_phpunit

.PHONY: test_phpstan
test_phpstan:
	$(PHPSTAN) analyse --level 5 --no-progress src tests

.PHONY: test_phpcs
test_phpcs: $(PHPCS) $(PHPCS_STANDARD)
	$(PHPCS) $(PHPCS_OPTIONS) src tests *.php

.PHONY: test_phpunit
test_phpunit: $(PHPUNIT) phpunit.xml test_data test_data/test_prog
	$(PHPUNIT) --testsuite $(PHPUNIT_TESTSUITE) $(PHPUNIT_OPTIONS)
	$(MAKE) test_clean

.PHONY: test_phpunit_cc
test_phpunit_cc: build
	$(MAKE) test_phpunit PHPUNIT_OPTIONS="--coverage-html build/report"

.PHONY: test_clean
test_clean:
	$(RM) test_data

.PHONY: clean
clean: test_clean
	$(RM) composer.lock $(COMPOSER) $(VENDOR)

.PHONY: clean_data
clean_data:
	$(RM) data

.PHONY: clean_all
clean_all: clean clean_data

$(VENDOR): $(COMPOSER)
	$(COMPOSER) install $(COMPOSER_OPTIONS)

$(COMPOSER):
	curl -sS https://getcomposer.org/installer | php
	$(CHMOD) u=rwx,go=rx $(COMPOSER)

$(PHPCS): $(VENDOR)

$(PHPUNIT): $(VENDOR)

test_data:
	$(MKDIR) test_data

test_data/test_prog: tests/test_prog.c test_data
	$(CC) $< -o $@
	file $@

build:
	$(MKDIR) $@
	$(CHMOD) u=rwx,go-rwx $@

config.sh:
	echo "#!/usr/bin/env bash" > $@
	echo "NM="$(shell which nm) >> $@
	echo "OTOOL="$(shell which otool) >> $@
	./config.check.sh

examples/example1: examples/example1.o
	$(CC) $(CFLAGS) $< -o $@

examples/example1.o: examples/example1.c
	$(CC) $(CFLAGS) -c $< -o $@

examples/example2: examples/example2.o
	$(CC) $(CFLAGS) $< -o $@

examples/example2.o: examples/example1.c
	$(CC) $(CFLAGS) -c $< -o $@
