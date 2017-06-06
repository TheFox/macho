<?php

namespace TheFox\MachO;

use RuntimeException;

class LoadCommandSegment extends LoadCommand
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $vmaddr;

    /**
     * @var int
     */
    private $vmsize;

    /**
     * @var int
     */
    private $fileoff;

    #private $filesize;
    #private $maxprot;
    #private $initprot;

    /**
     * @var int
     */
    private $nsects;

    #private $flags;

    /**
     * @var array
     */
    private $sections = [];

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param int $vmaddr
     */
    public function setVmAddr(int $vmaddr)
    {
        $this->vmaddr = $vmaddr;
    }

    /**
     * @return int
     */
    public function getVmAddr(): int
    {
        return $this->vmaddr;
    }

    /**
     * @param int $vmsize
     */
    public function setVmSize(int $vmsize)
    {
        $this->vmsize = $vmsize;
    }

    /**
     * @return int
     */
    public function getVmSize(): int
    {
        return $this->vmsize;
    }

    /**
     * @param int $fileoff
     */
    public function setFileOff(int $fileoff)
    {
        $this->fileoff = $fileoff;
    }

    /**
     * @return int
     */
    public function getFileOff(): int
    {
        return $this->fileoff;
    }

    /**
     * @param int $nsects
     */
    public function setNsects(int $nsects)
    {
        $this->nsects = $nsects;
    }

    /**
     * @return int
     */
    public function getNsects(): int
    {
        return $this->nsects;
    }

    /**
     * @param array $sections
     */
    public function setSections(array $sections)
    {
        $this->sections = $sections;
    }

    /**
     * @param LoadSection $section
     */
    public function addSection(LoadSection $section)
    {
        $name = $section->getName();
        $this->sections[$name] = $section;
    }

    /**
     * @return LoadSection[]
     */
    public function getSections(): array
    {
        return $this->sections;
    }

    /**
     * @param string $name
     * @return LoadSection|null
     */
    public function getSectionByName(string $name)
    {
        if (!array_key_exists($name, $this->sections)) {
            return null;
            //throw new RuntimeException('Section "' . $name . '" does not exist');
        }
        return $this->sections[$name];
    }
}
