<?php
return [
    /**
     * Database connections for log
     * Default  - array_keys(config('database.connections'))
     */
    'db_connections' => [],
    /**
     * logger name for log along with type
     */
    'logger_name' => 'request-analyze',
    /**
     * Directory inside storage folder
     * default 'storage/
     */
    'directory' => '/logs/request/',
    /**
     * Directory where log files will be saved
     */
    'file_name' => 'request-analyze',
    /**
     * Files stored daily basis or single file
     */
    'request_log' => 'daily'
];