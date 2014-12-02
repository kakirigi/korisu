<?php namespace Korisu\Reserve;

use Carbon\Carbon;
use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReserveFoldersCommand extends Command
{

    const GZIP_FLAG = 'z';
    const BZIP_FLAG = 'j';
    const GZIP_EXTENSION = 'tar.gz';
    const BZIP_EXTENSION = 'tar.bz2';

    /**
     * Get an instance of application configuration.
     *
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * Get an instance of Carbon.
     *
     * @var \Carbon\Carbon
     */
    protected $carbon;

    /**
     * Get an instance of Carbon.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Get a new instance of the ReserveGenerateCommand.
     *
     * @param \Illuminate\Config\Repository $config
     */
    function __construct(Config $config)
    {
        parent::__construct();

        $this->config = $config;

        $this->carbon = Carbon::now()->format('Y-m-d-H-i-s');
        $this->files  = new Filesystem();
    }

    /**
     * Configures the reserve:folders command.
     */
    public function configure()
    {
        $this->setName('reserve:folders')
             ->setDescription('Generate a zip archive for preconfigured folders as backup.')
             ->addOption('bzip', 'b', InputOption::VALUE_NONE,
               'Check this flag if you\'d like to use bzip instead of gzip');
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
        $folders = $this->config->get('reserve.folders');

        $this->verifyFoldersToReserveExist($folders, $output);

        $progress = $this->getHelper('progress');

        $progress->start($output, count($folders));

        foreach ($folders as $filename => $source) {
            $this->verifySourcePathExists($source, $output);
            $this->generateArchive($source, $filename, $input, $output, $progress);
        }

        $progress->finish();
    }

    /**
     * @param string                                            $source
     * @param string                                            $filename
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function generateArchive($source, $filename, InputInterface $input, OutputInterface $output, $progress)
    {
        $outputStream = [];
        $status = '';

        $format = $this->getArchiveFormat($input);
        $extension = $this->getArchiveExtension($input);

        $outputPath = $this->config->get('reserve.output.folders.path').'/'.$this->carbon;

        if( ! $this->files->exists($outputPath)) $this->files->makeDirectory($outputPath);

        $errorPath  = $this->config->get('reserve.output.folders.errors');

        $excluded = $this->getExcluded();

        exec("tar {$excluded} -{$format}cf {$outputPath}/{$filename}.{$extension} {$source} 2> {$errorPath}", $outputStream, $status);

        $progress->advance();

        $this->writeInProgress($output, $outputStream);
        $this->writeInError($output, $status);
        $this->writeInSuccess($source, $output, $status);
    }

    /**
     * @param array                                             $folders
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function verifyFoldersToReserveExist($folders, $output)
    {
        if(count($folders) === 0) {
            $output->writeln("<info>There are no folders to reserve.</info>");
            exit(0);
        }
    }

    /**
     * @param string                                            $source
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function verifySourcePathExists($source, OutputInterface $output)
    {
        if ( ! is_dir($source)) {
            $output->writeln("<error>{$source} doesn't exist.</error>");
            exit(1);
        }
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @return string
     */
    private function getArchiveFormat($input)
    {
        return $input->getOption('bzip') ? self::BZIP_FLAG : self::GZIP_FLAG;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @return string
     */
    private function getArchiveExtension($input)
    {
        return $input->getOption('bzip') ? self::BZIP_EXTENSION : self::GZIP_EXTENSION;
    }

    /**
     * @return string
     */
    private function getExcluded()
    {
        return '--exclude=".*\bnode_modules\b"';
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param array                                             $outputStream
     */
    private function writeInProgress(OutputInterface $output, $outputStream)
    {
        foreach ($outputStream as $outputLine) {
            $output->writeln("<fg=magenta>  >>  Including file: $outputLine</fg=magenta>");
        }
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param string                                            $status
     */
    private function writeInError(OutputInterface $output, $status)
    {
        if ($status !== 0) {
            $output->writeln("<error>  >>  Failed with an exit code of $status</error>");
        }
    }

    /**
     * @param                                                   $folder
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param string                                            $status
     */
    private function writeInSuccess($folder, $output, $status)
    {
        if ($status === 0) {
            $output->writeln("<info>  >>  Successfully archived the folder '$folder'.</info>");
        }
    }


}