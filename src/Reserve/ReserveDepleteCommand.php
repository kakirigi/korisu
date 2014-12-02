<?php namespace Korisu\Reserve;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Illuminate\Config\Repository as Config;

class ReserveDepleteCommand extends Command
{

    /**
     * Get an instance of application configuration.
     *
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * Get an instance of Carbon.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Get a new instance of the ReserveDepleteCommand.
     *
     * @param \Illuminate\Config\Repository $config
     */
    function __construct(Config $config)
    {
        parent::__construct();

        $this->config = $config;

        $this->files  = new Filesystem();
    }

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