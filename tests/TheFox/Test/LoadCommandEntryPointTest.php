<?php

namespace TheFox\Test;

use PHPUnit_Framework_TestCase;

use TheFox\MachO\LoadCommandEntryPoint;

class LoadCommandEntryPointTest extends PHPUnit_Framework_TestCase{
	
	public function testToString(){
		$cmd = new LoadCommandEntryPoint();
		$this->assertEquals('LC_MAIN', (string)$cmd);
	}
	
	public function testSetEntryOff(){
		$cmd = new LoadCommandEntryPoint();
		$cmd->setEntryOff(123);
		$this->assertEquals(123, $cmd->getEntryOff());
	}
	
	public function testSetStackSize(){
		$cmd = new LoadCommandEntryPoint();
		$cmd->setStackSize(456);
		$this->assertEquals(456, $cmd->getStackSize());
	}
	
}
