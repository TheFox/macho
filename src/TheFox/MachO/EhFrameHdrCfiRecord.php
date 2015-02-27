<?php

namespace TheFox\MachO;

class EhFrameHdrCfiRecord{
	
	private $cie;
	private $fde;
	
	public function setCie($cie){
		$this->cie = $cie;
	}
	
	public function getCie(){
		return $this->cie;
	}
	
	public function setFde($fde){
		$this->fde = $fde;
	}
	
	public function getFde(){
		return $this->fde;
	}
	
}
