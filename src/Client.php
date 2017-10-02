<?php
declare(strict_types=1);

namespace FreezyBee\SmokeTester;

use FreezyBee\SmokeTester\Http\Request;
use FreezyBee\SmokeTester\Http\Response;
use FreezyBee\SmokeTester\Http\Session;
use Nette\Application\BadRequestException;
use Nette\Application\Helpers;
use Nette\Application\IRouter;
use Nette\Application\Request as AppRequest;
use Nette\Application\Application;
use Nette\DI\Container;
use Nette\Http\UserStorage;
use Nette\Security\IIdentity;
use Nette\SmartObject;
use Nette\Security\User;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class Client
{
    use SmartObject;

    /** @var Container */
    private $container;

    /** @var array */
    private $config;

    /** @var Request */
    private $httpRequest;

    /** @var Response */
    private $httpResponse;

    /** @var IIdentity|null */
    private $identity;

    /** @var string|null */
    private $identityNamespace;

    /**
     * @param Container $container
     * @param array $config
     */
    public function __construct(Container $container, array $config)
    {
        $this->container = $container;

        $config['baseUrl'] = $config['baseUrl'] ?? 'http://localhost/';
        $config['catchExceptions'] = $config['catchExceptions'] ?? false;

        $this->config = $config;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $params
     * @return Response
     */
    public function uriRequest(string $method, string $uri, array $params = []): Response
    {
        if (preg_match('/^http(s|):\/\//', $uri) === 0) {
            $uri = $this->config['baseUrl'] . $uri;
        }

        $request = new Request($method, $uri, $params['headers'] ?? [], $params['body'] ?? '');
        return $this->processRequest($request);
    }

    /**
     * @param string $method
     * @param string $destination
     * @param array $parameters
     * @return Response
     */
    public function netteRequest(string $method, string $destination, array $parameters = []): Response
    {
        [$presenter, $action] = Helpers::splitName($destination);

        $appRequest = new AppRequest($presenter, $method, ['action' => $action] + $parameters);
        $httpRequest = new Request($method, $this->config['baseUrl']);

        /** @var IRouter $router */
        $router = $this->container->getByType(IRouter::class);
        $url = $router->constructUrl($appRequest, $httpRequest->getUrl());

        if ($url === null) {
            throw new BadRequestException($httpRequest->getUrl());
        }

        return $this->processRequest(new Request($method, $url));
    }

    /**
     * @param IIdentity $identity
     * @param string|null $namespace
     */
    public function login(IIdentity $identity, string $namespace = null): void
    {
        $this->identity = $identity;
        $this->identityNamespace = $namespace;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->httpResponse;
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Throwable
     */
    private function processRequest(Request $request): Response
    {
        $this->httpRequest = $request;
        $this->container->removeService('httpRequest');
        $this->container->addService('httpRequest', $this->httpRequest);

        $this->httpResponse = new Response();
        $this->container->removeService('httpResponse');
        $this->container->addService('httpResponse', $this->httpResponse);

        $this->container->removeService('session');
        $this->container->addService('session', new Session($this->httpRequest, $this->httpResponse));

        // login
        if ($this->identity !== null) {
            /** @var User $user */
            $user = $this->container->getByType(User::class);

            if ($this->identityNamespace !== null) {
                /** @var UserStorage $storage */
                $storage = $user->getStorage();
                $storage->setNamespace($this->identityNamespace);
            }

            $user->login($this->identity);
        }

        /** @var Application $application */
        $application = $this->container->getByType(Application::class);

        ob_start();

        try {
            $application->catchExceptions = $this->config['catchExceptions'];
            $application->run();
        } catch (\Throwable $exception) {
            if ($exception instanceof BadRequestException) {
                throw new BadRequestException($this->httpRequest->getUrl(), $exception->getCode(), $exception);
            }

            throw $exception;
        } finally {
            $this->httpResponse->setContent(ob_get_clean());
        }

        return $this->httpResponse;
    }
}
