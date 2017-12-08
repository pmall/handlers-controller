<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Interop\Http\Server\RequestHandlerInterface;

use Ellipse\Container\ReflectionContainer;

use Ellipse\Handlers\Controller;
use Ellipse\Handlers\ControllerRequestHandler;

describe('ControllerRequestHandler', function () {

    beforeEach(function () {

        $this->container = mock(ContainerInterface::class)->get();
        $this->controller = mock(Controller::class);

        $this->handler = new ControllerRequestHandler($this->container, $this->controller->get());

    });

    it('should implement RequestHandlerInterface', function () {

        expect($this->handler)->toBeAnInstanceOf(RequestHandlerInterface::class);

    });

    describe('->handle()', function () {

        it('should proxy the controller ->request() method with the given request and a reflection container', function () {

            $container = mock(ReflectionContainer::class)->get();

            allow(ReflectionContainer::class)->toBe($container);

            $request = mock(ServerRequestInterface::class)->get();
            $response = mock(ResponseInterface::class)->get();

            $this->controller->response->with($request, $container)->returns($response);

            $test = $this->handler->handle($request);

            expect($test)->toBe($response);

        });

    });

});

describe('ControllerRequestHandler', function () {

    beforeAll(function () {

        class TestController
        {
            private $dependency1;

            public function __construct(TestDependency1 $dependency1)
            {
                $this->dependency1 = $dependency1;
            }

            public function action(ServerRequestInterface $request, TestDependency2 $dependency2): ResponseInterface
            {
                if ($this->dependency1 == new TestDependency1($request)) {

                    return mock(ResponseInterface::class)->get();

                }
            }
        }

        class TestDependency1
        {
            private $request;

            public function __construct(ServerRequestInterface $request)
            {
                $this->request = $request;
            }
        }

        class TestDependency2
        {
            //
        }

    });

    describe('->handle()', function () {

        it('should execute the action by injecting the request and the request attributes', function () {

            $dependency2 = new TestDependency2;

            $container = mock(ContainerInterface::class);

            $exception = mock([Throwable::class, NotFoundExceptionInterface::class])->get();

            $container->get->with(TestController::class)->throws($exception);
            $container->get->with(TestDependency1::class)->throws($exception);
            $container->get->with(TestDependency2::class)->returns($dependency2);

            $request = mock(ServerRequestInterface::class);

            $request->getAttribute->with('a1')->returns('v1');
            $request->getAttribute->with('a2')->returns('v2');

            $handler = new ControllerRequestHandler($container->get(), new Controller('TestController@action:a1,a2'));

            $test = $handler->handle($request->get());

            expect($test)->toBeAnInstanceOf(ResponseInterface::class);

        });

    });

});
