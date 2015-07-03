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

if [[ -d $dst ]]; then
	echo "dst '$dst' exists"
	exit 1
fi

mkdir -p $dst
echo "name: $name ($dst)"
echo "path: $path"
echo

segment=__TEXT
section=__text
echo "create text '$section' section debug informations"
txt=$dst/section-$segment-$section.txt
$OTOOL -vVtjC $path > $txt

for section in __stubs __stub_helper __cstring __ustring __objc_classname __objc_methname __objc_methtype __const __gcc_except_tab __unwind_info __eh_frame; do
	echo "create text '$section' section debug informations"
	txt=$dst/section-$segment-$section.txt
	$OTOOL -vVjC -s $segment $section $path > $txt
done

segment=__DATA
for section in __program_vars __nl_symbol_ptr __got __la_symbol_ptr __mod_init_func __pointers __const __cfstring __objc_classlist __objc_nlclslist __objc_catlist __objc_protolist __objc_imageinfo __objc_const __objc_selrefs __objc_protorefs __objc_classrefs __objc_superrefs __objc_ivar __objc_data __data __bss __common; do
	echo "create data '$section' section debug informations"
	txt=$dst/section-$segment-$section.txt
	$OTOOL -vVjC -s $segment $section $path > $txt
done


echo "create name list debug informations"
txt=$dst/name_list.txt
$NM -na $path > $txt

echo "create load commands debug informations"
txt=$dst/load_commands.txt
$OTOOL -l $path > $txt
