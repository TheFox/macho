<?php

namespace TheFox\MachO;

use RuntimeException;

use TheFox\Utilities\Bin;

// /usr/include//macho-o/loader.h
define('LC_SEGMENT_64', 19);

// /usr/include/mach/machine.h
define('CPU_ARCH_ABI64', 0x01000000);
define('CPU_TYPE_X86', 7);
define('CPU_TYPE_X86_64', CPU_TYPE_X86 | CPU_ARCH_ABI64);
define('CPU_TYPE_ARM', 12);
define('CPU_TYPE_ARM64', CPU_TYPE_ARM | CPU_ARCH_ABI64);
define('CPU_SUBTYPE_LIB64', 0x80000000);

class Binary{
	
	private $path;
	private $magic;
	private $cpuType;
	private $cpuSubtype;
	private $fileType;
	private $nCmds;
	private $sizeOfCmds;
	private $flags;
	
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
	
	private function readHeader(){
		$fh = fopen($this->path, 'r');
		if($fh){
			$this->printPos($fh);
			
			$data = fread($fh, 4);
			#\Doctrine\Common\Util\Debug::dump(unpack('H*', $data));
			#\Doctrine\Common\Util\Debug::dump(unpack('h*', $data));
			$data = unpack('H*', $data[3].$data[2].$data[1].$data[0]);
			#\Doctrine\Common\Util\Debug::dump($data);
			
			if($data[1] != 'feedfacf'){
				fclose($fh);
				throw new RuntimeException('Unknown file type.', 3);
			}
			$this->magic = '0x'.$data[1];
			
			$data = fread($fh, 4);
			#$data = unpack('H*', $data);
			$data = unpack('H*', $data[3].$data[2].$data[1].$data[0]);
			#\Doctrine\Common\Util\Debug::dump($data);
			$this->cpuType = hexdec($data[1]);
			
			$data = fread($fh, 4);
			#$data = unpack('H*', $data);
			$data = unpack('H*', $data[3].$data[2].$data[1].$data[0]);
			#\Doctrine\Common\Util\Debug::dump($data);
			#$this->cpuSubtype = $data[1];
			#$this->cpuSubtype = hexdec($data[1]) & ~static::CPU_SUBTYPE_LIB64;
			$this->cpuSubtype = hexdec($data[1]) & ~CPU_SUBTYPE_LIB64;
			
			$data = fread($fh, 4);
			#$data = unpack('H*', $data);
			$data = unpack('H*', $data[3].$data[2].$data[1].$data[0]);
			#\Doctrine\Common\Util\Debug::dump($data);
			#$this->fileType = (int)$data[1];
			$this->fileType = hexdec($data[1]);
			
			$data = fread($fh, 4);
			#$data = unpack('H*', $data);
			$data = unpack('H*', $data[3].$data[2].$data[1].$data[0]);
			#\Doctrine\Common\Util\Debug::dump($data);
			$this->nCmds = hexdec($data[1]);
			
			$data = fread($fh, 4);
			$data = unpack('H*', $data[3].$data[2].$data[1].$data[0]);
			#\Doctrine\Common\Util\Debug::dump($data);
			$this->sizeOfCmds = hexdec($data[1]);
			
			$data = fread($fh, 4);
			$data = unpack('H*', $data[3].$data[2].$data[1].$data[0]);
			#\Doctrine\Common\Util\Debug::dump($data);
			$this->flags = '0x'.$data[1];
			#$this->flags = hexdec($data[1]);
			
			if($this->cpuType | CPU_ARCH_ABI64){
				/* reserved */
				$data = fread($fh, 4);
			}
			
			$cmdsData = fread($fh, $this->sizeOfCmds);
			#$data = unpack('H*', $data);
			#\Doctrine\Common\Util\Debug::dump($data);
			for($cmd = 0; $cmd < $this->nCmds; $cmd++){
				$type = unpack('H*', $cmdsData[3].$cmdsData[2].$cmdsData[1].$cmdsData[0]);
				$type = $type[1];
				$cmdsData = substr($cmdsData, 4);
				
				$len = unpack('H*', $cmdsData[3].$cmdsData[2].$cmdsData[1].$cmdsData[0]);
				$len = $len[1];
				$cmdsData = substr($cmdsData, 4);
				
				print '-> cmd: '.$cmd.': '.$type.' '.$len.PHP_EOL;
				
				#$cmdsData = substr($cmdsData, $len);
				
				#\Doctrine\Common\Util\Debug::dump($data);
				
				#sleep(1);
			}
			
			print '-> pos: '.dechex(ftell($fh)).PHP_EOL;
			
			$data = fread($fh, 20);
			$data = unpack('H*', $data);
			\Doctrine\Common\Util\Debug::dump($data);
			
			#\Doctrine\Common\Util\Debug::dump(PHP_INT_SIZE);
			#\Doctrine\Common\Util\Debug::dump(PHP_INT_MAX);
			
			
			
			fclose($fh);
		}
	}
	
	private function printPos($fh){
		print '-> pos: '.dechex(ftell($fh)).PHP_EOL;
	}
	
}
