<?php
namespace AlejandroHerr\Security\Core\Authentication\Provider;

use AlejandroHerr\Security\Core\Authentication\Token\WsseUserToken;
use AlejandroHerr\Security\Core\Exception\BadCredentialsException;
use AlejandroHerr\Security\Core\Exception\NonceUsedException;
use AlejandroHerr\Security\Core\Exception\WsseAuthenticationException;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class WsseProvider implements AuthenticationProviderInterface
{
    private $userProvider;
    private $cacheDir;
    public function __construct(UserProviderInterface $userProvider, $cacheDir)
    {
        $this->userProvider = $userProvider;
        $this->cacheDir = $cacheDir;
    }
    public function authenticate(TokenInterface $token)
    {
        try {
            $user = $this->userProvider->loadUserByUsername($token->getUsername());
            if ($user && $this->validateDigest($token->digest, $token->nonce, $token->created, $user->getPassword())) {
                $authenticatedToken = new WsseUserToken($user->getRoles());
                $authenticatedToken->setUser($user);

                return $authenticatedToken;
            }

        } catch (WsseAuthenticationException $e) {
            throw $e;
        }
        throw new BadCredentialsException();
    }

    protected function validateDigest($digest, $nonce, $created, $secret)
    {
        if (strtotime($created) > (time() + 60)) {
            return false;
        }
        if (time() - strtotime($created) > 300) {
            return false;
        }

        if (file_exists($this->cacheDir . '/' . $nonce)) {
            if (file_get_contents($this->cacheDir . '/' . $nonce) + 300 > time()) {
                throw new NonceUsedException('Previously used nonce detected');
            }
        }

        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
        file_put_contents($this->cacheDir . '/' . $nonce, time());

        $expected = base64_encode(hash('sha512',base64_decode($nonce) . $created . $secret, true));

        return $digest === $expected;
    }
    public function supports(TokenInterface $token)
    {
        return $token instanceof WsseUserToken;
    }
}
