<?php

namespace TheFox\Console\Command;

use RuntimeException;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use TheFox\MachO\Binary;

/**
 * @codeCoverageIgnore
 */
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
		$this->addArgument('path', InputArgument::REQUIRED, 'Path to the binary file.');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output){
		#$this->executePre($input, $output);
		
		if($path = $input->getArgument('path')){
			#\Doctrine\Common\Util\Debug::dump($path);
			$binary = new Binary($path);
			
			$all = false;
			if($input->hasOption('all') && $input->getOption('all')){
				$all = true;
				$output->writeln('path: '.$binary->getPath());
			}
			if($all || $input->hasOption('magic') && $input->getOption('magic')){
				$output->writeln('magic: '.$binary->getMagic());
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
