<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Ellipse\Container\ReflectionContainer;

use Ellipse\Resolvable\ResolvableCallable;
use Ellipse\Resolvable\ResolvableCallableFactory;

use Ellipse\Handlers\Controller;

describe('Controller', function () {

    beforeEach(function () {

        $this->factory = mock(ResolvableCallableFactory::class);

        allow(ResolvableCallableFactory::class)->toBe($this->factory->get());

    });

    describe('->response()', function () {

        beforeEach(function () {

            $controller = mock(['action' => function () {}])->get();

            $this->request = mock(ServerRequestInterface::class);
            $this->response = mock(ResponseInterface::class)->get();

            $this->container = mock(ReflectionContainer::class);

            $this->resolvable = mock(ResolvableCallable::class);

            $this->container->get->with('Controller')->returns($controller);

            $this->factory->__invoke->with([$controller, 'action'])->returns($this->resolvable);

            $this->resolvable->value->returns($this->response);

        });

        context('when the controller action string has attribute values', function () {

            it('should resolve the controller action using the attribute values as placeholders', function () {

                $this->request->getAttribute->with('a1')->returns('v1');
                $this->request->getAttribute->with('a2')->returns('v2');

                $controller = new Controller('Controller@action:a1,a2');

                $test = $controller->response($this->request->get(), $this->container->get());

                expect($test)->toBe($this->response);

                $this->resolvable->value->calledWith($this->container, ['v1', 'v2']);

            });

        });

        context('when the controller action string do not have attribute values', function () {

            it('should resolve the controller action using the attribute values as placeholders', function () {

                $controller = new Controller('Controller@action');

                $test = $controller->response($this->request->get(), $this->container->get());

                expect($test)->toBe($this->response);

                $this->resolvable->value->calledWith($this->container, []);

            });

        });

    });

});
