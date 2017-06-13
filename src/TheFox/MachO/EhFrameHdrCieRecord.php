<?php

namespace TheFox\MachO;

class EhFrameHdrCieRecord extends EhFrameHdrRecord
{
    /**
     * @var int
     */
    private $version;

    /**
     * @var string
     */
    private $augmentationString;

    /**
     * @var string
     */
    private $ehData;

    /**
     * @var int
     */
    private $codeAlignmentFactor;

    /**
     * @var int
     */
    private $dataAlignmentFactor;

    /**
     * @var int
     */
    private $augmentationLength;

    /**
     * @var string
     */
    private $augmentationData;

    /**
     * @var int
     */
    private $initialInstructions;

    /**
     * @var string
     */
    private $padding;

    /**
     * @param int $version
     */
    public function setVersion(int $version)
    {
        $this->version = $version;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @param string $augmentationString
     */
    public function setAugmentationString(string $augmentationString)
    {
        $this->augmentationString = $augmentationString;
    }

    /**
     * @return string
     */
    public function getAugmentationString(): string
    {
        return $this->augmentationString;
    }

    /**
     * @param string $ehData
     */
    public function setEhData(string $ehData)
    {
        $this->ehData = $ehData;
    }

    /**
     * @return string
     */
    public function getEhData(): string
    {
        return $this->ehData;
    }

    /**
     * @param int $codeAlignmentFactor
     */
    public function setCodeAlignmentFactor(int $codeAlignmentFactor)
    {
        $this->codeAlignmentFactor = $codeAlignmentFactor;
    }

    /**
     * @return int
     */
    public function getCodeAlignmentFactor(): int
    {
        return $this->codeAlignmentFactor;
    }

    /**
     * @param int $dataAlignmentFactor
     */
    public function setDataAlignmentFactor(int $dataAlignmentFactor)
    {
        $this->dataAlignmentFactor = $dataAlignmentFactor;
    }

    /**
     * @return int
     */
    public function getDataAlignmentFactor(): int
    {
        return $this->dataAlignmentFactor;
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
     * @param int $initialInstructions
     */
    public function setInitialInstructions(int $initialInstructions)
    {
        $this->initialInstructions = $initialInstructions;
    }

    /**
     * @return int
     */
    public function getInitialInstructions(): int
    {
        return $this->initialInstructions;
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
