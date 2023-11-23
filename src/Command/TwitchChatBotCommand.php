<?php

namespace App\Command;

use App\Entity\Message;
use App\Repository\QuestionRepository;
use GhostZero\Tmi\Client;
use GhostZero\Tmi\Events\Twitch\MessageEvent;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'twitch:chat-bot',
    description: 'Chat Bot Twitch',
)]
class TwitchChatBotCommand extends Command
{
    public function __construct(
        private readonly Client $client,
        private readonly ValidatorInterface $validator,
        private readonly QuestionRepository $questionRepository,
    ) {
        parent::__construct();
    }

    private function onMessage(MessageEvent $event): void
    {
        if ($event->self) {
            return;
        }

        $message = Message::create($event->user, $event->message);

        if ($this->validator->validate($message)->count() > 0) {
            return;
        }

        $this->questionRepository->save($message->toQuestion());

        $this->client->say(
            $event->channel->getName(),
            sprintf(
                'Merci @%s pour ta question, j\'y répondrais lors de la FAQ, un peu de patience.',
                $message->username
            )
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->client->on(MessageEvent::class, $this->onMessage(...));

        $this->client->connect();

        return Command::SUCCESS;
    }
}
