<?php

namespace TheFox\MachO;

class LoadCommandSegment extends LoadCommand{
	
	private $name;
	private $vmaddr;
	private $vmsize;
	private $fileoff;
	#private $filesize;
	#private $maxprot;
	#private $initprot;
	private $nsects;
	#private $flags;
	
	private $sections = array();
	
	public function __toString(){
		return $this->getName();
	}
	
	public function setName($name){
		$this->name = $name;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setVmAddr($vmaddr){
		$this->vmaddr = $vmaddr;
	}
	
	public function getVmAddr(){
		return $this->vmaddr;
	}
	
	public function setVmSize($vmsize){
		$this->vmsize = $vmsize;
	}
	
	public function getVmSize(){
		return $this->vmsize;
	}
	
	public function setFileOff($fileoff){
		$this->fileoff = $fileoff;
	}
	
	public function getFileOff(){
		return $this->fileoff;
	}
	
	public function setNsects($nsects){
		$this->nsects = $nsects;
	}
	
	public function getNsects(){
		return $this->nsects;
	}
	
	public function setSections($sections){
		$this->sections = $sections;
	}
	
	public function addSection(LoadSection $section){
		$name = (string)$section;
		$this->sections[$name] = $section;
	}
	
	public function getSections(){
		return $this->sections;
	}
	
	public function getSectionByName($name){
		return $this->sections[$name];
	}
	
}
