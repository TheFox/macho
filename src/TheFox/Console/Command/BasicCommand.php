<?php

namespace TheFox\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Liip\ProcessManager\ProcessManager;
use Liip\ProcessManager\PidFile;
use TheFox\Logger\Logger;
use TheFox\Logger\StreamHandler;

class BasicCommand extends Command
{
    /**
     * @var Logger
     */
    public $logger;

    /**
     * @var int
     */
    public $exit = 0;

    /**
     * @var string
     */
    private $pidFile;

    /**
     * @param int $exit
     */
    public function setExit(int $exit)
    {
        $this->exit = $exit;
    }

    /**
     * @return int
     */
    public function getExit(): int
    {
        return $this->exit;
    }

    /**
     * @return string
     */
    public function getLogfilePath(): string
    {
        return 'log/application.log';
    }

    /**
     * @return string
     */
    public function getPidfilePath(): string
    {
        return 'pid/application.pid';
    }

    /**
     * @deprecated
     * @param InputInterface $input
     */
    public function executePre(InputInterface $input)
    {
        $this->logger = new Logger($this->getName());
        $this->logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));
        $this->logger->pushHandler(new StreamHandler($this->getLogfilePath(), Logger::DEBUG));

        if ($input->hasOption('shutdown') && $input->getOption('shutdown')) {
            if (file_exists($this->getPidfilePath())) {
                $pid = file_get_contents($this->getPidfilePath());
                $this->logger->info('kill ' . $pid);
                posix_kill($pid, SIGTERM);
            }
            exit();
        } elseif ($input->hasOption('daemon') && $input->getOption('daemon')) {
            if (function_exists('pcntl_fork')) {
                $pid = pcntl_fork();
                if ($pid < 0 || $pid) {
                    exit();
                }

                $sid = posix_setsid();
                $this->signalHandlerSetup();

                $pid = pcntl_fork();
                if ($pid < 0 || $pid) {
                    exit();
                }

                umask(0);

                $this->stdStreamsSetup();
            }
        } else {
            $this->signalHandlerSetup();
        }

        $this->pidFile = new PidFile(new ProcessManager(), $this->getPidfilePath());
        $this->pidFile->acquireLock();
        $this->pidFile->setPid(getmypid());
    }

    /**
     * @deprecated
     */
    public function executePost()
    {
        $this->pidFile->releaseLock();
    }

    public function signalHandlerSetup()
    {
        if (function_exists('pcntl_signal')) {
            declare(ticks=1);
            pcntl_signal(SIGTERM, [$this, 'signalHandler']);
            pcntl_signal(SIGINT, [$this, 'signalHandler']);
            pcntl_signal(SIGHUP, [$this, 'signalHandler']);
        }
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

    private function stdStreamsSetup()
    {
        // global $STDIN, $STDOUT, $STDERR;

        fclose(STDIN);
        fclose(STDOUT);
        fclose(STDERR);
        // $STDIN = fopen('/dev/null', 'r');
        // $STDOUT = fopen('/dev/null', 'wb');
        // $STDERR = fopen('/dev/null', 'wb');
    }
}
