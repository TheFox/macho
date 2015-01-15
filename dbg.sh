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
echo

echo "create text section debug informations"
txt=$dst/text_section.txt
$OTOOL -vVtjC $path > $txt
#grep -Hn call $txt > $dst/text_section_calls.txt
#ls -lah $txt
#echo

echo "create stubs section debug informations"
txt=$dst/stubs_section.txt
$OTOOL -vVjC -s __TEXT __stubs $path > $txt
#ls -lah $txt
#echo

echo "create cstring section debug informations"
txt=$dst/cstring_section.txt
$OTOOL -vVjC -s __TEXT __cstring $path > $txt
#ls -lah $txt
#echo

echo "create ustring section debug informations"
txt=$dst/ustring_section.txt
$OTOOL -vVjC -s __TEXT __ustring $path > $txt
#ls -lah $txt
#echo

echo "create objc_methname section debug informations"
txt=$dst/objc_methname_section.txt
$OTOOL -vVjC -s __TEXT __objc_methname $path > $txt
#ls -lah $txt
#echo

echo "create objc_methtype section debug informations"
txt=$dst/objc_methtype_section.txt
$OTOOL -vVjC -s __TEXT __objc_methtype $path > $txt
#ls -lah $txt
#echo

echo "create const section debug informations"
txt=$dst/const_section.txt
$OTOOL -vVjC -s __TEXT __const $path > $txt
#ls -lah $txt
#echo

echo "create name list debug informations"
txt=$dst/name_list.txt
$NM -na $path > $txt
#ls -lah $txt
#echo



echo "create load commands debug informations"
txt=$dst/load_commands.txt
$OTOOL -l $path > $txt
#ls -lah $txt
#echo
