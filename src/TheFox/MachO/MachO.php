<?php

// Macho macho man, I gotta be a macho man.

namespace TheFox\MachO;

// /usr/include//macho-o/loader.h
define(__NAMESPACE__.'\MH_MAGIC', 0xfeedface);
define(__NAMESPACE__.'\MH_MAGIC_64', 0xfeedfacf);

define(__NAMESPACE__.'\LC_REQ_DYLD', 0x80000000);
define(__NAMESPACE__.'\LC_SEGMENT', 0x1);
define(__NAMESPACE__.'\LC_THREAD', 0x4);
define(__NAMESPACE__.'\LC_UNIXTHREAD', 0x5);
define(__NAMESPACE__.'\LC_SEGMENT_64', 0x19);
define(__NAMESPACE__.'\LC_MAIN', 0x28 | LC_REQ_DYLD);

// /usr/include/mach/machine.h
define(__NAMESPACE__.'\CPU_ARCH_ABI64', 0x01000000);
define(__NAMESPACE__.'\CPU_TYPE_X86', 7);
define(__NAMESPACE__.'\CPU_TYPE_X86_64', CPU_TYPE_X86 | CPU_ARCH_ABI64);
define(__NAMESPACE__.'\CPU_TYPE_ARM', 12);
define(__NAMESPACE__.'\CPU_TYPE_ARM64', CPU_TYPE_ARM | CPU_ARCH_ABI64);
define(__NAMESPACE__.'\CPU_SUBTYPE_LIB64', 0x80000000);

class MachO{
	
	const NAME = 'MachO';
	const VERSION = '0.4.1';
	
}
