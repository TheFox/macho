#!/usr/bin/env bash

set -e
SCRIPT_BASEDIR=$(dirname $0)


cd $SCRIPT_BASEDIR

if [ -f ./config.sh ]; then
	. ./config.sh
	#echo "nm: '$NM'"
	#echo "ot: '$OTOOL'"
	
	if [ "$NM" = "" ]; then
		echo "ERROR: 'nm' not found"
		exit 1
	fi
	if [ "$OTOOL" = "" ]; then
		echo "ERROR: 'otool' not found"
		exit 1
	fi
else
	exit 1
fi
