<?php

namespace AlejandroHerr\Security\Http\Firewall;

use AlejandroHerr\Security\Core\Exception\WsseAuthenticationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\LogoutException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Firewall\ExceptionListener;

class WsseExceptionListener extends ExceptionListener
{

    protected $logger;

    public function __construct(SecurityContextInterface $context, AuthenticationTrustResolverInterface $trustResolver, HttpUtils $httpUtils, $providerKey, AuthenticationEntryPointInterface $authenticationEntryPoint = null, $errorPage = null, AccessDeniedHandlerInterface $accessDeniedHandler = null, LoggerInterface $logger = null)
    {
        parent::__construct($context, $trustResolver, $httpUtils, $providerKey, $authenticationEntryPoint, $errorPage, $accessDeniedHandler);
        $this->logger = $logger;
    }
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        do {
            if ($exception instanceof WsseAuthenticationException) {
                return $this->handleAuthenticationException($event, $exception);
            } elseif ($exception instanceof AccessDeniedException) {
                return $this->handleAccessDeniedException($event, $exception);
            } elseif ($exception instanceof LogoutException) {
                return $this->handleLogoutException($event, $exception);
            }
        } while (null !== $exception = $exception->getPrevious());
    }

    private function handleAuthenticationException(GetResponseForExceptionEvent $event, AuthenticationException $exception)
    {
        if (null !== $this->logger) {
            $this->logger->info(sprintf('Wsse Authentication exception occurred (%u: %s)', $exception->getCode(),$exception->getMessage()));
        }

        try {
            $message = array('message' => $exception->getMessage());
            $response = new JsonResponse($message,$exception->getCode(),array('Content-Type'=>'application/json'));
            $event->setResponse($response);
        } catch (\Exception $e) {
            $event->setException($e);
        }
    }
}
