<?php

namespace TheFox\MachO;

use RuntimeException;

use TheFox\Utilities\Bin;

// /usr/include//macho-o/loader.h
define('LC_REQ_DYLD', 0x80000000);
define('LC_SEGMENT_64', 0x19);
define('LC_MAIN', 0x28 | LC_REQ_DYLD);

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
	private $expectedFileSize = null;
	private $expectedMd5sum = null;
	private $segments = array();
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
	
	public function getSegments(){
		return $this->segments;
	}
	
	public function getMainVmAddress(){
		return $this->mainVmAddress;
	}
	
	private function printPos($fh){
		print '-> pos: 0x'.dechex(ftell($fh)).PHP_EOL;
	}
	
	public function analyze(){
		$this->readHeader();
		
		if($this->expectedFileSize !== null && filesize($this->path) != $this->expectedFileSize){
			throw new RuntimeException("File size doesn't match the expected value. ".$this->expectedFileSize, 1);
		}
		
		if($this->expectedMd5sum !== null
			&& md5_file($this->path) != $this->expectedMd5sum){
			throw new RuntimeException("MD5 sum doesn't match the expected value. ".$this->expectedMd5sum, 2);
		}
	}
	
	private function readHeader(){
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
			$this->cpuSubtype = hexdec($data[1]) & ~CPU_SUBTYPE_LIB64;
			
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
			
			if($this->cpuType | CPU_ARCH_ABI64){
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
				
				$segname = '';
				
				if($type == LC_SEGMENT_64){
					$cmdsData = fread($fh, 16); // segname
					$segname = strstr($cmdsData, "\0", true);
					
					$cmdsData = fread($fh, 8); // vmaddr
					$vmaddr = unpack('H*', $cmdsData[7].$cmdsData[6].$cmdsData[5].$cmdsData[4].
						$cmdsData[3].$cmdsData[2].$cmdsData[1].$cmdsData[0]);
					$vmaddr = hexdec($vmaddr[1]);
					
					$cmdsData = fread($fh, 8); // vmsize
					$vmsize = unpack('H*', $cmdsData[7].$cmdsData[6].$cmdsData[5].$cmdsData[4].
						$cmdsData[3].$cmdsData[2].$cmdsData[1].$cmdsData[0]);
					$vmsize = $vmsize[1];
					
					$cmdsData = fread($fh, 8); // fileoff
					$fileoff = unpack('H*', $cmdsData[7].$cmdsData[6].$cmdsData[5].$cmdsData[4].
						$cmdsData[3].$cmdsData[2].$cmdsData[1].$cmdsData[0]);
					$fileoff = hexdec($fileoff[1]);
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
						'segname' => $segname,
						'vmaddr' => $vmaddr,
						'vmsize' => $vmsize,
						'fileoff' => $fileoff,
						'nsects' => $nsects,
						'sections' => array(),
					);
					
					#print '-> cmd: '.$cmd.' '.$len.': '.$fileoff.' 0x'.dechex($vmaddr).' 0x'.dechex($vmsize).' "'.$segname.'"'.PHP_EOL;
					
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
						$reloff = unpack('H*', $sectionData[3].$sectionData[2].$sectionData[1].$sectionData[0]);
						$reloff = hexdec($reloff[1]);
						
						$sectionData = fread($fh, 4); // nreloc
						$nreloc = unpack('H*', $sectionData[3].$sectionData[2].$sectionData[1].$sectionData[0]);
						$nreloc = hexdec($nreloc[1]);
						
						$sectionData = fread($fh, 4); // flags
						$sectionData = fread($fh, 4); // reserved1
						$sectionData = fread($fh, 4); // reserved2
						
						if($this->cpuType | CPU_ARCH_ABI64){
							$sectionData = fread($fh, 4); // reserved3
						}
						
						#print '        -> sect: '.$section.' 0x'.dechex($offset).' 0x'.dechex($addr).' "'.$sectname.'"'.PHP_EOL;
						
						$this->segments[$segname]['sections'][$sectname] = array(
							'sectname' => $sectname,
							'addr' => $addr,
							'size' => $size,
							'offset' => $offset,
						);
					}
				}
				elseif($type == LC_MAIN){
					#print '    -> LC_MAIN'.PHP_EOL;
					
					$cmdsData = fread($fh, 8); // entryoff
					$entryoff = unpack('H*', $cmdsData[7].$cmdsData[6].$cmdsData[5].$cmdsData[4].
						$cmdsData[3].$cmdsData[2].$cmdsData[1].$cmdsData[0]);
					$entryoff = hexdec($entryoff[1]);
					
					$cmdsData = fread($fh, 8); // stacksize
					#$stacksize = unpack('H*', $cmdsData[7].$cmdsData[6].$cmdsData[5].$cmdsData[4].
					#	$cmdsData[3].$cmdsData[2].$cmdsData[1].$cmdsData[0]);
					#$stacksize = hexdec($stacksize[1]);
					
					$this->mainEntryOffset = $entryoff;
				}
				else{
					$skipLen = $len - 4 - 4;
					#print '-> cmd: '.$cmd.': '.$type.' ('.(dechex(LC_MAIN)).') '.$len.' "'.$segname.'"'.PHP_EOL;
					$cmdsData = fread($fh, $skipLen);
				}
				#print PHP_EOL;
				
			}
			
			fclose($fh);
			
			if(isset($this->segments['__TEXT'])
				&& isset($this->segments['__TEXT']['vmaddr'])
				&& $this->segments['__TEXT']['vmaddr']
				&& $this->mainEntryOffset){
				$this->mainVmAddress = $this->segments['__TEXT']['vmaddr']
					+ $this->mainEntryOffset;
				#print 'mainVmAddress ok: 0x'.dechex($this->mainVmAddress).PHP_EOL;
			}
		}
	}
	
	public function write($segmentName, $sectionName, $offset, $data){
		#\Doctrine\Common\Util\Debug::dump($offset);
		#\Doctrine\Common\Util\Debug::dump($data);
		
		
		$mode = 'r+';
		#$mode = 'a+';
		#$mode = 'w+';
		$fh = fopen($this->path, $mode);
		if($fh){
			if(isset($this->segments[$segmentName])
				&& isset($this->segments[$segmentName]['sections'][$sectionName])
				&& $this->segments[$segmentName]['sections'][$sectionName]['offset']){
				rewind($fh);
				#fseek($fh, 0);
				#print 'jump to '.$segmentName.', '.$sectionName.': ';
				#print $this->segments[$segmentName]['sections'][$sectionName]['offset'].PHP_EOL;
				fseek($fh, $this->segments[$segmentName]['sections'][$sectionName]['offset'] + $offset);
				#$this->printPos($fh);
				
				fwrite($fh, $data);
			}
			fclose($fh);
		}
	}
	
}
