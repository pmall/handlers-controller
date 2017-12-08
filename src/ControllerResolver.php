<?php declare(strict_types=1);

namespace Ellipse\Handlers;

use Psr\Container\ContainerInterface;

use Interop\Http\Server\RequestHandlerInterface;

class ControllerResolver
{
    /**
     * The controller namespace.
     *
     * @var string
    */
    private $namespace;

    /**
     * The container.
     *
     * @var \Psr\Container\ContainerInterface
    */
    private $container;

    /**
     * The delegate.
     *
     * @var callable
    */
    private $delegate;

    /**
     * Set up a controller resolver with the given base namespace, container and
     * delegate.
     *
     * @param string                            $namespace
     * @param \Psr\Container\ContainerInterface $container
     * @param callable                          $delegate
     */
    public function __construct(string $namespace, ContainerInterface $container, callable $delegate)
    {
        $this->namespace = $namespace;
        $this->container = $container;
        $this->delegate = $delegate;
    }

    /**
     * Create an action request handler from the action string.
     *
     * @param mixed $element
     * @return \Interop\Http\Server\RequestHandlerInterface
     */
    public function __invoke($element): RequestHandlerInterface
    {
        if (is_string($element) && strpos($element, '@') !== false) {

            $str = $this->namespace == '' ? $element : $this->namespace . '\\' . $element;

            return new ControllerRequestHandler($this->container, new Controller($str));

        }

        return ($this->delegate)($element);
    }
}
