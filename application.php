#!/usr/bin/env php
<?php

require_once __DIR__.'/bootstrap.php';

use Symfony\Component\Console\Application;

use TheFox\MachO\MachO;
use TheFox\Console\Command\InfoCommand;
use TheFox\Console\Command\BinaryCommand;


$application = new Application(MachO::NAME, MachO::VERSION);
$application->add(new InfoCommand());
$application->add(new BinaryCommand());
$application->run();
