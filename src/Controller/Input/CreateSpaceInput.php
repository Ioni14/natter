<?php

namespace App\Controller\Input;

use Symfony\Component\Validator\Constraints as Assert;

class CreateSpaceInput
{
    /**
     * @Assert\Length(max="255")
     * @Assert\NotBlank()
     */
    public ?string $name;

    /**
     * @Assert\Regex(pattern="~[a-zA-Z][a-zA-Z0-9]{1,29}~")
     * @Assert\NotBlank()
     */
    public ?string $owner;
}
