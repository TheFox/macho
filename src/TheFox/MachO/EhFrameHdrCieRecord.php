<?php

namespace TheFox\MachO;

/**
 * @codeCoverageIgnore
 */
class EhFrameHdrCieRecord extends EhFrameHdrRecord{
	
	private $version;
	private $augmentationString;
	private $ehData;
	private $codeAlignmentFactor;
	private $dataAlignmentFactor;
	private $augmentationLength;
	private $augmentationData;
	private $initialInstructions;
	private $padding;
	
	public function setVersion($version){
		$this->version = $version;
	}
	
	public function getVersion(){
		return $this->version;
	}
	
	public function setAugmentationString($augmentationString){
		$this->augmentationString = $augmentationString;
	}
	
	public function getAugmentationString(){
		return $this->augmentationString;
	}
	
	public function setEhData($ehData){
		$this->ehData = $ehData;
	}
	
	public function getEhData(){
		return $this->ehData;
	}
	
	public function setCodeAlignmentFactor($codeAlignmentFactor){
		$this->codeAlignmentFactor = $codeAlignmentFactor;
	}
	
	public function getCodeAlignmentFactor(){
		return $this->codeAlignmentFactor;
	}
	
	public function setDataAlignmentFactor($dataAlignmentFactor){
		$this->dataAlignmentFactor = $dataAlignmentFactor;
	}
	
	public function getDataAlignmentFactor(){
		return $this->dataAlignmentFactor;
	}
	
	public function setAugmentationLength($augmentationLength){
		$this->augmentationLength = $augmentationLength;
	}
	
	public function getAugmentationLength(){
		return $this->augmentationLength;
	}
	
	public function setAugmentationData($augmentationData){
		$this->augmentationData = $augmentationData;
	}
	
	public function getAugmentationData(){
		return $this->augmentationData;
	}
	
	public function setInitialInstructions($initialInstructions){
		$this->initialInstructions = $initialInstructions;
	}
	
	public function getInitialInstructions(){
		return $this->initialInstructions;
	}
	
	public function setPadding($padding){
		$this->padding = $padding;
	}
	
	public function getPadding(){
		return $this->padding;
	}
	
}
