<?php

namespace App\Controller\Input;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Assert\Expression("this.owner == this.subject", message="owner must match authenticated user")
 */
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

    public ?string $subject;
}
