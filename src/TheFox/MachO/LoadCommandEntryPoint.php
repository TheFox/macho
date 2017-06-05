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

    public function setEntryOff(int $entryoff)
    {
        $this->entryoff = $entryoff;
    }

    public function getEntryOff(): int
    {
        return $this->entryoff;
    }

    public function setStackSize(int $stacksize)
    {
        $this->stacksize = $stacksize;
    }

    public function getStackSize(): int
    {
        return $this->stacksize;
    }
}
