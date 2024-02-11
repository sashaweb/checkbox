<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AuthorRequestDto
{
    public ?int $id = null;
    #[NotBlank]
    #[Length(min:3)]
    public ?string $firstName = null;

    #[NotBlank]
    public ?string $lastName = null;

    public ?string $patronymic = null;

}
