<?php

namespace TheFox\MachO;

use RuntimeException;

use TheFox\Utilities\Bin;

class Binary{
	
	private $path;
	private $magic;
	private $cpuType;
	private $cpuSubtype;
	private $fileType;
	private $nCmds;
	private $sizeOfCmds;
	private $flags;
	
	private $loadCommands = array();
	
	private $expectedFileSize = null;
	private $expectedMd5sum = null;
	private $mainEntryOffset = 0;
	private $mainVmAddress = 0;
	
	public function __construct($path){
		$this->path = $path;
		
		if(!$this->path){
			throw new RuntimeException('No Path.', 1);
		}
		
		if(!file_exists($this->path)){
			throw new RuntimeException('File '.$this->path.' does not exist.', 2);
		}
	}
	
	public function getPath(){
		return $this->path;
	}
	
	public function getMagic(){
		return $this->magic;
	}
	
	public function getCpuType(){
		return $this->cpuType;
	}
	
	public function getCpuSubtype(){
		return $this->cpuSubtype;
	}
	
	public function getFileType(){
		return $this->fileType;
	}
	
	public function getNCmds(){
		return $this->nCmds;
	}
	
	public function getSizeOfCmds(){
		return $this->sizeOfCmds;
	}
	
	public function getFlags(){
		return $this->flags;
	}
	
	public function setExpectedFileSize($expectedFileSize){
		$this->expectedFileSize = $expectedFileSize;
	}
	
	public function setExpectedMd5sum($expectedMd5sum){
		$this->expectedMd5sum = $expectedMd5sum;
	}
	
	public function getLoadCommands(){
		return $this->loadCommands;
	}
	
	public function getMainVmAddress(){
		return $this->mainVmAddress;
	}
	
	private function printPos($fh){
		print '-> pos: 0x'.dechex(ftell($fh)).PHP_EOL;
	}
	
	public function analyze(){
		$this->parseHeader();
		
		if($this->expectedFileSize !== null && filesize($this->path) != $this->expectedFileSize){
			throw new RuntimeException("File size doesn't match the expected value. ".$this->expectedFileSize, 1);
		}
		
		if($this->expectedMd5sum !== null
			&& md5_file($this->path) != $this->expectedMd5sum){
			throw new RuntimeException("MD5 sum doesn't match the expected value. ".$this->expectedMd5sum, 2);
		}
	}
	
	private function parseHeader(){
		$fh = fopen($this->path, 'r');
		if($fh){
			
			$data = fread($fh, 4);
			$data = unpack('H*', $data[3].$data[2].$data[1].$data[0]);
			
			if($data[1] != 'feedfacf'){
				fclose($fh);
				throw new RuntimeException('Unknown file type.', 3);
			}
			$this->magic = '0x'.$data[1];
			
			$data = fread($fh, 4);
			$data = unpack('H*', $data[3].$data[2].$data[1].$data[0]);
			$this->cpuType = hexdec($data[1]);
			
			$data = fread($fh, 4);
			$data = unpack('H*', $data[3].$data[2].$data[1].$data[0]);
			$this->cpuSubtype = hexdec($data[1]) & ~\TheFox\MachO\CPU_SUBTYPE_LIB64;
			
			$data = fread($fh, 4);
			$data = unpack('H*', $data[3].$data[2].$data[1].$data[0]);
			$this->fileType = hexdec($data[1]);
			
			$data = fread($fh, 4);
			$data = unpack('H*', $data[3].$data[2].$data[1].$data[0]);
			$this->nCmds = hexdec($data[1]);
			
			$data = fread($fh, 4);
			$data = unpack('H*', $data[3].$data[2].$data[1].$data[0]);
			$this->sizeOfCmds = hexdec($data[1]);
			
			$data = fread($fh, 4);
			$data = unpack('H*', $data[3].$data[2].$data[1].$data[0]);
			$this->flags = '0x'.$data[1];
			
			if($this->cpuType | \TheFox\MachO\CPU_ARCH_ABI64){
				// reserved
				$data = fread($fh, 4);
			}
			
			for($cmd = 0; $cmd < $this->nCmds; $cmd++){
				$cmdsData = fread($fh, 4); // cmd
				$type = unpack('H*', $cmdsData[3].$cmdsData[2].$cmdsData[1].$cmdsData[0]);
				$type = hexdec($type[1]);
				
				$cmdsData = fread($fh, 4); // cmdsize
				$len = unpack('H*', $cmdsData[3].$cmdsData[2].$cmdsData[1].$cmdsData[0]);
				$len = hexdec($len[1]);
				
				$cmdsData = fread($fh, $len - 8);
				$lcmd = LoadCommand::fromBinaryWithoutHead($this, $type, $len, $cmdsData);
				if($lcmd){
					$this->loadCommands[(string)$lcmd] = $lcmd;
				}
			}
			
			fclose($fh);
			
			if(isset($this->loadCommands['LC_MAIN'])){
				$this->mainEntryOffset = $this->loadCommands['LC_MAIN']->getEntryOff();
				
				if(isset($this->loadCommands['__TEXT'])){
					#\Doctrine\Common\Util\Debug::dump($this->loadCommands['__TEXT'], 3);
					$this->mainVmAddress = $this->loadCommands['__TEXT']->getVmAddr()
						+ $this->mainEntryOffset;
				}
			}
			#print 'mainEntryOffset: 0x'.dechex($this->mainEntryOffset).PHP_EOL;
			#print 'mainVmAddress: 0x'.dechex($this->mainVmAddress).PHP_EOL;
		}
	}
	
	public function write($segmentName, $sectionName, $offset, $data){
		$mode = 'r+';
		$fh = fopen($this->path, $mode);
		if($fh){
			if(isset($this->loadCommands[$segmentName])){
				$pos = 0;
				
				$lcmd = $this->loadCommands[$segmentName];
				if($lcmd instanceof LoadCommandSegment){
					$section = $lcmd->getSectionByName($sectionName);
					$pos = $section->getOffset() + $offset;
				}
				
				if($pos){
					rewind($fh);
					fseek($fh, $pos);
					fwrite($fh, $data);
				}
			}
			
			fclose($fh);
		}
	}
	
}
