<?php

namespace AlejandroHerr\Stack\BenchLogger;

use \Pimple;
use Silpion\Stack\Logger\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use \Ubench;

class BenchLogger implements HttpKernelInterface
{
    protected $app;
    protected $container;
    protected $bench;

    public function __construct(HttpKernelInterface $app, array $options = array())
    {
        $this->app = $app;
        $this->container = $this->setupContainer($options);
        $this->bench = new Ubench();
        $this->bench->start();
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $response = $this->app->handle($request, $type, $catch);

        $this->bench->end();

        $msg = sprintf('Benchmark for "%s %s"', $request->getMethod(), $request->getRequestUri());
        $map = array(
            'time' => $this->bench->getTime(false, '%d%s'),
            'memory' => $this->bench->getMemoryPeak(false, '%.3f%s')
        );
        $this->log($msg, $map);

        return $response;
    }

    protected function log($msg, array $context = array())
    {
        $logger = $this->container['logger'];
        $logger->log($this->container['log_level'], $msg, $context);
    }

    protected function setupContainer(array $options)
    {
        $containerBuilder = new ContainerBuilder();

        return $containerBuilder->process(new Pimple(), $options);
    }
}
