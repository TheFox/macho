<?php

namespace TheFox\MachO;

/**
 * @codeCoverageIgnore
 */
class EhFrameHdrRecord{
	
	private $length;
	private $extLength;
	private $cieId;
	private $version;
	private $augmentationString;
	private $ehData;
	private $codeAlignmentFactor;
	private $dataAlignmentFactor;
	private $augmentationLength;
	private $augmentationData;
	private $initialInstructions;
	private $padding;
	
	public function setLength($length){
		$this->length = $length;
	}
	
	public function getLength(){
		return $this->length;
	}
	
	public function setExtLength($extLength){
		$this->extLength = $extLength;
	}
	
	public function getExtLength(){
		return $this->extLength;
	}
	
	public function setCieId($cieId){
		$this->cieId = $cieId;
	}
	
	public function getCieId(){
		return $this->cieId;
	}
	
}
