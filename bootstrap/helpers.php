<?php

if ( ! function_exists('application_path'))
{
    /**
     * Get the path to the application folder.
     *
     * @param  string  $path
     * @return string
     */
    function application_path($path = '')
    {
        return __DIR__.'/../'.($path ? '/'.$path : $path);
    }
}

if ( ! function_exists('get_configuration'))
{
    /**
     * Get the path to the application folder.
     *
     * @param  string  $path
     * @return string
     */
    function get_configuration()
    {
        $instance = null;

        if (null === $instance) {
            $fileloader = new Illuminate\Config\FileLoader(
              new Illuminate\Filesystem\Filesystem,
              __DIR__ . '/../config'
            );

            $instance = new Illuminate\Config\Repository($fileloader, getenv('APP_ENV'));
        }

        return $instance;
    }
}