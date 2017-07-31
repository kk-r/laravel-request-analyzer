<?php

namespace krishna\laravelRequestAnalyze\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use krishna\laravelRequestAnalyze\Analyzer;

class RequestAnalysis implements ShouldQueue
{

    public $data;
    public $title;
    public $analyzer;

    /**
     * Create a Request analysis event.
     *
     * @param   $logTitle String
     * @param   $logData array
     * @return void
     */
    public function __construct($logTitle, $logData = [])
    {
        $this->data = $logData;
        $this->title = $logTitle;
    }

    public function handle()
    {
        $analyzer = new Analyzer();
        $analyzer->logQuery($this->title, $this->data);
    }
}