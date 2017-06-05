#!/usr/bin/env bash

set -e
SCRIPT_BASEDIR=$(dirname "$0")
UNAME=$(uname -s)


cd "$SCRIPT_BASEDIR"

if [ "$UNAME" = "Darwin" ]; then
	if [ -f ./config.sh ]; then
		. ./config.sh
		
		if [[ "$NM" = "" ]]; then
			echo "ERROR: 'nm' not found"
			exit 1
		fi
		if [[ "$OTOOL" = "" ]]; then
			echo "ERROR: 'otool' not found"
			exit 1
		fi
	else
		exit 1
	fi
fi
