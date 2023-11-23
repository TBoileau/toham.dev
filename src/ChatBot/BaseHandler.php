<?php

declare(strict_types=1);

namespace App\ChatBot;

use App\Entity\Command;
use App\Repository\CommandRepository;

final readonly class BaseHandler implements CommandHandlerInterface
{
    public function __construct(private CommandRepository $commandRepository)
    {
    }

    public function supports(Message $message): bool
    {
        return $this->commandRepository->count(['name' => $message->content]) > 0;
    }

    public function handle(Message $message): string
    {
        /** @var Command $command */
        $command = $this->commandRepository->findOneBy(['name' => $message->content]);

        return sprintf('@%s %s', $message->username, $command->getMessage());
    }
}
