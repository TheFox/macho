<?php

use TheFox\MachO\Binary;

class BinaryTest extends PHPUnit_Framework_TestCase{
	
	/**
	 * @expectedException RuntimeException
	 * @expectedExceptionCode 1
	 */
	public function testConstructRuntimeException1(){
		$binary = new Binary('');
	}
	
	/**
	 * @expectedException RuntimeException
	 * @expectedExceptionCode 2
	 */
	public function testConstructRuntimeException2(){
		$binary = new Binary('no_file');
	}
	
	/**
	 * @expectedException RuntimeException
	 * @expectedExceptionCode 3
	 */
	public function testConstructRuntimeException3(){
		$binary = new Binary('tests/BinaryTest.php');
	}
	
	public function testConstruct(){
		$binary = new Binary('test_data/test_prog');
		
		$this->assertEquals('test_data/test_prog', $binary->getPath());
		$this->assertEquals('cffaedfe', $binary->getMagic());
	}
	
}
