#!/usr/bin/env bash

SCRIPT_BASEDIR=$(dirname "$0")
PHPUNIT_TESTSUITE=${PHPUNIT_TESTSUITE:-osx}


cd "${SCRIPT_BASEDIR}/.."

mkdir -p tmp/test_data
make tmp/test_data/test_prog
vendor/bin/phpunit --testsuite "$PHPUNIT_TESTSUITE"
