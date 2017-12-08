# Controller request handlers resolver

This package provides a resolver producing Psr-15 request handlers from controller action strings using a Psr-11 container.

**Require** php >= 7.1

**Installation** `composer require ellipse/handlers-controller`

**Run tests** `./vendor/bin/kahlan`

- [Using the controller request handlers resolver](#using-the-controller-request-handlers-resolver)

## Using the controller request handlers resolver

Controller action strings are string containing a class name and method name spaced by `@`. A comma separated list of request attributes to inject can be added, spaced by `:`.

Example: `'App\Controller@index'` or `'App\Controller@show:category_id,post_id'`.

Many things to note:

- The controller instances are resolved using [ellipse/container-reflection](https://github.com/ellipsephp/container-reflection) auto-wiring feature.
- The controller methods are called using [ellipse/resolvable-callable](https://github.com/ellipsephp/resolvable-callable) callable dependency injection feature.
- When one parameter of the controller constructor or controller method is type hinted as `Psr\Http\Message\ServerRequestInterface`, the one received by the request handler `->handle()` method is injected.
- The non class type hinted parameters of the controller method will be the specified request attributes, in the order they are listed in the controller action string.

```php
<?php

namespace App;

class SomeController
{
    private $response;

    public function __construct(ResponseFactory $response)
    {
        // Dependencies are automatically injected.

        $this->response = $response;
    }

    public function index(ServerRequestInterface $request, $request_attribute)
    {
        // $request is the request available at the time the request handler is being processed.
        // $request_attribute is the request attribute named 'id'.

        // some processing ...

        // returns a response.
        return $this->response->createResponse();
    }
}
```

```php
<?php

namespace App;

use Some\Psr11Container;

use Ellipse\Handlers\ControllerResolver;

// Get a Psr-11 container.
$container = new Psr11Container;

// Create a resolver with a base namespace for container classes, the Psr-11 container
// and a delegate for non controller action string elements.
$resolver = new ControllerResolver('App', $container, function ($element) {

    // $element is not a controller action string, just return it.

    return $element;

});

// Create a request handler based on SomeController's index method and injecting the 'id'
// request attribute.
$handler = $resolver('SomeController@index:id');
```
