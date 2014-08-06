<?php
namespace AlejandroHerr\Stack\Cors;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors implements HttpKernelInterface
{
    protected $app;
    protected $corsManager;

    public function __construct(HttpKernelInterface $app, array $options = array())
    {
        $this->app = $app;
        $this->corsManager = new CorsManager($options);
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        if (!$this->corsManager->isCorsRequest($request)) {
            return $this->app->handle($request, $type, $catch);
        }
        if ($this->corsManager->isCorsPreflightRequest($request)) {
            return $this->corsManager->createCorsPreflightResponse($request);
        }
        if (!$this->corsManager->isValidCorsRequest($request)) {
            return $this->corsManager->createInvalidCorsResponse();
        }

        $response = $this->app->handle($request, $type, $catch);

        return $this->corsManager->addCorsResponseHeaders($request,$response);
    }

    protected function jsonResponseToResponse(Response $response)
    {
        $content = $response->getContent();
        $status = $response->getStatusCode();
        $response = $response->headers->all();

        return new Response($content,$status,$response);
    }
}
