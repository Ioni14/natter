<?php

namespace App\Security\Token;

class Token
{
    public \DateTimeInterface $expireAt;
    public string $username;
    /** @var array<string,string> */
    public array $attributes = [];

    public function __construct(\DateTimeInterface $expireAt, string $username)
    {
        $this->expireAt = $expireAt;
        $this->username = $username;
    }
}
