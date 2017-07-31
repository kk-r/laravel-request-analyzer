<?php

namespace krishna\laravelRequestAnalyze\Middleware;

use Closure;
use krishna\laravelRequestAnalyze\Analyzer;
use krishna\laravelRequestAnalyze\Jobs\RequestAnalysis;

class RequestAnalyze
{
    protected $startTime;
    protected $analyzer;

    public function __construct(Analyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string|null              $ability
     * @param string|null              $boundModelName
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $ability = null, $boundModelName = null)
    {
        $this->analyzer->enableQueryLogs();
        $this->startTime = microtime(false);
        return $next($request);
    }

    public function terminate($request, $response) {
        $responseTime = microtime(false) - $this->startTime;
        $data = $this->analyzer->getExecutedQueries();
        $data['response_time'] = $responseTime;

        $title = $request->getMethod().' - '.$request->route()->uri();

        dispatch(new RequestAnalysis($title, $data));
        $this->analyzer->disableQueryLogs();
    }
}