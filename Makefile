
RM = rm -rf
CHMOD = chmod
MKDIR = mkdir -p
VENDOR = vendor
PHPCS = vendor/bin/phpcs
PHPCS_STANDARD = vendor/thefox/phpcsrs/Standards/TheFox
PHPCS_REPORT = --report=full --report-width=160
PHPUNIT = vendor/bin/phpunit
COMPOSER = ./composer.phar
COMPOSER_DEV ?= --dev


.PHONY: all install test test_phpcs test_phpunit test_phpunit_cc test_clean release clean clean_release clean_all

all: install test

install: $(VENDOR) config.sh

install_release: $(COMPOSER)
	$(MAKE) install COMPOSER_DEV=--no-dev

update: $(COMPOSER)
	$(COMPOSER) selfupdate
	$(COMPOSER) update

test: test_phpcs test_phpunit

test_phpcs: $(PHPCS) $(PHPCS_STANDARD)
	$(PHPCS) -v -s -p $(PHPCS_REPORT) --standard=$(PHPCS_STANDARD) src tests bootstrap.php

test_phpunit: $(PHPUNIT) phpunit.xml test_data test_data/test_prog
	TEST=true $(PHPUNIT) $(PHPUNIT_COVERAGE_HTML) $(PHPUNIT_COVERAGE_XML) $(PHPUNIT_COVERAGE_CLOVER)
	#$(MAKE) test_clean

test_phpunit_cc: build
	$(MAKE) test_phpunit PHPUNIT_COVERAGE_HTML="--coverage-html build/report"

test_clean:
	$(RM) test_data

release: release.sh
	./release.sh

clean: test_clean
	$(RM) composer.lock $(COMPOSER)
	$(RM) vendor/*
	$(RM) vendor

clean_data:
	$(RM) data/*
	$(RM) data

clean_release: clean_data
	$(RM) composer.lock $(COMPOSER)
	$(RM) log pid

clean_all: clean clean_data clean_release

$(VENDOR): $(COMPOSER)
	$(COMPOSER) install $(COMPOSER_PREFER_SOURCE) --no-interaction $(COMPOSER_DEV)

$(COMPOSER):
	curl -sS https://getcomposer.org/installer | php
	$(CHMOD) 755 $(COMPOSER)

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
	$(CHMOD) 0700 build

config.sh:
	echo "#!/usr/bin/env bash" > $@
	echo "NM="$(shell which nm) >> $@
	echo "OTOOL="$(shell which otool) >> $@
	./config.check.sh
