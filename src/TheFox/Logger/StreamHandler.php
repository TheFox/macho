<?php

namespace TheFox\Logger;

class StreamHandler
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var
     */
    private $level;

    /**
     * StreamHandler constructor.
     * 
     * @param string $path
     * @param int $level
     */
    public function __construct(string $path, int $level)
    {
        $this->setPath($path);
        $this->setLevel($level);
    }

    /**
     * @param string $path
     */
    public function setPath(string $path)
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
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param string $level
     */
    public function setLevel(string $level)
    {
        $this->level = $level;
    }
}
