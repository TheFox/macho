<?php

namespace TheFox\Test\Osx;

use PHPUnit_Framework_TestCase;
use TheFox\MachO\LoadCommand;
use TheFox\MachO\Binary;

class LoadCommandTest extends PHPUnit_Framework_TestCase
{
    public function testSetCmd()
    {
        $cmd = new LoadCommand();
        $cmd->setCmd('xyz');
        $this->assertEquals('xyz', $cmd->getCmd());
    }

    public function testSetLength()
    {
        $cmd = new LoadCommand();
        $cmd->setLength(123);
        $this->assertEquals(123, $cmd->getLength());
    }

    public function testSetBinary()
    {
        $binary = new Binary('path');

        $cmd = new LoadCommand();
        $cmd->setBinary($binary);

        $this->assertEquals($binary, $cmd->getBinary());
    }
}
