#!/usr/bin/env bash

set -e
DATE=$(date +"%Y/%m/%d %H:%M:%S")
SCRIPT_BASEDIR=$(dirname $0)
DST=debug


print_usage(){
	echo "Usage: $0 NAME PATH"
	echo "    NAME = the name of the program you want do debug"
	echo "    PATH = path to the program"
	echo
	echo "Informations about a binary will be stored to 'debug' directory."
}

cd $SCRIPT_BASEDIR
if [ -f ./config.sh ]; then
	. ./config.sh
	./config.check.sh
else
	echo "ERROR: installation failed. Run 'make install'."
	exit 1
fi

name=$1
path=$2
dst=$DST/$name

if [ "$name" = "" ]; then
	print_usage
	exit 3
fi
if [ "$path" = "" ]; then
	print_usage
	exit 3
fi

mkdir -p $dst
echo "name: $name ($dst)"
echo "path: $path"

echo "create text section debug informations"
txt=$dst/text_section.txt
$OTOOL -vVtjC $path > $txt
echo >> $txt
$OTOOL -vVjC -s __TEXT __stubs $path >> $txt
echo >> $txt
$OTOOL -vVjC -s __TEXT __cstring $path >> $txt

echo "create load commands debug informations"
txt=$dst/load_commands.txt
$OTOOL -l $path > $txt
