<?php

namespace TheFox\MachO;

class LoadCommandSegment extends LoadCommand
{
    /**
     * @var string
     */
    private $name;
    private $vmaddr;
    private $vmsize;
    private $fileoff;
    #private $filesize;
    #private $maxprot;
    #private $initprot;
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

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setVmAddr($vmaddr)
    {
        $this->vmaddr = $vmaddr;
    }

    public function getVmAddr()
    {
        return $this->vmaddr;
    }

    public function setVmSize($vmsize)
    {
        $this->vmsize = $vmsize;
    }

    public function getVmSize()
    {
        return $this->vmsize;
    }

    public function setFileOff($fileoff)
    {
        $this->fileoff = $fileoff;
    }

    public function getFileOff()
    {
        return $this->fileoff;
    }

    public function setNsects($nsects)
    {
        $this->nsects = $nsects;
    }

    public function getNsects()
    {
        return $this->nsects;
    }

    public function setSections(array $sections)
    {
        $this->sections = $sections;
    }

    public function addSection(LoadSection $section)
    {
        $name = (string)$section;
        $this->sections[$name] = $section;
    }

    public function getSections(): array
    {
        return $this->sections;
    }

    public function getSectionByName($name): LoadSection
    {
        return $this->sections[$name];
    }
}
