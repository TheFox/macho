<?php

namespace TheFox\MachO;

use RuntimeException;
use TheFox\Utilities\Bin;

class Binary
{
    private $path;
    private $magic;
    private $cpuType;
    private $cpuSubtype;
    private $fileType;
    private $nCmds;
    private $sizeOfCmds;
    private $flags;

    private $loadCommands = array();

    private $expectedFileSize = null;
    private $expectedMd5sum = null;
    private $mainEntryOffset = 0;
    private $mainVmAddress = 0;
    private $ehFrame = null;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getMagic()
    {
        return $this->magic;
    }

    public function getCpuType()
    {
        return $this->cpuType;
    }

    public function getCpuSubtype()
    {
        return $this->cpuSubtype;
    }

    public function getFileType()
    {
        return $this->fileType;
    }

    public function getNcmds()
    {
        return $this->nCmds;
    }

    public function getSizeOfCmds()
    {
        return $this->sizeOfCmds;
    }

    public function getFlags()
    {
        return $this->flags;
    }

    public function setExpectedFileSize($expectedFileSize)
    {
        $this->expectedFileSize = $expectedFileSize;
    }

    public function getExpectedFileSize()
    {
        return $this->expectedFileSize;
    }

    public function setExpectedMd5sum($expectedMd5sum)
    {
        $this->expectedMd5sum = $expectedMd5sum;
    }

    public function getExpectedMd5sum()
    {
        return $this->expectedMd5sum;
    }

    public function getLoadCommands()
    {
        return $this->loadCommands;
    }

    public function getMainVmAddress()
    {
        return $this->mainVmAddress;
    }

    public function getEhFrame()
    {
        if (!$this->ehFrame) {
            $this->parseEhFrame();
        }
        return $this->ehFrame;
    }

    private function printPos($fh)
    {
        print '-> pos: 0x' . dechex(ftell($fh)) . PHP_EOL;
    }

    public function analyze()
    {
        if (!$this->path) {
            throw new RuntimeException('No Path.', 10);
        }

        if (!file_exists($this->path)) {
            throw new RuntimeException('File ' . $this->path . ' does not exist.', 11);
        }

        $this->parseHeader();

        if ($this->expectedFileSize !== null && filesize($this->path) != $this->expectedFileSize) {
            throw new RuntimeException("File size doesn't match the expected value. " . $this->expectedFileSize, 20);
        }

        if ($this->expectedMd5sum !== null
            && md5_file($this->path) != $this->expectedMd5sum
        ) {
            throw new RuntimeException("MD5 sum doesn't match the expected value. " . $this->expectedMd5sum, 21);
        }
    }

    private function parseHeader()
    {
        $fh = fopen($this->path, 'r');
        if ($fh) {

            $data = fread($fh, 4); // magic
            $data = unpack('H*', strrev($data));
            $this->magic = hexdec($data[1]);

            if ($this->magic == \TheFox\MachO\MH_MAGIC
                || $this->magic == \TheFox\MachO\MH_MAGIC_64
            ) {

                $data = fread($fh, 4); // cputype
                $data = unpack('H*', strrev($data));
                $this->cpuType = hexdec($data[1]);

                $data = fread($fh, 4); // cpusubtype
                $data = unpack('H*', strrev($data));
                #$this->cpuSubtype = hexdec($data[1]) & ~\TheFox\MachO\CPU_SUBTYPE_LIB64;
                $this->cpuSubtype = hexdec($data[1]);
                #print "sub: 0x".dechex($this->cpuSubtype)."\n";

                $data = fread($fh, 4); // filetype
                $data = unpack('H*', strrev($data));
                $this->fileType = hexdec($data[1]);

                $data = fread($fh, 4); // ncmds
                $data = unpack('H*', strrev($data));
                $this->nCmds = hexdec($data[1]);

                $data = fread($fh, 4); // sizeofcmds
                $data = unpack('H*', strrev($data));
                $this->sizeOfCmds = hexdec($data[1]);

                $data = fread($fh, 4); // flags
                $data = unpack('H*', strrev($data));
                $this->flags = hexdec($data[1]);

                if ($this->cpuType & \TheFox\MachO\CPU_ARCH_ABI64) {
                    // reserved
                    $data = fread($fh, 4);
                }

                for ($cmdN = 0; $cmdN < $this->nCmds; $cmdN++) {
                    $cmdsData = fread($fh, 4); // cmd
                    $cmd = unpack('H*', strrev($cmdsData));
                    $cmd = hexdec($cmd[1]);

                    $cmdsData = fread($fh, 4); // cmdsize
                    $cmdsize = unpack('H*', strrev($cmdsData));
                    $cmdsize = hexdec($cmdsize[1]);

                    #print 'cmd '.$cmdN.': 0x'.dechex($cmd).' 0x'.dechex($cmdsize)."\n";

                    $cmdsData = fread($fh, $cmdsize - 8);
                    $lcmd = LoadCommand::fromBinaryWithoutHead($this, $cmd, $cmdsize, $cmdsData);
                    if ($lcmd) {
                        $this->loadCommands[(string)$lcmd] = $lcmd;
                    }
                }

                if (isset($this->loadCommands['LC_MAIN'])) {
                    $this->mainEntryOffset = $this->loadCommands['LC_MAIN']->getEntryOff();

                    if (isset($this->loadCommands['__TEXT'])) {
                        #\Doctrine\Common\Util\Debug::dump($this->loadCommands['__TEXT'], 3);
                        $this->mainVmAddress = $this->loadCommands['__TEXT']->getVmAddr();
                        $this->mainVmAddress += $this->mainEntryOffset;
                    }
                }
            } else {
                throw new RuntimeException('Unknown file type.', 30);
            }

            fclose($fh);
        }
    }

    public function write($segmentName, $sectionName, $offset, $data)
    {
        $rv = null;

        $mode = 'r+';
        $fh = fopen($this->path, $mode);
        if ($fh) {
            if (isset($this->loadCommands[$segmentName])) {
                $pos = 0;

                $lcmd = $this->loadCommands[$segmentName];
                if ($lcmd instanceof LoadCommandSegment) {
                    $section = $lcmd->getSectionByName($sectionName);
                    $pos = $section->getOffset() + $offset;
                }

                if ($pos) {
                    rewind($fh);
                    fseek($fh, $pos);
                    $rv = fwrite($fh, $data);
                }
            }
            #else{ print "seg '$segmentName' not found\n"; }
            fclose($fh);
        }

        return $rv;
    }

    private function parseEhFrame()
    {
        if (isset($this->loadCommands['__TEXT'])) {
            $lcmd = $this->loadCommands['__TEXT'];
            #\Doctrine\Common\Util\Debug::dump($lcmd);

            $section = $lcmd->getSectionByName('__eh_frame');

            #\Doctrine\Common\Util\Debug::dump($section, 1);

            $fh = fopen($this->path, 'r');
            if ($fh) {
                $pos = $section->getOffset();
                rewind($fh);
                fseek($fh, $pos);
                $data = fread($fh, $section->getSize());

                #$data = unpack('H*', $data);
                #\Doctrine\Common\Util\Debug::dump($data);
                fclose($fh);

                $this->ehFrame = EhFrame::fromBinaryWithoutHead($this, $data);
            }
        }
    }
}
