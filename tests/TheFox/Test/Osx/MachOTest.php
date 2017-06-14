<?php

namespace TheFox\Test\Osx;

use PHPUnit\Framework\TestCase;
use TheFox\MachO\MachO;

class MachOTest extends TestCase
{
    public function testBasic()
    {
        //$macho = new MachO();
        $this->assertEquals(0xfeedface, MachO::MH_MAGIC);
    }
}
