<?php

use TheFox\MachO\Binary;

class BinaryTest extends PHPUnit_Framework_TestCase{
	
	/**
	 * @expectedException RuntimeException
	 * @expectedExceptionCode 10
	 */
	public function testConstructRuntimeException1(){
		$binary = new Binary('');
	}
	
	/**
	 * @expectedException RuntimeException
	 * @expectedExceptionCode 11
	 */
	public function testConstructRuntimeException2(){
		$binary = new Binary('no_file');
	}
	
	/**
	 * @expectedException RuntimeException
	 * @expectedExceptionCode 30
	 */
	public function testConstructRuntimeException3(){
		$binary = new Binary('tests/BinaryTest.php');
		$binary->analyze();
	}
	
	public function testConstruct(){
		$binary = new Binary('test_data/test_prog');
		$binary->analyze();
		
		$this->assertEquals('test_data/test_prog', $binary->getPath());
		$this->assertEquals('0xfeedfacf', $binary->getMagic());
	}
	
}
