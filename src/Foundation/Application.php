<?php namespace Korisu\Foundation;

use Korisu\Reserve\ReserveAllCommand;
use Korisu\Reserve\ReserveDatabasesCommand;
use Korisu\Reserve\ReserveDepleteCommand;
use Korisu\Reserve\ReserveFoldersCommand;
use Illuminate\Config\Repository as Config;
use Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{

    public $config;

    public function __construct(Config $config)
    {
        parent::__construct('Korisu (Little Squirrel)', '1.2');

        $this->config = $config;

        $this->registerCommands();
    }

    public function registerCommands()
    {
        $this->add(new ReserveAllCommand());
        $this->add(new ReserveFoldersCommand($this->config));
        $this->add(new ReserveDatabasesCommand($this->config));
        $this->add(new ReserveDepleteCommand($this->config));
    }

} 