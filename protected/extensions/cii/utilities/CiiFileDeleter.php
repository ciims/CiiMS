<?php

class CiiFileDeleter
{
	/**
     * Terrifying function that recursively deletes a directory
     *
     * @url http://php.net/manual/en/function.rmdir.php
     * @param string $dir     The directory that we want to delete
     * @return boolean
     */
    public static function removeDirectory($dir = '')
    {
        if ($dir == '' || $dir == NULL || $dir == '/')
            return false;

        $files = array_diff(scandir($dir), array('.','..')); 
        foreach ($files as $file)
            (is_dir("$dir/$file")) ? CiiFileDeleter::removeDirectory("$dir/$file") : unlink("$dir/$file"); 

        return rmdir($dir); 
    }
}