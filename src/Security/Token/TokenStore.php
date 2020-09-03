<?php

namespace App\Security\Token;

interface TokenStore
{
    /**
     * @return string the token ID
     */
    public function createToken(Token $token): string;
    public function read(?string $tokenId): ?Token;
}
