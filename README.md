# Laravel request analyzer

Laravel package for analyze request with timing, slow query and duplicate queries. Lot of things in bucket list. Feel free to contribute. 

## Installation

```
composer require kk-r/laravel-request-analyzer
```

## Configuration

Add the provider to your config/app.php:

```php
// in your config/app.php add the provider to the service providers key

'providers' => [
    kkr\laravelRequestAnalyze\Providers\RequestAnalyzeServiceProvider::class,
]
```

// You have add middleware for analyze requests individually
####Add inside app\Http\Kernal.php in Top mention
```php
use kkr\laravelRequestAnalyze\Middleware\RequestAnalyze as RequestAnalyzer;
```
####For individual route add inside $routeMiddleware and don't forget to add middleware in Route.
```php
protected $routeMiddleware => [
    .....
    'RequestAnalyzer' => RequestAnalyzer::class,
]
```
####OR for group level routes add inside middleware Groups
```php
protected $middlewareGroups => [
    .....
    RequestAnalyzer::class,
]
```
####OR for all routes add middleware globally
```php
protected $middleware => [
    .....
    RequestAnalyzer::class,
]
```

### License

This package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
