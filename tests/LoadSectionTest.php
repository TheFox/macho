<?php

use TheFox\MachO\LoadSection;

class LoadSectionTest extends PHPUnit_Framework_TestCase{
	
	public function testToString(){
		$sect = new LoadSection();
		
		$sect->setAddr(0x21);
		$this->assertEquals('0x21', (string)$sect);
		
		$sect->setName('name2');
		$this->assertEquals('name2', (string)$sect);
	}
	
	public function testSetName(){
		$sect = new LoadSection();
		$sect->setName('name1');
		$this->assertEquals('name1', $sect->getName());
	}
	
	public function testSetAddr(){
		$sect = new LoadSection();
		$sect->setAddr('xyz');
		$this->assertEquals('xyz', $sect->getAddr());
	}
	
	public function testSetSize(){
		$sect = new LoadSection();
		$sect->setSize('xyz');
		$this->assertEquals('xyz', $sect->getSize());
	}
	
	public function testSetOffset(){
		$sect = new LoadSection();
		$sect->setOffset('xyz');
		$this->assertEquals('xyz', $sect->getOffset());
	}
	
	public function testSetReloff(){
		$sect = new LoadSection();
		$sect->setReloff('xyz');
		$this->assertEquals('xyz', $sect->getReloff());
	}
	
	public function testSetNreloc(){
		$sect = new LoadSection();
		$sect->setNreloc('xyz');
		$this->assertEquals('xyz', $sect->getNreloc());
	}
	
	public function testSetLoadCommand(){
		$sect = new LoadSection();
		$sect->setLoadCommand('xyz');
		$this->assertEquals('xyz', $sect->getLoadCommand());
	}
	
}
