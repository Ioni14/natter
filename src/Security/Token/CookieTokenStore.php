<?php

namespace App\Security\Token;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CookieTokenStore implements TokenStore
{
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function createToken(Token $token): string
    {
        if (!$this->session->isStarted()) {
            $this->session->start();
        }
        $this->session->invalidate(); // force new session to prevent Session Fixation Attacks

        $this->session->set('username', $token->username);
        $this->session->set('expiry', $token->expireAt);
        $this->session->set('attrs', $token->attributes);

        return $this->session->getId();
    }

    public function read(?string $tokenId): ?Token
    {
        dump($this->session->all());
        if (!$this->session->has('username') || !$this->session->has('expiry')) {
            return null;
        }

        $token = new Token($this->session->get('expiry'), $this->session->get('username'));
        $token->attributes = $this->session->get('attrs', []);

        return $token;
    }
}
