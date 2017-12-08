<?php declare(strict_types=1);

namespace Ellipse\Handlers;

use Interop\Container\ServiceProviderInterface;

class ControllerResolverServiceProvider implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [];
    }

    public function getExtensions()
    {
        return [
            'resolvers.controller.namespace' => function ($container, string $previous = null) {

                if (is_null($previous)) {

                    return '';

                }

                return $previous;

            },

            'ellipse.resolvers.middleware' => function ($container, callable $previous = null) {

                $namespace = $container->get('resolvers.controller.namespace');

                $previous = $previous ?: function ($element) {

                    return $element;

                };

                return new ControllerResolver($namespace, $container, $previous);

            },
        ];
    }
}
