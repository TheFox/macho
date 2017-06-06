<?php

namespace TheFox\Test\Osx;

use PHPUnit_Framework_TestCase;
use TheFox\MachO\LoadCommandSegment;
use TheFox\MachO\LoadSection;

class LoadCommandSegmentTest extends PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        $cmd = new LoadCommandSegment();
        $cmd->setName('test1');
        $this->assertEquals('test1', (string)$cmd);
    }

    public function testSetName()
    {
        $seg = new LoadCommandSegment();
        $seg->setName('xyz');
        $this->assertEquals('xyz', $seg->getName());
    }

    public function testSetVmAddr()
    {
        $seg = new LoadCommandSegment();
        $seg->setVmAddr(123);
        $this->assertEquals(123, $seg->getVmAddr());
    }

    public function testSetVmSize()
    {
        $seg = new LoadCommandSegment();
        $seg->setVmSize(123);
        $this->assertEquals(123, $seg->getVmSize());
    }

    public function testSetFileOff()
    {
        $seg = new LoadCommandSegment();
        $seg->setFileOff(123);
        $this->assertEquals(123, $seg->getFileOff());
    }

    public function testSetNsects()
    {
        $seg = new LoadCommandSegment();
        $seg->setNsects(123);
        $this->assertEquals(123, $seg->getNsects());
    }

    public function testSetSections()
    {
        $seg = new LoadCommandSegment();
        $seg->setSections([1, 2, 3]);
        $this->assertEquals([1, 2, 3], $seg->getSections());
    }

    public function testGetSectionByName()
    {
        $sect1 = new LoadSection();
        $sect1->setName('sect1');

        $sect2 = new LoadSection();
        $sect2->setName('sect2');

        $sect3 = new LoadSection();
        $sect3->setName('sect3');

        $seg = new LoadCommandSegment();
        $seg->addSection($sect1);
        $seg->addSection($sect2);
        $seg->addSection($sect3);

        $this->assertEquals($sect2, $seg->getSectionByName('sect2'));
    }
}
