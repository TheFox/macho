<?php

namespace TheFox\MachO;

class LoadCommandEntryPoint extends LoadCommand
{
    /**
     * @var int
     */
    private $entryoff;

    /**
     * @var int
     */
    private $stacksize;

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'LC_MAIN';
    }

    /**
     * @param int $entryoff
     */
    public function setEntryOff(int $entryoff)
    {
        $this->entryoff = $entryoff;
    }

    /**
     * @return int
     */
    public function getEntryOff(): int
    {
        return $this->entryoff;
    }

    /**
     * @param int $stacksize
     */
    public function setStackSize(int $stacksize)
    {
        $this->stacksize = $stacksize;
    }

    /**
     * @return int
     */
    public function getStackSize(): int
    {
        return $this->stacksize;
    }
}
