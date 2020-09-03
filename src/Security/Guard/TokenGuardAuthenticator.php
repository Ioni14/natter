<?php

namespace App\Security\Guard;

use App\Security\Token\Token;
use App\Security\Token\TokenStore;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class TokenGuardAuthenticator extends AbstractGuardAuthenticator
{
    private TokenStore $tokenStore;

    public function __construct(TokenStore $tokenStore)
    {
        $this->tokenStore = $tokenStore;
    }

    public function supports(Request $request): bool
    {
        dump($request->headers->all());
        return $this->tokenStore->read(null) !== null;
    }

    public function getCredentials(Request $request): array
    {
        $token = $this->tokenStore->read(null);

        return ['token' => $token];
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        /** @var Token $token */
        $token = $credentials['token'];
        if (new \DateTimeImmutable() >= $token->expireAt) {
            throw new AuthenticationException('Token has expired.');
        }

        return $userProvider->loadUserByUsername($token->username);
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['error' => $exception->getMessageKey()], 401);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): ?Response
    {
        return null;
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse(['error' => 'Auth session cookie required'], 401, []);
    }
}
