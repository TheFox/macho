<?php

namespace TheFox\Test\Linux;

use RuntimeException;
use TheFox\Test\Osx\BinaryTest as BinaryOsxTest;
use TheFox\MachO\Binary;

class BinaryTest extends BinaryOsxTest
{
    /**
     * @expectedException RuntimeException
     * @expectedExceptionCode 30
     */
    public function testConstructRuntimeException20()
    {
        $binary = new Binary('tmp/test_data/test_prog');
        $binary->setExpectedFileSize(24);
        $binary->analyze();
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionCode 30
     */
    public function testConstructRuntimeException21()
    {
        $binary = new Binary('tmp/test_data/test_prog');
        $binary->setExpectedMd5sum('md5sum');
        $binary->analyze();
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionCode 30
     */
    public function testConstructRuntimeException30()
    {
        $binary = new Binary('macho');
        $binary->analyze();
    }

    public function testBasic()
    {
        $this->assertTrue(true);
    }

    public function testGetPath()
    {
        $binary = new Binary('tmp/test_data/test_prog');
        $this->assertEquals('tmp/test_data/test_prog', $binary->getPath());
    }
}
