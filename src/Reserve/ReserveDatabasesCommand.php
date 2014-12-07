<?php namespace Korisu\Reserve;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReserveDatabasesCommand extends ReserveCommand
{

    /**
     * Configures the reserve:folders command.
     */
    public function configure()
    {
        $this->setName('reserve:databases')
          ->setDescription('Generate a dump or a gzipped archive of preconfigured databases as backup.')
          ->addOption('all', 'a', InputOption::VALUE_NONE,
            'Backup all accessible databases to a single file')
          ->addOption('gzip', 'g', InputOption::VALUE_NONE,
            'Check this flag if you\'d like the dump to be gzipped');
    }

    /**
     * Executes the reserve:folders command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return null|int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $databases = $this->config->get('reserve.databases');

        $this->verifyDatabasesToReserveExist($output, $databases);

        $progress = $this->getHelper('progress');

        $progress->start($output, count($databases));

        foreach ($databases as $filename => $database) {
            $this->generateMysqlDump($database, $filename, $input, $output, $progress);
        }

        $progress->finish();
    }

    /**
     * @param string                                            $database
     * @param string                                            $filename
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param                                                   $progress
     */
    protected function generateMysqlDump($database, $filename, InputInterface $input, OutputInterface $output, $progress)
    {
        $outputStream = [];
        $status = '';

        $username = $this->config->get('database.username');
        $password = $this->config->get('database.password');

        $outputPath = $this->config->get('reserve.output.databases.path').'/'.$this->carbon;

        if( ! $this->files->exists($outputPath)) $this->files->makeDirectory($outputPath);

        $errorPath  = $this->config->get('reserve.output.databases.errors');

        $gzip     = $input->getOption('gzip') ? '| gzip -9' : '';

        $filename = $input->getOption('gzip') ? "$filename.sql.gz" : "$filename.sql";

        if($input->getOption('all'))
        {
            $filename = $input->getOption('gzip') ? 'all.sql.gz' : 'all.sql';
            $database = '--all-databases';
        }

        $command = "mysqldump -u {$username} -p{$password} $database {$gzip} > {$outputPath}/$filename 2> {$errorPath}";

        exec($command, $outputStream, $status);

        $progress->advance();

        $this->writeInError($database, $output, $status);
        $this->writeInSuccess($database, $output, $status);

        if($input->getOption('all'))
        {
            exit($status);
        }
    }

    /**
     * @param string                                            $database
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param string                                            $status
     */
    private function writeInError($database, OutputInterface $output, $status)
    {
        if ($status !== 0) {
            $output->writeln("<error>  >> Failed to dump $database. (exit code $status)</error>");
        }
    }

    /**
     * @param string                                            $database
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param string                                            $status
     */
    private function writeInSuccess($database, $output, $status)
    {
        if ($status === 0) {
            $output->writeln("<info>  >> Successfully dumped $database.</info>");
        }
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param                                                   $databases
     */
    protected function verifyDatabasesToReserveExist(OutputInterface $output, $databases)
    {
        if (count($databases) === 0) {
            $output->writeln("<info>There are no databases to reserve.</info>");
            exit(0);
        }
    }


}