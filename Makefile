
CFLAGS = -Wall -O3
RM = rm -rf
CHMOD = chmod
MKDIR = mkdir -p
VENDOR = vendor
PHPCS = vendor/bin/phpcs
PHPCS_STANDARD = vendor/thefox/phpcsrs/Standards/TheFox
PHPCS_REPORT = --report=full --report-width=160
PHPUNIT = vendor/bin/phpunit
PHPUNIT_TESTSUITE ?= osx
COMPOSER = ./composer.phar
COMPOSER_DEV ?= 
COMPOSER_INTERACTION ?= --no-interaction
COMPOSER_PREFER_SOURCE ?= 


.PHONY: all install test test_phpcs test_phpunit test_phpunit_cc test_clean release clean clean_all

all: install test examples/example1 examples/example2

install: $(VENDOR) config.sh

update: $(COMPOSER)
	$(COMPOSER) selfupdate
	$(COMPOSER) update

test: test_phpcs test_phpunit

test_phpcs: $(PHPCS) $(PHPCS_STANDARD)
	$(PHPCS) -v -s -p $(PHPCS_REPORT) --standard=$(PHPCS_STANDARD) src tests bootstrap.php

test_phpunit: $(PHPUNIT) phpunit.xml test_data test_data/test_prog
	TEST=true $(PHPUNIT) --testsuite $(PHPUNIT_TESTSUITE) $(PHPUNIT_COVERAGE_HTML) $(PHPUNIT_COVERAGE_XML) $(PHPUNIT_COVERAGE_CLOVER)
	$(MAKE) test_clean

test_phpunit_cc: build
	$(MAKE) test_phpunit PHPUNIT_COVERAGE_HTML="--coverage-html build/report"

test_clean:
	$(RM) test_data

clean: test_clean
	$(RM) composer.lock $(COMPOSER)
	$(RM) vendor/*
	$(RM) vendor

clean_data:
	$(RM) data/*
	$(RM) data

clean_all: clean clean_data

$(VENDOR): $(COMPOSER)
	$(COMPOSER) install $(COMPOSER_PREFER_SOURCE) $(COMPOSER_INTERACTION) $(COMPOSER_DEV)

$(COMPOSER):
	curl -sS https://getcomposer.org/installer | php
	$(CHMOD) a+rx-w,u+w $(COMPOSER)

$(PHPCS): $(VENDOR)

$(PHPUNIT): $(VENDOR)

test_data:
	$(MKDIR) test_data

test_data/test_prog: tests/test_prog.c test_data
	$(CC) $< -o $@
	file $@

build:
	$(MKDIR) build
	$(MKDIR) build/logs
	$(CHMOD) a-rwx,u+rwx build

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
