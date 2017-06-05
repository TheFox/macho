<?php

namespace TheFox\MachO;

class LoadSection
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $addr;

    /**
     * @var int
     */
    private $size;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $reloff;

    /**
     * @var int
     */
    private $nreloc;

    /**
     * Parent
     *
     * @var LoadCommand
     */
    private $loadCommand;

    /**
     * @return string
     */
    public function __toString(): string
    {
        if ($this->name) {
            return $this->name;
        }
        return '0x' . dechex($this->getAddr());
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
     * @param int $addr
     */
    public function setAddr(int $addr)
    {
        $this->addr = $addr;
    }

    /**
     * @return int
     */
    public function getAddr(): int
    {
        return $this->addr;
    }

    /**
     * @param int $size
     */
    public function setSize(int $size)
    {
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $offset
     */
    public function setOffset(int $offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param int $reloff
     */
    public function setReloff(int $reloff)
    {
        $this->reloff = $reloff;
    }

    /**
     * @return int
     */
    public function getReloff(): int
    {
        return $this->reloff;
    }

    /**
     * @param int $nreloc
     */
    public function setNreloc(int $nreloc)
    {
        $this->nreloc = $nreloc;
    }

    /**
     * @return int
     */
    public function getNreloc(): int
    {
        return $this->nreloc;
    }

    /**
     * @param LoadCommand $loadCommand
     */
    public function setLoadCommand(LoadCommand $loadCommand)
    {
        $this->loadCommand = $loadCommand;
    }

    /**
     * @return LoadCommand
     */
    public function getLoadCommand(): LoadCommand
    {
        return $this->loadCommand;
    }
}
