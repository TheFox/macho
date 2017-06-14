<?php

// Macho macho man, I gotta be a macho man.

namespace TheFox\MachO;

// /usr/include//macho-o/loader.h
//define(__NAMESPACE__ . '\MH_MAGIC', 0xfeedface);
//define(__NAMESPACE__ . '\MH_MAGIC_64', 0xfeedfacf);
/*define(__NAMESPACE__ . '\LC_REQ_DYLD', 0x80000000);
define(__NAMESPACE__ . '\LC_SEGMENT', 0x1);
define(__NAMESPACE__ . '\LC_THREAD', 0x4);
define(__NAMESPACE__ . '\LC_UNIXTHREAD', 0x5);
define(__NAMESPACE__ . '\LC_SEGMENT_64', 0x19);
define(__NAMESPACE__ . '\LC_MAIN', 0x28 | LC_REQ_DYLD);*/

// /usr/include/mach/machine.h
/*define(__NAMESPACE__ . '\CPU_ARCH_ABI64', 0x01000000);
define(__NAMESPACE__ . '\CPU_TYPE_X86', 7);
define(__NAMESPACE__ . '\CPU_TYPE_X86_64', CPU_TYPE_X86 | CPU_ARCH_ABI64);
define(__NAMESPACE__ . '\CPU_TYPE_ARM', 12);
define(__NAMESPACE__ . '\CPU_TYPE_ARM64', CPU_TYPE_ARM | CPU_ARCH_ABI64);
define(__NAMESPACE__ . '\CPU_SUBTYPE_LIB64', 0x80000000);*/

class MachO
{
    const NAME = 'MachO';
    const VERSION = '0.6.0-dev.2';

    const MH_MAGIC = 0xfeedface;
    const MH_MAGIC_64 = 0xfeedfacf;
    
    const LC_REQ_DYLD = 0x80000000;
    const LC_SEGMENT = 0x1;
    const LC_THREAD = 0x4;
    const LC_UNIXTHREAD = 0x5;
    const LC_SEGMENT_64 = 0x19;
    const LC_MAIN = 0x28 | MachO::LC_REQ_DYLD;

    const CPU_ARCH_ABI64 = 0x01000000;
    const CPU_TYPE_X86 = 7;
    const CPU_TYPE_X86_64 = MachO::CPU_TYPE_X86 | MachO::CPU_ARCH_ABI64;
    const CPU_TYPE_ARM = 12;
    const CPU_TYPE_ARM64 = MachO::CPU_TYPE_ARM | MachO::CPU_ARCH_ABI64;
    const CPU_SUBTYPE_LIB64 = 0x80000000;
}
