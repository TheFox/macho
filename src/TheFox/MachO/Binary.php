<?php

namespace TheFox\MachO;

use RuntimeException;

use TheFox\Utilities\Bin;

class Binary{
	
	private $path;
	private $magic;
	
	public function __construct($path){
		$this->path = $path;
		
		if(!$this->path){
			throw new RuntimeException('No Path.', 1);
		}
		
		if(!file_exists($this->path)){
			throw new RuntimeException('File '.$this->path.' does not exist.', 2);
		}
		
		$this->readHeader();
	}
	
	public function getPath(){
		return $this->path;
	}
	
	public function getMagic(){
		return $this->magic;
	}
	
	private function readHeader(){
		$fh = fopen($this->path, 'r');
		if($fh){
			$magic = fread($fh, 4);
			
			#Bin::debugData($magic);
			
			#\Doctrine\Common\Util\Debug::dump(unpack('H*', $magic));
			
			$data = unpack('H*', $magic);
			if($data[1] != 'cffaedfe'){
				fclose($fh);
				throw new RuntimeException('Unknown file type.', 3);
			}
			$this->magic = $data[1];
			
			fclose($fh);
		}
	}
	
}
