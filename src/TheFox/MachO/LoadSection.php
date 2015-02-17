<?php

namespace TheFox\MachO;

class LoadSection{
	
	private $name;
	private $addr;
	private $size;
	private $offset;
	private $reloff;
	private $nreloc;
	
	// parent
	private $loadCommand;
	
	public function __toString(){
		if($this->getName()){
			return $this->getName();
		}
		return '0x'.dechex($this->getAddr());
	}
	
	public function setName($name){
		$this->name = $name;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setAddr($addr){
		$this->addr = $addr;
	}
	
	public function getAddr(){
		return $this->addr;
	}
	
	public function setSize($size){
		$this->size = $size;
	}
	
	public function getSize(){
		return $this->size;
	}
	
	public function setOffset($offset){
		$this->offset = $offset;
	}
	
	public function getOffset(){
		return $this->offset;
	}
	
	public function setReloff($reloff){
		$this->reloff = $reloff;
	}
	
	public function getReloff(){
		return $this->reloff;
	}
	
	public function setNreloc($nreloc){
		$this->nreloc = $nreloc;
	}
	
	public function getNreloc(){
		return $this->nreloc;
	}
	
	public function setLoadCommand($loadCommand){
		$this->loadCommand = $loadCommand;
	}
	
	public function getLoadCommand(){
		return $this->loadCommand;
	}
	
}
