<?php

namespace TheFox\Console\Command;

//use RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TheFox\MachO\MachO;
use TheFox\MachO\Binary;
use TheFox\MachO\LoadCommandSegment;
use TheFox\MachO\LoadCommandEntryPoint;

class BinaryCommand extends BasicCommand
{
    /**
     * @return string
     */
    public function getLogfilePath(): string
    {
        return 'log/binary.log';
    }

    /**
     * @return string
     */
    public function getPidfilePath(): string
    {
        return 'pid/binary.pid';
    }

    protected function configure()
    {
        $this->setName('binary');
        $this->setDescription('Show infos about a binary file.');

        $this->addOption('all', null, InputOption::VALUE_NONE, 'Print all informations.');
        $this->addOption('magic', null, InputOption::VALUE_NONE, 'Prints the magic number.');
        $this->addOption('cpu', null, InputOption::VALUE_NONE, 'Prints the cpu.');
        $this->addOption('filetype', null, InputOption::VALUE_NONE, 'Prints the filetype.');
        $this->addOption('ncmds', null, InputOption::VALUE_NONE, 'Prints the ncmds.');
        $this->addOption('sizeofcmds', null, InputOption::VALUE_NONE, 'Prints the sizeofcmds.');
        $this->addOption('flags', null, InputOption::VALUE_NONE, 'Prints the flags.');
        $this->addOption('segments', null, InputOption::VALUE_NONE, 'Prints segments.');
        $this->addOption('main', null, InputOption::VALUE_NONE, 'Prints the entry point of the main() function.');

        $this->addArgument('path', InputArgument::REQUIRED, 'Path to the binary file.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');
        if (!$path) {
            return 1;
        }
        
        $binary = new Binary($path);
        $binary->analyze();

        $all = false;
        if ($input->hasOption('all') && $input->getOption('all')) {
            $all = true;
            $output->writeln('path: ' . $binary->getPath());
        }
        if ($all || $input->hasOption('magic') && $input->getOption('magic')) {
            $output->writeln('magic: ' . dechex($binary->getMagic()));
        }
        if ($all || $input->hasOption('cpu') && $input->getOption('cpu')) {
            //$abi64 = 0;
            $abi64 = $binary->getCpuType() & MachO::CPU_ARCH_ABI64;
            $out = 'cpu: 0x' . dechex($binary->getCpuType()) . ' ';
            $out .= '0x' . dechex($binary->getCpuSubtype()) . ' ';
            $out .= ($abi64 ? '64' : '32') . '-bit';
            $output->writeln($out);
        }
        if ($all || $input->hasOption('filetype') && $input->getOption('filetype')) {
            $output->writeln('filetype: ' . $binary->getFileType());
        }
        if ($all || $input->hasOption('ncmds') && $input->getOption('ncmds')) {
            $output->writeln('ncmds: ' . $binary->getNcmds());
        }
        if ($all || $input->hasOption('sizeofcmds') && $input->getOption('sizeofcmds')) {
            $output->writeln('sizeofcmds: ' . $binary->getSizeOfCmds());
        }
        if ($all || $input->hasOption('flags') && $input->getOption('flags')) {
            $output->writeln('flags: ' . $binary->getFlags());
        }
        if ($all || $input->hasOption('main') && $input->getOption('main')) {
            $output->writeln('main: 0x' . dechex($binary->getMainVmAddress()));
        }
        if ($all || $input->hasOption('segments') && $input->getOption('segments')) {
            foreach ($binary->getLoadCommands() as $lcmdName => $lcmd) {
                if ($lcmd instanceof LoadCommandSegment) {
                    $output->writeln('segment: ' . $lcmd . ' (' . $lcmd->getNsects() . ' 0x' . dechex($lcmd->getVmAddr()) . ')');
                    
                    $sections = $lcmd->getSections();
                    foreach ($sections as $sectionId => $section) {
                        $out = "\t" . ' section: ' . $sectionId . ' ';
                        $out .= '"' . $section->getName() . '" (0x' . dechex($section->getAddr()) . ' 0x' . dechex($section->getOffset()) . ')';
                        
                        $output->writeln($out);
                    }
                } elseif ($lcmd instanceof LoadCommandEntryPoint) {
                    $output->writeln('segment: ' . $lcmd . ' (0x' . dechex($lcmd->getEntryOff()) . ' 0x' . dechex($lcmd->getStackSize()) . ')');
                }
            }
        }

        return 0;
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
