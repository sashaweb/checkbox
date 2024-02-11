<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints\NotBlank;

class BookRequestDto
{
    #[NotBlank]
    public ?string $title = null;

    public ?string $description = null;

    #[NotBlank]
    public ?string $imageBase64 = null;

    #[NotBlank]
    public ?\DateTimeImmutable $publishedAt = null;

    #[NotBlank]
    public ?array $authorIds = null;


}
