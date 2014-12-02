<?php namespace Korisu\Reserve;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReserveAllCommand extends Command
{

    /**
     * Configures the reserve:all command.
     */
    public function configure()
    {
        $this->setName('reserve:all')
          ->setDescription('Generate backups of your folders and databases.')
          ->addOption('folders', 'f', InputOption::VALUE_NONE, 'Don\'t include folders.')
          ->addOption('bzip', 'b', InputOption::VALUE_NONE, 'Use bzip to archive folders.')
          ->addOption('databases', 'd', InputOption::VALUE_NONE, 'Don\'t include databases.')
          ->addOption('gzip', 'g', InputOption::VALUE_NONE, 'Use gzip to archive databases.')
          ->addOption('all', 'a', InputOption::VALUE_NONE, 'Dump all accessible databases in a single file.');
    }

    /**
     * Executes the reserve:all command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return null|int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ( ! $input->getOption('folders')) {
            $this->runReserveFoldersCommand($input, $output);
        }
        if ( ! $input->getOption('databases')) {
            $this->runReserveDatabasesCommand($input, $output);
        }

        $this->writeInComment($input, $output);
    }

    /**
     * @param $input
     * @param $output
     * @throws \Exception
     */
    private function runReserveFoldersCommand($input, $output)
    {
        $command = $this->getApplication()->find('reserve:folders');

        $arguments = [
          'command' => 'reserve:folders',
          '--bzip'  => $input->getOption('bzip'),
        ];

        $input = new ArrayInput($arguments);

        $returnCode = $command->run($input, $output);

        $this->writeInError($output, $returnCode);
    }

    /**
     * @param $input
     * @param $output
     * @throws \Exception
     */
    private function runReserveDatabasesCommand($input, $output)
    {

        $command = $this->getApplication()->find('reserve:databases');

        $arguments = [
          'command' => 'reserve:databases',
          '--gzip'  => $input->getOption('gzip'),
          '--all'   => $input->getOption('all')
        ];

        $input = new ArrayInput($arguments);

        $returnCode = $command->run($input, $output);

        $this->writeInError($output, $returnCode);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function writeInComment(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('folders') && $input->getOption('databases')) {
            $output->writeln('<comment>You have chosen not to do anything.</comment>');
        }
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param string                                            $status
     */
    private function writeInError(OutputInterface $output, $status)
    {
        if ($status !== 0) {
            $output->writeln("<error>Failed with an exit code of $status</error>");
        }
    }
} 