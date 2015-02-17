<?php

namespace TheFox\MachO;

class LoadCommand{
	
	private $cmd;
	private $length;
	
	private $binary;
	
	public function setCmd($cmd){
		$this->cmd = $cmd;
	}
	
	public function getCmd(){
		return $this->cmd;
	}
	
	public function setLength($length){
		$this->length = $length;
	}
	
	public function getLength(){
		return $this->length;
	}
	
	public function setBinary($binary){
		$this->binary = $binary;
	}
	
	public function getBinary(){
		return $this->binary;
	}
	
	public static function fromBinaryWithoutHead($binary, $cmd, $length, $bin){
		$lcmd = null;
		
		if($cmd == \TheFox\MachO\LC_SEGMENT_64){
			$lcmd = new LoadCommandSegment();
			$lcmd->setBinary($binary);
			$lcmd->setCmd($cmd);
			$lcmd->setLength($length);
			
			$data = substr($bin, 0, 16); // segname
			$bin = substr($bin, 16);
			$val = strstr($data, "\0", true);
			$lcmd->setName($val);
			
			$data = substr($bin, 0, 8); // vmaddr
			$bin = substr($bin, 8);
			$val = unpack('H*', $data[7].$data[6].$data[5].$data[4].
				$data[3].$data[2].$data[1].$data[0]);
			$val = hexdec($val[1]);
			$lcmd->setVmAddr($val);
			
			$data = substr($bin, 0, 8); // vmsize
			$bin = substr($bin, 8);
			$val = unpack('H*', $data[7].$data[6].$data[5].$data[4].
				$data[3].$data[2].$data[1].$data[0]);
			$val = hexdec($val[1]);
			$lcmd->setVmSize($val);
			
			$data = substr($bin, 0, 8); // fileoff
			$bin = substr($bin, 8);
			$val = unpack('H*', $data[7].$data[6].$data[5].$data[4].
				$data[3].$data[2].$data[1].$data[0]);
			$val = hexdec($val[1]);
			$lcmd->setFileOff($val);
			
			$data = substr($bin, 0, 8); // filesize
			$bin = substr($bin, 8);
			
			$data = substr($bin, 0, 4); // maxprot
			$bin = substr($bin, 4);
			
			$data = substr($bin, 0, 4); // initprot
			$bin = substr($bin, 4);
			
			$data = substr($bin, 0, 4); // nsects
			$bin = substr($bin, 4);
			$val = unpack('H*', $data[3].$data[2].$data[1].$data[0]);
			$val = hexdec($val[1]);
			$lcmd->setNsects($val);
			$nsects = $val;
			
			$data = substr($bin, 0, 4); // flags
			$bin = substr($bin, 4);
			
			#print '-> cmd: '.$cmd.' '.$len.': '.$fileoff.' 0x'.dechex($vmaddr).' 0x'.dechex($vmsize).' "'.$segname.'"'.PHP_EOL;
			
			#$sections = array();
			for($sectionC = 0; $sectionC < $nsects; $sectionC++){
				$sectionO = new LoadSection();
				$sectionO->setLoadCommand($lcmd);
				
				$data = substr($bin, 0, 16); // sectname
				$bin = substr($bin, 16);
				$val = strstr($data, "\0", true);
				#print "sec name '$val'\n";
				$sectionO->setName($val);
				
				$data = substr($bin, 0, 16); // segname
				$bin = substr($bin, 16);
				
				$addr = 0;
				$size = 0;
				if($lcmd->getBinary()->getCpuType() | \TheFox\MachO\CPU_ARCH_ABI64){
					$data = substr($bin, 0, 8); // addr
					$bin = substr($bin, 8);
					$addr = unpack('H*', $data[7].$data[6].$data[5].$data[4].
						$data[3].$data[2].$data[1].$data[0]);
					
					$data = substr($bin, 0, 8); // size
					$bin = substr($bin, 8);
					$size = unpack('H*', $data[7].$data[6].$data[5].$data[4].
						$data[3].$data[2].$data[1].$data[0]);
				}
				else{
					$data = substr($bin, 0, 4); // addr
					$bin = substr($bin, 4);
					$addr = unpack('H*', $data[3].$data[2].$data[1].$data[0]);
					
					$data = substr($bin, 0, 4); // size
					$bin = substr($bin, 4);
					$size = unpack('H*', $data[3].$data[2].$data[1].$data[0]);
				}
				$addr = hexdec($addr[1]);
				$size = hexdec($size[1]);
				$sectionO->setAddr($addr);
				$sectionO->setSize($size);
				
				$data = substr($bin, 0, 4); // offset
				$bin = substr($bin, 4);
				$val = unpack('H*', $data[3].$data[2].$data[1].$data[0]);
				$val = hexdec($val[1]);
				$sectionO->setOffset($val);
				
				$data = substr($bin, 0, 4); // align
				$bin = substr($bin, 4);
				
				$data = substr($bin, 0, 4); // reloff
				$bin = substr($bin, 4);
				$val = unpack('H*', $data[3].$data[2].$data[1].$data[0]);
				$val = hexdec($val[1]);
				$sectionO->setReloff($val);
				
				$data = substr($bin, 0, 4); // nreloc
				$bin = substr($bin, 4);
				$val = unpack('H*', $data[3].$data[2].$data[1].$data[0]);
				$val = hexdec($val[1]);
				$sectionO->setNreloc($val);
				
				$data = substr($bin, 0, 4); // flags
				$bin = substr($bin, 4);
				
				$data = substr($bin, 0, 4); // reserved1
				$bin = substr($bin, 4);
				
				$data = substr($bin, 0, 4); // reserved2
				$bin = substr($bin, 4);
				
				if($lcmd->getBinary()->getCpuType() | \TheFox\MachO\CPU_ARCH_ABI64){
					$data = substr($bin, 0, 4); // reserved3
					$bin = substr($bin, 4);
				}
				
				#$sections[] = $sectionO;
				$lcmd->addSection($sectionO);
			}
			
			#$lcmd->setSections($sections);
		}
		elseif($cmd == \TheFox\MachO\LC_MAIN){
			$lcmd = new LoadCommandEntryPoint();
			$lcmd->setBinary($binary);
			$lcmd->setCmd($cmd);
			$lcmd->setLength($length);
			
			$data = substr($bin, 0, 8); // entryoff
			$bin = substr($bin, 8);
			$val = unpack('H*', $data[7].$data[6].$data[5].$data[4].
				$data[3].$data[2].$data[1].$data[0]);
			$val = hexdec($val[1]);
			$lcmd->setEntryOff($val);
			
			$data = substr($bin, 0, 8); // stacksize
			$bin = substr($bin, 8);
			$val = unpack('H*', $data[7].$data[6].$data[5].$data[4].
				$data[3].$data[2].$data[1].$data[0]);
			$val = hexdec($val[1]);
			$lcmd->setStackSize($val);
		}
		else{
			$skipLen = $length - 4 - 4;
			#print '-> cmd: '.$cmd.': '.$cmd.' ('.(dechex(\TheFox\MachO\LC_MAIN)).') '.$length.' "'.$segname.'"'.PHP_EOL;
			$data = substr($bin, 0, $skipLen);
			$bin = substr($bin, $skipLen);
		}
		#print PHP_EOL;
		
		return $lcmd;
	}
	
}
