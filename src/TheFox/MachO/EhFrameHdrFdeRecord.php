<?php

namespace TheFox\MachO;

class EhFrameHdrFdeRecord extends EhFrameHdrRecord
{
    /**
     * @var int
     */
    private $pcBegin;

    /**
     * @var int
     */
    private $pcRange;

    /**
     * @var int
     */
    private $augmentationLength;

    /**
     * @var string
     */
    private $augmentationData;

    /**
     * @var string
     */
    private $callFrameInstructions;

    /**
     * @var string
     */
    private $padding;

    /**
     * @param int $pcBegin
     */
    public function setPcBegin(int $pcBegin)
    {
        $this->pcBegin = $pcBegin;
    }

    /**
     * @return int
     */
    public function getPcBegin(): int
    {
        return $this->pcBegin;
    }

    /**
     * @param int $pcRange
     */
    public function setPcRange(int $pcRange)
    {
        $this->pcRange = $pcRange;
    }

    /**
     * @return int
     */
    public function getPcRange(): int
    {
        return $this->pcRange;
    }

    /**
     * @param int $augmentationLength
     */
    public function setAugmentationLength(int $augmentationLength)
    {
        $this->augmentationLength = $augmentationLength;
    }

    /**
     * @return int
     */
    public function getAugmentationLength(): int
    {
        return $this->augmentationLength;
    }

    /**
     * @param string $augmentationData
     */
    public function setAugmentationData(string $augmentationData)
    {
        $this->augmentationData = $augmentationData;
    }

    /**
     * @return string
     */
    public function getAugmentationData(): string
    {
        return $this->augmentationData;
    }

    /**
     * @param string $callFrameInstructions
     */
    public function setCallFrameInstructions(string $callFrameInstructions)
    {
        $this->callFrameInstructions = $callFrameInstructions;
    }

    /**
     * @return string
     */
    public function getCallFrameInstructions(): string
    {
        return $this->callFrameInstructions;
    }

    /**
     * @param string $padding
     */
    public function setPadding(string $padding)
    {
        $this->padding = $padding;
    }

    /**
     * @return string
     */
    public function getPadding(): string
    {
        return $this->padding;
    }
}
