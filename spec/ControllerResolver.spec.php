<?php

use function Eloquent\Phony\Kahlan\stub;
use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Interop\Http\Server\RequestHandlerInterface;

use Ellipse\Handlers\Controller;
use Ellipse\Handlers\ControllerResolver;
use Ellipse\Handlers\ControllerRequestHandler;

describe('ControllerResolver', function () {

    beforeEach(function () {

        $this->container = mock(ContainerInterface::class)->get();

        $this->delegate = stub();

    });

    describe('->__invoke()', function () {

        context('when the given element is a controller action string', function () {

            context('when the resolver has a controller namespace', function () {

                it('should return a new ControllerRequestHandler using the given controller action string prepended with the namespace', function () {

                    $resolver = new ControllerResolver('Namespace', $this->container, $this->delegate);

                    $test = $resolver('Controller@action:id');

                    $handler = new ControllerRequestHandler($this->container, new Controller('Namespace\\Controller@action:id'));

                    expect($test)->toEqual($handler);

                });

            });

            context('when the resolver do not have a controller namespace', function () {

                it('should return a new ControllerRequestHandler using the given controller action string', function () {

                    $resolver = new ControllerResolver('', $this->container, $this->delegate);

                    $test = $resolver('Controller@action:id');

                    $handler = new ControllerRequestHandler($this->container, new Controller('Controller@action:id'));

                    expect($test)->toEqual($handler);

                });

            });

        });

        context('when the given element is not a string', function () {

            it('should proxy the delegate', function () {

                $element = new class {};

                $handler = mock(RequestHandlerInterface::class)->get();

                $this->delegate->with($element)->returns($handler);

                $resolver = new ControllerResolver('', $this->container, $this->delegate);

                $test = $resolver($element);

                expect($test)->toBe($handler);

            });

        });

        context('when the given element is not a string formatted as a controller action string', function () {

            it('should proxy the delegate', function () {

                $element = 'element';

                $handler = mock(RequestHandlerInterface::class)->get();

                $this->delegate->with($element)->returns($handler);

                $resolver = new ControllerResolver('', $this->container, $this->delegate);

                $test = $resolver($element);

                expect($test)->toBe($handler);

            });

        });

    });

});
