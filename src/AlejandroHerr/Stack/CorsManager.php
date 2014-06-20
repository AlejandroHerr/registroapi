<?php

namespace AlejandroHerr\Stack;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsManager
{
    protected $options;

    public function __construct($options = array())
    {
        $this->setOptions($options);
    }

    protected function setOptions($options = array())
    {
        $options = array_merge(
            array(
                'allowedOrigins' => array(),
                'allowedHeaders' => array(),
                'allowedMethods' => array(),
                'supportsCredentials' => false,
                'exposedHeaders' => false,
                'maxAge' => 0
            ),
            $options
        );
        $options['allowedMethods'] = array_map('strtoupper',$options['allowedMethods']);
        $options['allowedHeaders'] = array_map('strtolower',$options['allowedHeaders']);
        $options['exposedHeaders'] = $options['exposedHeaders'] ? array_map('strtolower',$options['exposedHeaders']) : false;
        $this->options = $options;
    }

    public function addCorsResponseHeaders(Request $request, Response $response)
    {
        $this->wirteOriginHeadersResponse($request, $response);
        if ($this->options['exposedHeaders']) {
            $exposedHeaders = implode(', ', $this->options['exposedHeaders']);
            $response->headers->set('Access-Control-Expose-Headers',$exposedHeaders);
        }

        return $response;
    }

    public function createCorsPreflightResponse(Request $request)
    {
        if (!$this->isValidCorsPreflightRequest($request)) {
            return $this->createInvalidCorsResponse;
        }

        $response = new Response();

        $this->wirteOriginHeadersResponse($request, $response);
        $this->writeAllowedMethodsHeaders($response);
        $response->headers->set('Access-Control-Allow-Headers',$request->headers->get('Access-Control-Request-Headers'));
        $response->headers->set('Access-Control-Max-Age',$this->options['maxAge']);

        return $response;

    }

    public function createInvalidCorsResponse()
    {
        return new Response(
            json_encode('Invalid CORS'),
            403,
            array('Content-Type' => 'application/json')
        );
    }

    public function isCorsPreflightRequest(Request $request)
    {
        return $request->getMethod() === 'OPTIONS'
            && $request->headers->has('Access-Control-Request-Method');
    }

    public function isCorsRequest(Request $request)
    {
        return $request->headers->has('Origin');
    }

    public function isValidCorsPreflightRequest(Request $request)
    {
        return $this->isOriginAllowed($request)
            && $this->isRequestMethodAllowed($request)
            && $this->isRequestHeadersAllowed($request)
        ;

    }

    public function isValidCorsRequest(Request $request)
    {
        return $this->isOriginAllowed($request) && $this->isHttpMethodAllowed($request);
    }

    protected function isHttpMethodAllowed(Request $request)
    {
        $method = $request->getMethod();

        return in_array($method, $this->options['allowedMethods'])
            || in_array('*', $this->options['allowedMethods']);
    }

    protected function isOriginAllowed(Request $request)
    {
        $origin = $request->headers->get('Origin');

        return in_array($origin, $this->options['allowedOrigins'])
            || in_array('*', $this->options['allowedOrigins']);
    }

    protected function isRequestHeadersAllowed(Request $request)
    {
        if (!$request->headers->has('Access-Control-Request-Headers')) {
            return true;
        }
        if (in_array('*', $this->options['allowedMethods'])) {
            return true;
        }

        $requestHeaders = explode(',', $request->headers->get('Access-Control-Request-Headers'));
        $requestHeaders = array_map('trim',$requestHeaders);
        $listAllowedHeaders = $this->options['allowedHeaders'];

        return array_reduce(
            $requestHeaders,
            function ($r, $i) use ($listAllowedHeaders) {
                $r = $r && in_array($i, $listAllowedHeaders);

                return $r;
            },
            true
        );
    }

    protected function isRequestMethodAllowed(Request $request)
    {
        $method = $request->headers->get('Access-Control-Request-Method');

        return in_array($method, $this->options['allowedMethods'])
            || in_array('*', $this->options['allowedMethods']);
    }

    protected function writeAllowedMethodsHeaders(Response $response)
    {
        $response->headers->set('Access-Control-Allow-Methods',implode(', ', $this->options['allowedMethods']));
        if (in_array('*', $this->options['allowedMethods'])) {
            $response->headers->set('Access-Control-Allow-Methods','GET, POST, PUT, DELETE, PATCH, OPTIONS');
        }
    }

    protected function wirteOriginHeadersResponse(Request $request, Response $response)
    {
        $response->headers->set('Access-Control-Allow-Origin',$request->headers->get('Origin'));
        if ($this->options['supportsCredentials']) {
            $response->headers->set('Access-Control-Allow-Credentials','true');
            if (in_array('*', $this->options['allowedOrigins'])) {
                $response->headers->set('Access-Control-Allow-Origin','*');
            }
        }
    }
}
