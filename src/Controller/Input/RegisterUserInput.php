<?php

namespace App\Controller\Input;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserInput
{
    /**
     * @Assert\Length(max="50")
     * @Assert\Regex(pattern="~[a-zA-Z][a-zA-Z0-9]{1,29}~")
     * @Assert\NotBlank()
     */
    public ?string $username;

    /**
     * @Assert\Length(min="8")
     * @Assert\NotBlank()
     */
    public ?string $password;
}
