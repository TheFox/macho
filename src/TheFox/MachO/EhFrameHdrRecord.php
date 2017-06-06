<?php

namespace TheFox\MachO;

class EhFrameHdrRecord
{
    /**
     * @var int
     */
    private $length;

    /**
     * @var int
     */
    private $extLength;

    /**
     * @var int
     */
    private $cieId;

//    private $version;
//    private $augmentationString;
//    private $ehData;
//    private $codeAlignmentFactor;
//    private $dataAlignmentFactor;
//    private $augmentationLength;
//    private $augmentationData;
//    private $initialInstructions;
//    private $padding;

    /**
     * @param int $length
     */
    public function setLength(int $length)
    {
        $this->length = $length;
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @param int $extLength
     */
    public function setExtLength(int $extLength)
    {
        $this->extLength = $extLength;
    }

    /**
     * @return int
     */
    public function getExtLength(): int
    {
        return $this->extLength;
    }

    /**
     * @param int $cieId
     */
    public function setCieId(int $cieId)
    {
        $this->cieId = $cieId;
    }

    /**
     * @return int
     */
    public function getCieId(): int
    {
        return $this->cieId;
    }
}
