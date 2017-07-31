<?php

namespace kkr\laravelRequestAnalyze;

use Illuminate\Log\Writer;
use Monolog\Logger;

class Log
{
    /**
     * Set the logger name
     * @var string
     */
    protected $loggerName;

    /**
     * File Name
     * @var string
     */
    protected $file;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $extension = '.log';

    /**
     * @var string
     */
    protected $title;

    /**
     * Available: emergency, alert, critical, error, warning, notice, info and debug
     * @var string
     */
    protected $level;

    /**
     * @var \Illuminate\Log\Writer
     */
    protected $log;
    /**
     * @var string
     */
    protected $filePath;

    /**
     * Log constructor.
     */
    function __construct()
    {
        $config = config('requestAnalyzer');
        $this->level = 'info';
        $this->loggerName = $config['logger_name'];
        $this->path = storage_path($config['directory']) ;
        $this->file = $this->setFileName($config['file_name']);
        $this->logDays = $config['request_log'];
    }

    /**
     * Set the file name
     *
     * @param $name
     * @return string
     */
    protected function setFileName($name)
    {
        return $name . $this->extension;
    }


    /**
     * Write in log file.
     * @param $title string
     * @param $message array
     * @return bool
     */
    function write($title = '', $message = [])
    {
        $this->title = $title. "\n";
        $this->setLogger();
        $fullPathToFile = $this->getFilePath();

        if($this->logDays == 'single')
            $this->log->useFiles($fullPathToFile);
        else
            $this->log->useDailyFiles($fullPathToFile);

        $this->log->write('debug', $this->title, $message);

        return true;
    }

    /**
     * instance of \Illuminate\Log\Writer
     */
    private function setLogger()
    {
        $this->log = new Writer(new Logger($this->loggerName));
    }



    /**
     * Concat path and filename
     * @return string
     */
    private function getFilePath()
    {
        return $this->addSlashToLast($this->path) . $this->file;
    }

    /**
     * Add slash at end.
     *
     * @param $path
     * @return string
     */
    private function addSlashToLast($path)
    {
        if(substr($path,-1) != '/')
            return $path . '/';

        return $path;
    }

}
