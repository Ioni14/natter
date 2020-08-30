<?php

namespace App\Security\Guard;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class BasicGuardAuthenticator extends AbstractGuardAuthenticator
{
    private UserPasswordEncoderInterface $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function supports(Request $request): bool
    {
        return $request->headers->has('Authorization') && stripos($request->headers->get('Authorization'), 'Basic ') === 0;
    }

    public function getCredentials(Request $request): array
    {
        $basic = substr($request->headers->get('Authorization'), strlen('Basic '));
        $credentialsStr = base64_decode($basic);
        if (strpos($credentialsStr, ':') === false) {
            throw new AuthenticationException('Basic credentials do not contain semicolon.');
        }
        $credentials = explode(':', $credentialsStr);

        return [
            'username' => array_shift($credentials),
            'password' => implode(':', $credentials),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        return $userProvider->loadUserByUsername($credentials['username']);
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $this->userPasswordEncoder->isPasswordValid($user, $credentials['password']);
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
        return new JsonResponse(['error' => 'Auth required'], 401);
    }
}
