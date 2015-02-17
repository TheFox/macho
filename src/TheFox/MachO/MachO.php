<?php

namespace TheFox\MachO;

// /usr/include//macho-o/loader.h
define(__NAMESPACE__.'\LC_REQ_DYLD', 0x80000000);
define(__NAMESPACE__.'\LC_SEGMENT_64', 0x19);
define(__NAMESPACE__.'\LC_MAIN', 0x28 | LC_REQ_DYLD);

// /usr/include/mach/machine.h
define(__NAMESPACE__.'\CPU_ARCH_ABI64', 0x01000000);
define(__NAMESPACE__.'\CPU_TYPE_X86', 7);
define(__NAMESPACE__.'\CPU_TYPE_X86_64', CPU_TYPE_X86 | CPU_ARCH_ABI64);
define(__NAMESPACE__.'\CPU_TYPE_ARM', 12);
define(__NAMESPACE__.'\CPU_TYPE_ARM64', CPU_TYPE_ARM | CPU_ARCH_ABI64);
define(__NAMESPACE__.'\CPU_SUBTYPE_LIB64', 0x80000000);

define(__NAMESPACE__.'\C1', 1);
define(__NAMESPACE__.'\C2', 1 + CPU_TYPE_X86);

class MachO{
	
	const NAME = 'MachO';
	const VERSION = '0.2.x-dev';
	const RELEASE = 1;
	
}
