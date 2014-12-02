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