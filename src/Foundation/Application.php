<?php namespace Korisu\Foundation;

use Korisu\Reserve\ReserveAllCommand;
use Korisu\Reserve\ReserveDatabasesCommand;
use Korisu\Reserve\ReserveDepleteCommand;
use Korisu\Reserve\ReserveFoldersCommand;
use Illuminate\Config\Repository as Config;
use Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{

    const VERSION = '1.2.2';

    protected $name = 'Korisu (Little Squirrel)';

    public function __construct()
    {
        parent::__construct($this->name, $this::VERSION);

        $this->registerCommands();
    }

    public function registerCommands()
    {
        $this->add(new ReserveAllCommand());
        $this->add(new ReserveFoldersCommand());
        $this->add(new ReserveDatabasesCommand());
        $this->add(new ReserveDepleteCommand());
    }

    public function applicationName()
    {
        return $this->name;
    }

} 