<?php

namespace TheFox\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TheFox\MachO\MachO;

class InfoCommand extends Command
{
    protected function configure()
    {
        $this->setName('info');
        $this->setDescription('Show infos about this application.');
        $this->addOption('name', null, InputOption::VALUE_NONE, 'Prints the name of this application.');
        $this->addOption('name_lc', null, InputOption::VALUE_NONE, 'Prints the lower-case name of this application.');
        $this->addOption('version_number', null, InputOption::VALUE_NONE, 'Prints the version of this application.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->hasOption('name') && $input->getOption('name')) {
            print MachO::NAME;
        } elseif ($input->hasOption('name_lc') && $input->getOption('name_lc')) {
            print strtolower(MachO::NAME);
        } elseif ($input->hasOption('version_number') && $input->getOption('version_number')) {
            print MachO::VERSION;
        }
        
        return 0;
    }
}
