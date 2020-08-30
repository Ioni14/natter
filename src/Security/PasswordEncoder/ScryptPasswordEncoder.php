<?php

namespace App\Security\PasswordEncoder;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\SelfSaltingEncoderInterface;

class ScryptPasswordEncoder implements PasswordEncoderInterface, SelfSaltingEncoderInterface
{
    public function encodePassword(string $raw, ?string $salt): string
    {
        return sodium_crypto_pwhash_scryptsalsa208sha256_str($raw, 524_288, 33_554_432 /* 32MB */);
    }

    public function isPasswordValid(string $encoded, string $raw, ?string $salt): bool
    {
        return sodium_crypto_pwhash_scryptsalsa208sha256_str_verify($encoded, $raw);
    }

    public function needsRehash(string $encoded): bool
    {
        return false;
    }
}
