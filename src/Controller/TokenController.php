<?php

namespace App\Controller;

use App\Security\Token\Token;
use App\Security\Token\TokenStore;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class TokenController
{
    private Security $security;
    private TokenStore $tokenStore;

    public function __construct(Security $security, TokenStore $tokenStore)
    {
        $this->security = $security;
        $this->tokenStore = $tokenStore;
    }

    /**
     * @Route("/sessions", methods={"POST"})
     */
    public function login(Request $request): Response
    {
        $user = $this->security->getUser();
        if ($user === null) {
            throw new \LogicException('no auth user');
        }

        $token = new Token(new \DateTimeImmutable('+10 minutes'), $user->getUsername());
        $tokenId = $this->tokenStore->createToken($token);

        return new JsonResponse(['token_id' => $tokenId], 200, [], false);
    }
}
