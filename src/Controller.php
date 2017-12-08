<?php declare(strict_types=1);

namespace Ellipse\Handlers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Ellipse\Container\ReflectionContainer;

use Ellipse\Resolvable\ResolvableCallableFactory;

class Controller
{
    /**
     * The controller action string.
     *
     * @var string
     */
    private $str;

    /**
     * Set up a controller with the given controller action string.
     *
     * @param string $str
     */
    public function __construct(string $str)
    {
        $this->str = $str;
    }

    /**
     * Return the response produced by the controller action string.
     *
     * @param \Psr\Http\Message\ServerRequestInterface  $request
     * @param \Ellipse\Container\ReflectionContrainer   $container
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function response(ServerRequestInterface $request, ReflectionContainer $container): ResponseInterface
    {
        $factory = new ResolvableCallableFactory;

        $parts = explode(':', $this->str);

        [$class, $method] = explode('@', $parts[0]);

        $attributes = array_filter(preg_split('/\s*,\s*/', $parts[1] ?? ''));

        $placeholders = array_map([$request, 'getAttribute'], $attributes);

        $controller = $container->get($class);

        return $factory([$controller, $method])->value($container, $placeholders);
    }
}
