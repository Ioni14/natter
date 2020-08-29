<?php

namespace App\Controller\Input;

use Symfony\Component\Validator\Constraints as Assert;

class PostMessageInput
{
    /**
     * @Assert\Length(max="1024")
     * @Assert\NotBlank()
     */
    public ?string $message;

    /**
     * @Assert\Regex(pattern="~[a-zA-Z][a-zA-Z0-9]{0,29}~")
     * @Assert\NotBlank()
     */
    public ?string $author;
}
