<?php namespace Korisu\Reserve;

use Carbon\Carbon;
use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ReserveCommand extends Command
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
    function __construct()
    {
        parent::__construct();

        $this->carbon = Carbon::now()->format('Y-m-d-H-i-s');
        $this->files  = new Filesystem();
        $this->config = get_configuration();
    }

}