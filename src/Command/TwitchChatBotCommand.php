<?php

namespace App\Command;

use App\ChatBot\CommandHandlerInterface;
use App\ChatBot\Message;
use App\Repository\CommandRepository;
use App\Repository\QuestionRepository;
use GhostZero\Tmi\Client;
use GhostZero\Tmi\Events\Twitch\MessageEvent;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function Symfony\Component\String\u;

#[AsCommand(
    name: 'twitch:chat-bot',
    description: 'Chat Bot Twitch',
)]
final class TwitchChatBotCommand extends Command
{
    /**
     * @param iterable<CommandHandlerInterface> $commandHandlers
     */
    public function __construct(private readonly Client $client, private readonly iterable $commandHandlers)
    {
        parent::__construct();
    }

    private function onMessage(MessageEvent $event): void
    {
        $message = Message::create($event->user, $event->message);

        foreach ($this->commandHandlers as $commandHandler) {
            if ($commandHandler->supports($message)) {
                $this->client->say($event->channel->getName(), $commandHandler->handle($message));
            }
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->client->on(MessageEvent::class, $this->onMessage(...));

        $this->client->connect();

        return Command::SUCCESS;
    }
}
