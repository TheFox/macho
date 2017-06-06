<?php

namespace TheFox\MachO;

use RuntimeException;

class Binary
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var int
     */
    private $magic;

    /**
     * @var int
     */
    private $cpuType;

    /**
     * @var int
     */
    private $cpuSubtype;

    /**
     * @var int
     */
    private $fileType;

    /**
     * @var
     */
    private $nCmds;

    /**
     * @var
     */
    private $sizeOfCmds;

    /**
     * @var
     */
    private $flags;

    /**
     * @var LoadCommandSegment[]|LoadCommandEntryPoint[]
     */
    private $loadCommands = [];

    /**
     * @var int
     */
    private $expectedFileSize;

    /**
     * @var string
     */
    private $expectedMd5sum;

    /**
     * @var int
     */
    private $mainEntryOffset = 0;

    /**
     * @var int
     */
    private $mainVmAddress = 0;

    /**
     * @var EhFrame
     */
    private $ehFrame;

    /**
     * Binary constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return int
     */
    public function getMagic(): int
    {
        return $this->magic;
    }

    /**
     * @return int
     */
    public function getCpuType(): int
    {
        return $this->cpuType;
    }

    /**
     * @return int
     */
    public function getCpuSubtype(): int
    {
        return $this->cpuSubtype;
    }

    /**
     * @return int
     */
    public function getFileType(): int
    {
        return $this->fileType;
    }

    /**
     * @return int
     */
    public function getNcmds(): int
    {
        return $this->nCmds;
    }

    /**
     * @return int
     */
    public function getSizeOfCmds(): int
    {
        return $this->sizeOfCmds;
    }

    /**
     * @return int
     */
    public function getFlags(): int
    {
        return $this->flags;
    }

    /**
     * @param int $expectedFileSize
     */
    public function setExpectedFileSize(int $expectedFileSize)
    {
        $this->expectedFileSize = $expectedFileSize;
    }

    /**
     * @return int
     */
    public function getExpectedFileSize(): int
    {
        return $this->expectedFileSize;
    }

    /**
     * @param string $expectedMd5sum
     */
    public function setExpectedMd5sum(string $expectedMd5sum)
    {
        $this->expectedMd5sum = $expectedMd5sum;
    }

    /**
     * @return string
     */
    public function getExpectedMd5sum(): string
    {
        return $this->expectedMd5sum;
    }

    /**
     * @return array
     */
    public function getLoadCommands()
    {
        return $this->loadCommands;
    }

    /**
     * @return int
     */
    public function getMainVmAddress(): int
    {
        return $this->mainVmAddress;
    }

    /**
     * @return EhFrame|null
     */
    public function getEhFrame()
    {
        if (!$this->ehFrame) {
            if (!$this->parseEhFrame()) {
                return null;
            }
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

    /**
     * @return bool
     */
    private function parseHeader(): bool
    {
        $fh = fopen($this->path, 'r');
        if (!$fh) {
            return false;
        }

        $data = fread($fh, 4); // magic
        $data = unpack('H*', strrev($data));
        $this->magic = hexdec($data[1]);

        $mhMagic = MachO::MH_MAGIC;
        $mhMagic64 = MachO::MH_MAGIC_64;

        if ($this->magic != $mhMagic && $this->magic != $mhMagic64) {
            throw new RuntimeException('Unknown file type.', 30);
        }

        $data = fread($fh, 4); // cputype
        $data = unpack('H*', strrev($data));
        $this->cpuType = hexdec($data[1]);

        $data = fread($fh, 4); // cpusubtype
        $data = unpack('H*', strrev($data));
        //$this->cpuSubtype = hexdec($data[1]) & ~MachO::CPU_SUBTYPE_LIB64;
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

        if ($this->cpuType & MachO::CPU_ARCH_ABI64) {
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
            $lcMainCmd = $this->loadCommands['LC_MAIN'];
            $this->mainEntryOffset = $lcMainCmd->getEntryOff();

            if (isset($this->loadCommands['__TEXT'])) {
                $textCmd = $this->loadCommands['__TEXT'];
                $this->mainVmAddress = $textCmd->getVmAddr();
                $this->mainVmAddress += $this->mainEntryOffset;
            }
        }

        fclose($fh);

        return true;
    }

    /**
     * @param string $segmentName
     * @param string $sectionName
     * @param int $offset
     * @param string $data
     * @return bool|int|null
     */
    public function write(string $segmentName, string $sectionName, int $offset, string $data)
    {
        $mode = 'r+';
        $fh = fopen($this->path, $mode);
        if (!$fh) {
            return null;
        }

        $rv = null;
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

        return $rv;
    }

    /**
     * @return bool
     */
    private function parseEhFrame(): bool
    {
        if (!isset($this->loadCommands['__TEXT'])) {
            return false;
        }

        $lcmd = $this->loadCommands['__TEXT'];

        $section = $lcmd->getSectionByName('__eh_frame');
        if (!$section) {
            return false;
        }

        $fh = fopen($this->path, 'r');
        if (!$fh) {
            return false;
        }

        $pos = $section->getOffset();
        rewind($fh);
        fseek($fh, $pos);
        $data = fread($fh, $section->getSize());

        fclose($fh);

        $this->ehFrame = EhFrame::fromBinaryWithoutHead($this, $data);

        return true;
    }
}
