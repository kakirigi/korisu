<?php namespace Korisu\Reserve;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReserveDepleteCommand extends ReserveCommand
{

    /**
     * Configures the reserve:deplete command.
     */
    public function configure()
    {
        $this->setName('reserve:deplete')
          ->setDescription('Remove all backups of your folders and databases except the latest one.')
          ->addOption('all', 'a', InputOption::VALUE_NONE, 'Delete all reserves.');
    }

    /**
     * Executes the reserve:deplete command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return null|int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $folderReserves   = $this->files->directories($this->config->get('reserve.output.folders.path'));
        $databaseReserves = $this->files->directories($this->config->get('reserve.output.databases.path'));

        $directories = $this->processReserveList($input, $folderReserves, $databaseReserves);

        $progress = $this->getHelper('progress');

        $progress->start($output, count($directories));

        foreach($directories as $directory)
        {
            $this->files->deleteDirectory($directory);
            $progress->advance();
        }

        $progress->finish();

        $output->writeln('<comment>Depletion has been completed.</comment>');
    }

    /**
     * @param $input
     * @param $folderReserves
     * @param $databaseReserves
     * @return array
     */
    private function processReserveList($input, $folderReserves, $databaseReserves)
    {
        if( ! $input->getOption('all'))
        {
            array_pop($folderReserves);
            array_pop($databaseReserves);
        }

        return array_merge($folderReserves, $databaseReserves);
    }
}