<?php

namespace TheFox\Test\Osx;

use PHPUnit\Framework\TestCase;
use TheFox\MachO\LoadCommand;
use TheFox\MachO\Binary;

class LoadCommandTest extends TestCase
{
    public function testSetCmd()
    {
        $cmd = new LoadCommand();
        $cmd->setCmd(123);
        $this->assertEquals(123, $cmd->getCmd());
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
