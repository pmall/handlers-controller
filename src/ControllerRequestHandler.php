<?php declare(strict_types=1);

namespace Ellipse\Handlers;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Interop\Http\Server\RequestHandlerInterface;

use Ellipse\Container\ReflectionContainer;
use Ellipse\Container\OverriddenContainer;

class ControllerRequestHandler implements RequestHandlerInterface
{
    /**
     * The container.
     *
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * The controller to use to produce a response.
     *
     * @var \Ellipse\Handlers\Controller
     */
    private $controller;

    /**
     * Set up a controller request handler with the given container and
     * controller.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @param \Ellipse\Handlers\Controller      $controller
     */
    public function __construct(ContainerInterface $container, Controller $controller)
    {
        $this->container = $container;
        $this->controller = $controller;
    }

    /**
     * Use the controller to produce a response from the given request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface  $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $container = new ReflectionContainer(
            new OverriddenContainer($this->container, [
                ServerRequestInterface::class => $request,
            ])
        );

        return $this->controller->response($request, $container);
    }
}
