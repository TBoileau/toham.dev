<?php

declare(strict_types=1);

namespace App\ChatBot;

use Symfony\Component\Validator\Constraints\NotBlank;

final class Message
{
    private function __construct(
        #[NotBlank]
        public string $username,
        #[NotBlank]
        public string $content
    ) {
    }

    public static function create(string $username, string $message): self
    {
        return new self($username, $message);
    }
}
