<?php

// https://refspecs.linuxfoundation.org/LSB_3.0.0/LSB-Core-generic/LSB-Core-generic/ehframechpt.html

namespace TheFox\MachO;

use TheFox\Utilities\Leb128;

class EhFrame
{
    /**
     * @var EhFrameHdrCfiRecord[]
     */
    private $records = [];

    /**
     * @var Binary
     */
    private $binary;

    /**
     * @param EhFrameHdrCfiRecord $record
     */
    public function addRecord(EhFrameHdrCfiRecord $record)
    {
        $this->records[] = $record;
    }

    /**
     * @param Binary $binary
     */
    public function setBinary(Binary $binary)
    {
        $this->binary = $binary;
    }

    /**
     * @return Binary
     */
    public function getBinary(): Binary
    {
        return $this->binary;
    }

    /**
     * @param Binary $binary
     * @param string $bin
     * @return EhFrame
     */
    public static function fromBinaryWithoutHead(Binary $binary, string $bin): EhFrame
    {
        $ehframe = new Ehframe();
        $ehframe->setBinary($binary);

        while ($bin) {
            $pos = 0;

            $data = substr($bin, 0, 4); // Length
            $bin = substr($bin, 4);
            $val = unpack('H*', strrev($data));
            $val = hexdec($val[1]);
            $length = $val;
            $pos += 4;

            $extLength = 0;
            if ($length == 0xffffffff) {
                $data = substr($bin, 0, 8); // Extended Length
                $bin = substr($bin, 8);
                $val = unpack('H*', strrev($data));
                $val = hexdec($val[1]);
                $extLength = $val;
                $pos += 8;
            }

            $data = substr($bin, 0, 4); // CIE ID
            $bin = substr($bin, 4);
            $val = unpack('H*', strrev($data));
            $val = hexdec($val[1]);
            $cieId = $val;
            $pos += 4;

            $data = substr($bin, 0, 1); // Version
            $bin = substr($bin, 1);
            $val = unpack('H*', $data);
            $val = hexdec($val[1]);
            $version = $val;
            $pos++;

            // Augmentation String
            $augmentationString = '';
            $c = 0;
            do {
                $data = substr($bin, 0, 1);
                $bin = substr($bin, 1);
                $c = ord($data);
                if ($c) {
                    $augmentationString .= $data;
                }
                $pos++;
            } while ($c);

            // EH Data
            $ehData = '';
            if ($augmentationString == 'eh') {
                if ($binary->getCpuType() | MachO::CPU_ARCH_ABI64) {
                    $data = substr($bin, 0, 8);
                    $bin = substr($bin, 8);
                    $pos += 8;
                } else {
                    $data = substr($bin, 0, 4);
                    $bin = substr($bin, 4);
                    $pos += 4;
                }
                $val = unpack('H*', strrev($data));
                $val = hexdec($val[1]);
                $ehData = $val;
            }

            // Code Alignment Factor
            $codeAlignmentFactor = 0;
            $data = substr($bin, 0, 9);
            $lebLen = Leb128::udecode($data, $codeAlignmentFactor, 9);
            $bin = substr($bin, $lebLen);
            $pos += $lebLen;

            // Data Alignment Factor
            $dataAlignmentFactor = 0;
            $data = substr($bin, 0, 9);
            $lebLen = Leb128::sdecode($data, $dataAlignmentFactor, 9);
            $bin = substr($bin, $lebLen);
            $pos += $lebLen;

            // Augmentation
            $augmentationLength = 0;
            $augmentationData = '';
            if (strpos($augmentationString, 'z') !== false) {
                print 'Augmentation Data' . "\n";

                // Augmentation Length
                $data = substr($bin, 0, 9);
                $lebLen = Leb128::udecode($data, $augmentationLength, 9);
                $bin = substr($bin, $lebLen);
                $pos += $lebLen;
                print 'Augmentation Length: ' . $augmentationLength . "\n";

                // Augmentation Data
                $data = substr($bin, 0, $augmentationLength);
                $bin = substr($bin, $augmentationLength);
                $val = unpack('H*', strrev($data));
                $val = hexdec($val[1]);
                $augmentationData = $val;
                $pos += $augmentationLength;
                print 'Augmentation Data: ' . $augmentationData . "\n";
            } else {
                print 'no Augmentation Data' . "\n";
            }


            $left = $length - $pos;
            $data = substr($bin, 0, 12);
            $bin = substr($bin, 12);
            $val = unpack('H*', $data);
            $val = hexdec($val[1]);
            $initialInstructions = $val;
            $pos += 12;
            
            $padding = '';
            
            $cieRecord = new EhFrameHdrCieRecord();
            $cieRecord->setLength($length);
            $cieRecord->setExtLength($extLength);
            $cieRecord->setCieId($cieId);
            $cieRecord->setVersion($version);
            $cieRecord->setAugmentationString($augmentationString);
            $cieRecord->setEhData($ehData);
            $cieRecord->setCodeAlignmentFactor($codeAlignmentFactor);
            $cieRecord->setDataAlignmentFactor($dataAlignmentFactor);
            $cieRecord->setAugmentationLength($augmentationLength);
            $cieRecord->setAugmentationData($augmentationData);
            $cieRecord->setInitialInstructions($initialInstructions);
            $cieRecord->setPadding($padding);


            $pos = 0;

            $data = substr($bin, 0, 4); // Length
            $bin = substr($bin, 4);
            $val = unpack('H*', strrev($data));
            $val = hexdec($val[1]);
            $length = $val;
            $pos += 4;

            $extLength = 0;
            if ($length == 0xffffffff) {
                $data = substr($bin, 0, 8); // Extended Length
                $bin = substr($bin, 8);
                $val = unpack('H*', strrev($data));
                $val = hexdec($val[1]);
                $extLength = $val;
                $pos += 8;
            }

            $data = substr($bin, 0, 4); // CIE ID
            $bin = substr($bin, 4);
            $val = unpack('H*', strrev($data));
            $val = hexdec($val[1]);
            $cieId = $val;
            $pos += 4;

            $data = substr($bin, 0, 8); // PC Begin
            $bin = substr($bin, 8);
            $val = unpack('H*', strrev($data));
            $val = hexdec($val[1]);
            $pcBegin = $val;
            $pos += 8;

            $data = substr($bin, 0, 8); // PC Range
            $bin = substr($bin, 8);
            $val = unpack('H*', strrev($data));
            $val = hexdec($val[1]);
            $pcRange = $val;
            $pos += 8;

            // Augmentation
            $augmentationLength = 0;
            //$augmentationData = '';
            if (strpos($augmentationString, 'z') !== false) {
                print 'Augmentation Data' . "\n";

                // Augmentation Length
                $data = substr($bin, 0, 9);
                $lebLen = Leb128::udecode($data, $augmentationLength, 9);
                $bin = substr($bin, $lebLen);
                $pos += $lebLen;
                print 'Augmentation Length: ' . $augmentationLength . "\n";

                // Augmentation Data
                $data = substr($bin, 0, $augmentationLength);
                $bin = substr($bin, $augmentationLength);
                $val = unpack('H*', strrev($data));
                $val = hexdec($val[1]);
                $augmentationData = $val;
                $pos += $augmentationLength;
                print 'Augmentation Data: ' . $augmentationData . "\n";
            } else {
                print 'no Augmentation Data' . "\n";
            }


            $augmentationLength = 0;
            $augmentationData = '';
            $callFrameInstructions = '';
            $padding = '';

            print 'codeAlignmentFactor: ' . $codeAlignmentFactor . "\n";
            print 'dataAlignmentFactor: ' . $dataAlignmentFactor . "\n";
            print 'augmentationLength: ' . $augmentationLength . "\n";
            print 'initialInstructions: ' . $initialInstructions . "\n";
            print 'fde length: ' . $length . "\n";
            print 'fde ext length: ' . $extLength . "\n";
            print 'fde cie: 0x' . dechex($cieId) . "\n";
            print 'fde pc begin: 0x' . dechex($pcBegin) . "\n";
            print 'fde pc range: 0x' . dechex($pcRange) . "\n";


            $left = $length - $pos;
            print 'left: ' . $left . "\n";


            $fdeRecord = new EhFrameHdrFdeRecord();
            $fdeRecord->setPcBegin($pcBegin);
            $fdeRecord->setPcRange($pcRange);
            $fdeRecord->setAugmentationLength($augmentationLength);
            $fdeRecord->setAugmentationData($augmentationData);
            $fdeRecord->setCallFrameInstructions($callFrameInstructions);
            $fdeRecord->setPadding($padding);

            $cfiRecord = new EhFrameHdrCfiRecord();
            $cfiRecord->setCie($cieRecord);
            $cfiRecord->setFde($fdeRecord);

            $ehframe->addRecord($cfiRecord);
            
            break;
        }
        
        return $ehframe;
    }
}
