<?php

use TheFox\MachO\MachO;

class MachOTest extends PHPUnit_Framework_TestCase{
	
	public function testBasic(){
		$macho = new MachO();
		$this->assertEquals(0xfeedface, \TheFox\MachO\MH_MAGIC);
	}
	
}
