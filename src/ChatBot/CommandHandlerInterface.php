<?php

declare(strict_types=1);

namespace App\ChatBot;

interface CommandHandlerInterface
{
    public function supports(Message $message): bool;

    public function handle(Message $message): string;
}
