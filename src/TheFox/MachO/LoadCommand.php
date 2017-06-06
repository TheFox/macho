<?php

namespace TheFox\MachO;

class LoadCommand
{
    /**
     * @var int
     */
    private $cmd;

    /**
     * @var int
     */
    private $length;

    /**
     * @var Binary
     */
    private $binary;

    /**
     * @param int $cmd
     */
    public function setCmd(int $cmd)
    {
        $this->cmd = $cmd;
    }

    /**
     * @return int
     */
    public function getCmd(): int
    {
        return $this->cmd;
    }

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
     * @param int $cmd
     * @param int $length
     * @param string $bin
     * @return null|LoadCommandSegment|LoadCommandEntryPoint
     */
    public static function fromBinaryWithoutHead(Binary $binary, int $cmd, int $length, string $bin)
    {
        $lcmd = null;

        $archLen = 4;
        if ($binary->getCpuType() & MachO::CPU_ARCH_ABI64) {
            #print '  -> ABI64'."\n";
            $archLen = 8;
        }

        if ($cmd == MachO::LC_SEGMENT || $cmd == MachO::LC_SEGMENT_64) {
            #print '  -> LC_SEGMENT'."\n";

            $lcmd = new LoadCommandSegment();
            $lcmd->setBinary($binary);
            $lcmd->setCmd($cmd);
            $lcmd->setLength($length);

            $data = substr($bin, 0, 16); // segname
            $bin = substr($bin, 16);
            $val = strstr($data, "\0", true);
            $lcmd->setName($val);
            #print '  -> name: '.$val."\n";

            $data = substr($bin, 0, $archLen); // vmaddr
            $bin = substr($bin, $archLen);
            $val = unpack('H*', strrev($data));
            $val = hexdec($val[1]);
            $lcmd->setVmAddr($val);

            $data = substr($bin, 0, $archLen); // vmsize
            $bin = substr($bin, $archLen);
            $val = unpack('H*', strrev($data));
            $val = hexdec($val[1]);
            $lcmd->setVmSize($val);

            $data = substr($bin, 0, $archLen); // fileoff
            $bin = substr($bin, $archLen);
            $val = unpack('H*', strrev($data));
            $val = hexdec($val[1]);
            $lcmd->setFileOff($val);

            $data = substr($bin, 0, $archLen); // filesize
            $bin = substr($bin, $archLen);

            $data = substr($bin, 0, 4); // maxprot
            $bin = substr($bin, 4);

            $data = substr($bin, 0, 4); // initprot
            $bin = substr($bin, 4);

            $data = substr($bin, 0, 4); // nsects
            $bin = substr($bin, 4);
            $val = unpack('H*', strrev($data));
            $val = hexdec($val[1]);
            $lcmd->setNsects($val);
            $nsects = $val;

            $data = substr($bin, 0, 4); // flags
            $bin = substr($bin, 4);

            #print '  -> cmd: 0x'.dechex($cmd).' sections='.$nsects.PHP_EOL;
            #print '  -> cmd: '.$cmd.' '.$len.': 0x'.dechex($vmaddr).' 0x'.dechex($vmsize).' "'.$segname.'"'.PHP_EOL;

            #$sections = array();
            for ($sectionC = 0; $sectionC < $nsects; $sectionC++) {
                #print '    -> sec'."\n";

                $sectionO = new LoadSection();
                $sectionO->setLoadCommand($lcmd);

                $data = substr($bin, 0, 16); // sectname
                $bin = substr($bin, 16);
                $val = strstr($data, "\0", true);
                #print "sec name '$val'\n";
                $sectionO->setName($val);

                $data = substr($bin, 0, 16); // segname
                $bin = substr($bin, 16);

                $data = substr($bin, 0, $archLen); // addr
                $bin = substr($bin, $archLen);
                $addr = unpack('H*', strrev($data));
                $addr = hexdec($addr[1]);
                $sectionO->setAddr($addr);

                $data = substr($bin, 0, $archLen); // size
                $bin = substr($bin, $archLen);
                $size = unpack('H*', strrev($data));
                $size = hexdec($size[1]);
                $sectionO->setSize($size);

                $data = substr($bin, 0, 4); // offset
                $bin = substr($bin, 4);
                $val = unpack('H*', strrev($data));
                $val = hexdec($val[1]);
                $sectionO->setOffset($val);

                $data = substr($bin, 0, 4); // align
                $bin = substr($bin, 4);

                $data = substr($bin, 0, 4); // reloff
                $bin = substr($bin, 4);
                $val = unpack('H*', strrev($data));
                $val = hexdec($val[1]);
                $sectionO->setReloff($val);

                $data = substr($bin, 0, 4); // nreloc
                $bin = substr($bin, 4);
                $val = unpack('H*', strrev($data));
                $val = hexdec($val[1]);
                $sectionO->setNreloc($val);

                $data = substr($bin, 0, 4); // flags
                $bin = substr($bin, 4);

                $data = substr($bin, 0, 4); // reserved1
                $bin = substr($bin, 4);

                $data = substr($bin, 0, 4); // reserved2
                $bin = substr($bin, 4);

                if ($lcmd->getBinary()->getCpuType() & MachO::CPU_ARCH_ABI64) {
                    $data = substr($bin, 0, 4); // reserved3
                    $bin = substr($bin, 4);
                }

                #$sections[] = $sectionO;
                $lcmd->addSection($sectionO);
            }

            #$lcmd->setSections($sections);
        } elseif ($cmd == MachO::LC_MAIN) {
            $lcmd = new LoadCommandEntryPoint();
            $lcmd->setBinary($binary);
            $lcmd->setCmd($cmd);
            $lcmd->setLength($length);

            $data = substr($bin, 0, 8); // entryoff
            $bin = substr($bin, 8);
            $val = unpack('H*', strrev($data));
            $val = hexdec($val[1]);
            $lcmd->setEntryOff($val);

            $data = substr($bin, 0, 8); // stacksize
            $bin = substr($bin, 8);
            $val = unpack('H*', strrev($data));
            $val = hexdec($val[1]);
            $lcmd->setStackSize($val);
        } else {
            $skipLen = $length - 4 - 4;
            $data = substr($bin, 0, $skipLen);
            $bin = substr($bin, $skipLen);

            #print '  -> skip: '.$skipLen."\n";
        }
        #print PHP_EOL;

        return $lcmd;
    }
}
