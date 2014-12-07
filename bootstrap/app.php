<?php

/*
|--------------------------------------------------------------------------
| Setup Environment and Configuration
|--------------------------------------------------------------------------
|
| Here we set up our environment and create a new configuration instance.
|
*/

define('APP_ENV', 'development', true);

$config = get_configuration();

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Next thing we will do is create a new Little Squirrel application instance.
|
*/

$app = new \Korisu\Foundation\Application();

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/
return $app;

