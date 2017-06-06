<?php

namespace TheFox\Test\Osx;

use PHPUnit_Framework_TestCase;
use TheFox\MachO\LoadCommand;
use TheFox\MachO\LoadSection;

class LoadSectionTest extends PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        $sect = new LoadSection();

        $sect->setAddr(0x21);
        $this->assertEquals('0x21', (string)$sect);

        $sect->setName('name2');
        $this->assertEquals('name2', (string)$sect);
    }

    public function testSetName()
    {
        $sect = new LoadSection();
        $sect->setName('name1');
        $this->assertEquals('name1', $sect->getName());
    }

    public function testSetAddr()
    {
        $sect = new LoadSection();
        $sect->setAddr(0x21);
        $this->assertEquals(0x21, $sect->getAddr());
    }

    public function testSetSize()
    {
        $sect = new LoadSection();
        $sect->setSize(0x21);
        $this->assertEquals(0x21, $sect->getSize());
    }

    public function testSetOffset()
    {
        $sect = new LoadSection();
        $sect->setOffset(0x21);
        $this->assertEquals(0x21, $sect->getOffset());
    }

    public function testSetReloff()
    {
        $sect = new LoadSection();
        $sect->setReloff(0x21);
        $this->assertEquals(0x21, $sect->getReloff());
    }

    public function testSetNreloc()
    {
        $sect = new LoadSection();
        $sect->setNreloc(0x21);
        $this->assertEquals(0x21, $sect->getNreloc());
    }

    public function testSetLoadCommand()
    {
        $loadCommand = new LoadCommand();

        $sect = new LoadSection();
        $sect->setLoadCommand($loadCommand);
        $this->assertEquals($loadCommand, $sect->getLoadCommand());
    }
}
