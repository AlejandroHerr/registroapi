<?php
namespace Esnuab\Libro\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Esnuab\Libro\Security\WsseUserToken;

class WsseListener implements ListenerInterface
{
	protected $securityContext;
	protected $authenticationManager;
	public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, Connection $conn, LoggerInterface $logger = null) {
		$this->securityContext = $securityContext;
		$this->authenticationManager = $authenticationManager;
		$this->conn = $conn;
		$this->logger = $logger;
	}
	public function handle(GetResponseEvent $event)
	{
		$request = $event->getRequest();
		if ($this->isIpBlocked($request)) {
			if (null !== $this->logger) {
				$this->logger->addNotice('IP bloqueada / Acceso no autorizado');
			}
			$response = new JsonResponse(array(
				'error' => 'Too many login attempts in the last 30 minutes. Waith 30 min.'
			), 401);
			$event->setResponse($response);
			return;
		}
		$wsseRegex = '/UsernameToken Username="([^"]+)", PasswordDigest="([^"]+)", Nonce="([^"]+)", Created="([^"]+)"/';
		if (!$request->headers->has('x-wsse') || 1 !== preg_match($wsseRegex, $request->headers->get('x-wsse'), $matches)) {
			$response = new JsonResponse(array(
				'error' => 'Wrong headers.'
			), 401);
			$event->setResponse($response);
			return;
		}
		$token = new WsseUserToken();
		$token->setUser($matches[1]);
		$token->digest = $matches[2];
		$token->nonce = $matches[3];
		$token->created = $matches[4];
		try {
			$authToken = $this->authenticationManager->authenticate($token);
			$this->securityContext->setToken($authToken);
			if (null !== $this->logger) {
				$this->logger->addInfo('Acceso autorizado');
			}
			return;
		}
		catch (AuthenticationException $failed) {
			$this->reportIp($request);
			if (null !== $this->logger) {
				$this->logger->addNotice('Acceso no autorizado');
			}
			$response = new JsonResponse(array(
				'error' => 'Wrong credentials.'
			), 401);
			$event->setResponse($response);
			return;
		}
		$response = new JsonResponse();
		$response->setStatusCode(401);
		$event->setResponse($response);
	}
	protected function isIpBlocked(Request $request)
	{
		$ip = $request->getClientIp();
		$ip = str_replace('.', 'p', $ip);
		$timeLimit = time() - 30 * 60;
		$sql = "SELECT COUNT(id) AS total FROM login_attempts WHERE ip = ? AND timestamp >= ?";
		$stmt = $this->conn->prepare($sql);
		$stmt->bindValue(1, $ip);
		$stmt->bindValue(2, $timeLimit);
		$stmt->execute();
		$count = $stmt->fetch($query);
		if ($count['total'] < 10) {
			return false;
		}
		return true;
	}
	protected function reportIp(Request $request)
	{
		$ip = $request->getClientIp();
		$ip = str_replace('.', 'p', $ip);
		$time = time();
		$this->conn->insert('login_attempts', array(
			'ip' => $ip,
			'timestamp' => $time
		));
	}
}