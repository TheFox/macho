<?php

namespace TheFox\MachO;

class LoadCommandEntryPoint extends LoadCommand{
	
	private $entryoff;
	private $stacksize;
	
	public function __toString(){
		return 'LC_MAIN';
	}
	
	public function setEntryOff($entryoff){
		$this->entryoff = $entryoff;
	}
	
	public function getEntryOff(){
		return $this->entryoff;
	}
	
	public function setStackSize($stacksize){
		$this->stacksize = $stacksize;
	}
	
	public function getStackSize(){
		return $this->stacksize;
	}
	
}
