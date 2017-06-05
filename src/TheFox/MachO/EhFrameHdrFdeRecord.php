<?php

namespace TheFox\MachO;

class EhFrameHdrFdeRecord extends EhFrameHdrRecord
{
    private $pcBegin;
    private $pcRange;
    private $augmentationLength;
    private $augmentationData;
    private $callFrameInstructions;
    private $padding;

    public function setPcBegin($pcBegin)
    {
        $this->pcBegin = $pcBegin;
    }

    public function getPcBegin()
    {
        return $this->pcBegin;
    }

    public function setPcRange($pcRange)
    {
        $this->pcRange = $pcRange;
    }

    public function getPcRange()
    {
        return $this->pcRange;
    }

    public function setAugmentationLength($augmentationLength)
    {
        $this->augmentationLength = $augmentationLength;
    }

    public function getAugmentationLength()
    {
        return $this->augmentationLength;
    }

    public function setAugmentationData($augmentationData)
    {
        $this->augmentationData = $augmentationData;
    }

    public function getAugmentationData()
    {
        return $this->augmentationData;
    }

    public function setCallFrameInstructions($callFrameInstructions)
    {
        $this->callFrameInstructions = $callFrameInstructions;
    }

    public function getCallFrameInstructions()
    {
        return $this->callFrameInstructions;
    }

    public function setPadding($padding)
    {
        $this->padding = $padding;
    }

    public function getPadding()
    {
        return $this->padding;
    }
}
