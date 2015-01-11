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
	private $segments = array();
	
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
			#$this->printPos($fh);
			
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
			
			#$this->printPos($fh);
			
			#$cmdsData = fread($fh, $this->sizeOfCmds);
			#$data = unpack('H*', $data);
			#\Doctrine\Common\Util\Debug::dump($cmdsData);
			for($cmd = 0; $cmd < $this->nCmds; $cmd++){
				#$this->printPos($fh);
				#print '-> cmd'.PHP_EOL;
				
				$cmdsData = fread($fh, 4); // cmd
				#print '  -> type: '.$cmdsData.PHP_EOL;
				$type = unpack('H*', $cmdsData[3].$cmdsData[2].$cmdsData[1].$cmdsData[0]);
				$type = $type[1];
				
				$cmdsData = fread($fh, 4); // cmdsize
				#print '  -> len: '.$cmdsData.PHP_EOL;
				$len = unpack('H*', $cmdsData[3].$cmdsData[2].$cmdsData[1].$cmdsData[0]);
				#$len = $len[1];
				$len = hexdec($len[1]);
				
				$segname = '';
				
				if($type == LC_SEGMENT_64){
					$cmdsData = fread($fh, 16); // segname
					#print '  -> name: '.$cmdsData.PHP_EOL;
					#\Doctrine\Common\Util\Debug::dump($cmdsData);
					#$segname = strval($cmdsData);
					$segname = strstr($cmdsData, "\0", true);
					#\Doctrine\Common\Util\Debug::dump($segname);
					/*$segname = unpack('H*',
						$cmdsData[15].$cmdsData[14].$cmdsData[13].$cmdsData[12]
						.$cmdsData[11].$cmdsData[10].$cmdsData[9].$cmdsData[8]
						.$cmdsData[7].$cmdsData[6].$cmdsData[5].$cmdsData[4]
						.$cmdsData[3].$cmdsData[2].$cmdsData[1].$cmdsData[0]
					);
					$segname = $segname[1];*/
					
					$cmdsData = fread($fh, 8); // vmaddr
					$cmdsData = fread($fh, 8); // vmsize
					
					$cmdsData = fread($fh, 8); // fileoff
					$fileoff = unpack('H*', $cmdsData[7].$cmdsData[6].$cmdsData[5].$cmdsData[4].
						$cmdsData[3].$cmdsData[2].$cmdsData[1].$cmdsData[0]);
					$fileoff = $fileoff[1];
					#print '    -> fileoff: '.$fileoff.PHP_EOL;
					
					$cmdsData = fread($fh, 8); // filesize
					$cmdsData = fread($fh, 4); // maxprot
					$cmdsData = fread($fh, 4); // initprot
					
					$cmdsData = fread($fh, 4); // nsects
					$nsects = unpack('H*', $cmdsData[3].$cmdsData[2].$cmdsData[1].$cmdsData[0]);
					$nsects = hexdec($nsects[1]);
					#print '    -> nsects: '.$nsects.PHP_EOL;
					
					$cmdsData = fread($fh, 4); // flags
					
					$this->segments[$segname] = array(
						'fileoff' => $fileoff,
						'nsects' => $nsects,
						'sections' => array(),
					);
					
					#print '    -> cmd: '.$cmd.': '.$type.' '.$len.' "'.$segname.'"'.PHP_EOL;
					
					for($section = 0; $section < $nsects; $section++){
						$sectionData = fread($fh, 16); // sectname
						$sectname = strstr($sectionData, "\0", true);
						
						$sectionData = fread($fh, 16); // segname
						
						$addr = 0;
						$size = 0;
						if($this->cpuType | CPU_ARCH_ABI64){
							$sectionData = fread($fh, 8); // addr
							$addr = unpack('H*', $sectionData[7].$sectionData[6].$sectionData[5].$sectionData[4].
								$sectionData[3].$sectionData[2].$sectionData[1].$sectionData[0]);
							
							$sectionData = fread($fh, 8); // size
							$size = unpack('H*', $sectionData[7].$sectionData[6].$sectionData[5].$sectionData[4].
								$sectionData[3].$sectionData[2].$sectionData[1].$sectionData[0]);
						}
						else{
							$sectionData = fread($fh, 4); // addr
							$addr = unpack('H*', $sectionData[3].$sectionData[2].$sectionData[1].$sectionData[0]);
							
							$sectionData = fread($fh, 4); // size
							$size = unpack('H*', $sectionData[3].$sectionData[2].$sectionData[1].$sectionData[0]);
						}
						$addr = hexdec($addr[1]);
						#$addr = $addr[1];
						$size = hexdec($size[1]);
						#$size = $size[1];
						
						$sectionData = fread($fh, 4); // offset
						$offset = unpack('H*', $sectionData[3].$sectionData[2].$sectionData[1].$sectionData[0]);
						$offset = hexdec($offset[1]);
						
						$sectionData = fread($fh, 4); // align
						$sectionData = fread($fh, 4); // reloff
						$sectionData = fread($fh, 4); // nreloc
						$sectionData = fread($fh, 4); // flags
						$sectionData = fread($fh, 4); // reserved1
						$sectionData = fread($fh, 4); // reserved2
						
						if($this->cpuType | CPU_ARCH_ABI64){
							$sectionData = fread($fh, 4); // reserved3
						}
						
						#print '        -> sect: '.$section.' 0x'.dechex($addr).' '.dechex($size).' '.$offset.' "'.$sectname.'"'.PHP_EOL;
						
						$this->segments[$segname]['sections'][$sectname] = array(
							'sectname' => $sectname,
							'addr' => $addr,
							'size' => $size,
							'offset' => $offset,
						);
						
						#usleep(500000);
					}
				}
				else{
					$skipLen = $len - 4 - 4;
					#print '-> cmd: '.$cmd.': '.$type.' '.$len.' "'.$segname.'" skip '.$skipLen.' byte'.PHP_EOL;
					$cmdsData = fread($fh, $skipLen);
				}
					
				#print PHP_EOL;
				
			}
			
			#$this->printPos($fh);
			
			
			
			#$data = fread($fh, 256);
			#\Doctrine\Common\Util\Debug::dump($data);
			#$data = unpack('H*', $data);
			#\Doctrine\Common\Util\Debug::dump($data);
			
			#\Doctrine\Common\Util\Debug::dump($this->segments, 4);
			
			fclose($fh);
		}
	}
	
	private function printPos($fh){
		print '-> pos: 0x'.dechex(ftell($fh)).PHP_EOL;
	}
	
}
