<?php

namespace TheFox\Console\Command;

use RuntimeException;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use TheFox\MachO\Binary;
use TheFox\MachO\LoadCommandSegment;
use TheFox\MachO\LoadCommandEntryPoint;

class BinaryCommand extends BasicCommand{
	
	public function getLogfilePath(){
		return 'log/binary.log';
	}
	
	public function getPidfilePath(){
		return 'pid/binary.pid';
	}
	
	protected function configure(){
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
	
	protected function execute(InputInterface $input, OutputInterface $output){
		#$this->executePre($input, $output);
		
		if($path = $input->getArgument('path')){
			$binary = new Binary($path);
			$binary->analyze();
			
			$all = false;
			if($input->hasOption('all') && $input->getOption('all')){
				$all = true;
				$output->writeln('path: '.$binary->getPath());
			}
			if($all || $input->hasOption('magic') && $input->getOption('magic')){
				$output->writeln('magic: '.dechex($binary->getMagic()));
			}
			if($all || $input->hasOption('cpu') && $input->getOption('cpu')){
				$abi64 = 0;
				$abi64 = $binary->getCpuType() & \TheFox\MachO\CPU_ARCH_ABI64;
				$out = 'cpu: 0x'.dechex($binary->getCpuType()).' ';
				$out .= '0x'.dechex($binary->getCpuSubtype()).' ';
				$out .= ($abi64 ? '64' : '32').'-bit';
				$output->writeln($out);
			}
			if($all || $input->hasOption('filetype') && $input->getOption('filetype')){
				$output->writeln('filetype: '.$binary->getFileType());
			}
			if($all || $input->hasOption('ncmds') && $input->getOption('ncmds')){
				$output->writeln('ncmds: '.$binary->getNCmds());
			}
			if($all || $input->hasOption('sizeofcmds') && $input->getOption('sizeofcmds')){
				$output->writeln('sizeofcmds: '.$binary->getSizeOfCmds());
			}
			if($all || $input->hasOption('flags') && $input->getOption('flags')){
				$output->writeln('flags: '.$binary->getFlags());
			}
			if($all || $input->hasOption('main') && $input->getOption('main')){
				$output->writeln('main: 0x'.dechex($binary->getMainVmAddress()));
			}
			if($all || $input->hasOption('segments') && $input->getOption('segments')){
				foreach($binary->getLoadCommands() as $lcmdName => $lcmd){
					if($lcmd instanceof LoadCommandSegment){
						$output->writeln('segment: '.$lcmd.' ('.$lcmd->getNsects().' 0x'.dechex($lcmd->getVmAddr()).')');
						foreach($lcmd->getSections() as $sectionId => $section){
							$out = "\t".' section: '.$sectionId.' ';
							$out .= '"'.$section->getName().'" (0x'.dechex($section->getAddr()).' 0x'.dechex($section->getOffset()).')';
							$output->writeln($out);
						}
					}
					elseif($lcmd instanceof LoadCommandEntryPoint){
						$output->writeln('segment: '.$lcmd.' (0x'.dechex($lcmd->getEntryOff()).' 0x'.dechex($lcmd->getStackSize()).')');
					}
				}
			}
		}
		
		#$this->executePost();
	}
	
	public function signalHandler($signal){
		$this->exit++;
		
		switch($signal){
			case SIGTERM:
				$this->log->notice('signal: SIGTERM');
				break;
			case SIGINT:
				print PHP_EOL;
				$this->log->notice('signal: SIGINT');
				break;
			case SIGHUP:
				$this->log->notice('signal: SIGHUP');
				break;
			case SIGQUIT:
				$this->log->notice('signal: SIGQUIT');
				break;
			case SIGKILL:
				$this->log->notice('signal: SIGKILL');
				break;
			case SIGUSR1:
				$this->log->notice('signal: SIGUSR1');
				break;
			default:
				$this->log->notice('signal: N/A');
		}
		
		$this->log->notice('main abort ['.$this->exit.']');
		
		if($this->exit >= 2){
			exit(1);
		}
	}
	
}
