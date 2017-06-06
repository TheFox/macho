<?php

namespace TheFox\Console\Command;

//use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TheFox\MachO\MachO;

class InfoCommand extends BasicCommand
{
    /**
     * @return string
     */
    public function getLogfilePath(): string
    {
        return 'log/info.log';
    }

    /**
     * @return string
     */
    public function getPidfilePath(): string
    {
        return 'pid/info.pid';
    }

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
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        #$this->executePre($input, $output);

        if ($input->hasOption('name') && $input->getOption('name')) {
            print MachO::NAME;
        } elseif ($input->hasOption('name_lc') && $input->getOption('name_lc')) {
            print strtolower(MachO::NAME);
        } elseif ($input->hasOption('version_number') && $input->getOption('version_number')) {
            print MachO::VERSION;
        }

        #$this->executePost();
    }

    /**
     * @param int $signal
     */
    public function signalHandler(int $signal)
    {
        $this->exit++;

        switch ($signal) {
            case SIGTERM:
                $this->logger->notice('signal: SIGTERM');
                break;
            case SIGINT:
                print PHP_EOL;
                $this->logger->notice('signal: SIGINT');
                break;
            case SIGHUP:
                $this->logger->notice('signal: SIGHUP');
                break;
            case SIGQUIT:
                $this->logger->notice('signal: SIGQUIT');
                break;
            case SIGKILL:
                $this->logger->notice('signal: SIGKILL');
                break;
            case SIGUSR1:
                $this->logger->notice('signal: SIGUSR1');
                break;
            default:
                $this->logger->notice('signal: N/A');
        }

        $this->logger->notice('main abort [' . $this->exit . ']');

        if ($this->exit >= 2) {
            exit(1);
        }
    }
}
