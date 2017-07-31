<?php

namespace kkr\laravelRequestAnalyze;

use Illuminate\Support\Facades\DB;

class Analyzer
{
    protected $config;
    protected $log;
    protected $connections;

    /**
     * Log constructor.
     */
    public function __construct()
    {
        $this->config = config('requestAnalyzer');
        $this->log = new Log();
        $this->setDBConnections();
    }

    public function enableQueryLogs()
    {
        array_map(function($connection){
            DB::connection($connection)->enableQueryLog();
        }, $this->connections);
    }

    function disableQueryLogs()
    {
        array_map(function($connection){
            DB::connection($connection)->disableQueryLog();
        }, $this->connections);
    }

    public function getQueryLogs()
    {
        $queryLogs = [];
        array_map(function($connection) use(&$queryLogs){
            $queryLog = \DB::connection($connection)->getQueryLog();
            $queryLogs = array_merge($queryLogs, $queryLog);
        }, $this->connections);
        return $queryLogs;
    }

    protected function setDBConnections()
    {
        $connections = array_keys(config('database.connections'));
        if (!empty($this->config['db_connections'])) {
            $connections = $this->connections = $this->config['db_connections'];
        }
        $this->connections = $connections;
    }

    public function getExecutedQueries()
    {
        $queryLogs = $this->getQueryLogs();
        if (!empty($queryLogs)) {
            return $this->getQueryFormat($queryLogs);
        }
        return [];
    }

    protected function getQueryFormat($queries)
    {
        $data = [];
        $data['queries'] = [];
        $data['timings'] = [];
        $data['all_queries'] = [];
        array_map(function($query) use(&$data){
            $formattedQuery = $this->getQueryBindings($query['query'], $query['bindings']);
            array_push($data['queries'], $formattedQuery);
            array_push($data['timings'], $query['time']);
            array_push($data['all_queries'], [ $formattedQuery, $query['time'] ]);
        },$queries);
        $duplicateQueries = array_diff_assoc($data['queries'], array_unique($data['queries']));
        $maxQueries = $this->getMaxQuery($data);
        return [
            'queries' => $data['all_queries'],
            'duplicateQueries' => $duplicateQueries,
            'slowQuery' => $maxQueries
        ];
    }

    protected function getMaxQuery($data = [])
    {
        $query = [];
        if (!empty($data['timings']) && !empty($data['timings'])) {
            $maxTime   = max($data['timings']);
            $maxKey = array_search($maxTime, $data['timings']);
            $maxQuery = (!empty($data['queries'][$maxKey]) ? $data['queries'][$maxKey] : '');
            return [
                'query' => $maxQuery,
                'timings' => $maxTime
            ];
        }
        return $query;
    }

    public function getQueryBindings($query = '', $bindings = [])
    {
        if (!empty($bindings)) {
            // Format binding data for sql insertion
            foreach ($bindings as $i => $binding) {
                if (is_object($binding) && $binding instanceof \DateTime) {
                    $bindings[$i] = '\''.$binding->format('Y-m-d H:i:s').'\'';
                } elseif (is_null($binding)) {
                    $bindings[$i] = 'NULL';
                } elseif (is_bool($binding)) {
                    $bindings[$i] = $binding ? '1' : '0';
                } elseif (is_string($binding)) {
                    $bindings[$i] = "'$binding'";
                }
            }

            return preg_replace_callback(
                '/\?/',
                function () use (&$bindings) {
                    return array_shift($bindings);
                }, $query
            );
        }
        return $query;
    }

    public function logQuery($title, $data)
    {
        $this->log->write($title, $data);
    }

}
