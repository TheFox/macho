<?php

namespace TheFox\Test\Osx;

use RuntimeException;
use PHPUnit\Framework\TestCase;
use TheFox\MachO\Binary;

class BinaryTest extends TestCase
{
    /**
     * @expectedException RuntimeException
     * @expectedExceptionCode 10
     */
    public function testConstructRuntimeException10()
    {
        $binary = new Binary('');
        $binary->analyze();
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionCode 11
     */
    public function testConstructRuntimeException11()
    {
        $binary = new Binary('no_file');
        $binary->analyze();
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionCode 20
     */
    public function testConstructRuntimeException20()
    {
        $binary = new Binary('tmp/test_data/test_prog');
        $binary->setExpectedFileSize(24);
        $binary->analyze();
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionCode 21
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
        $binary = new Binary('application.php');
        $binary->analyze();
    }

    public function testBasic()
    {
        $binary = new Binary('tmp/test_data/test_prog');
        $binary->analyze();
        
        $this->assertTrue(true);
    }

    public function testGetPath()
    {
        $binary = new Binary('tmp/test_data/test_prog');
        $binary->analyze();

        $this->assertEquals('tmp/test_data/test_prog', $binary->getPath());
    }

    /*public function testGetMagic(){
        $binary = new Binary('test_data/test_prog');
        $binary->analyze();
        
        $this->assertEquals(0xfeedfacf, $binary->getMagic());
    }
    
    public function testGetCpuSubtype(){
        $binary = new Binary('test_data/test_prog');
        $binary->analyze();
        
        $this->assertEquals(0x80000003, $binary->getCpuSubtype());
    }
    
    public function testGetFileType(){
        $binary = new Binary('test_data/test_prog');
        $binary->analyze();
        
        $this->assertEquals(2, $binary->getFileType());
    }
    
    public function testGetNcmds(){
        $binary = new Binary('test_data/test_prog');
        $binary->analyze();
        
        $this->assertEquals(16, $binary->getNcmds());
    }
    
    public function testGetSizeOfCmds(){
        $binary = new Binary('test_data/test_prog');
        $binary->analyze();
        
        $this->assertEquals(1296, $binary->getSizeOfCmds());
    }
    
    public function testGetFlags(){
        $binary = new Binary('test_data/test_prog');
        $binary->analyze();
        
        $this->assertEquals(0x200085, $binary->getFlags());
    }*/

    public function testGetExpectedFileSize()
    {
        $binary = new Binary('tmp/test_data/test_prog');
        $binary->setExpectedFileSize(24);

        $this->assertEquals(24, $binary->getExpectedFileSize());
    }

    public function testSetExpectedMd5sum()
    {
        $binary = new Binary('tmp/test_data/test_prog');
        $binary->setExpectedMd5sum('xyz');

        $this->assertEquals('xyz', $binary->getExpectedMd5sum());
    }
}
