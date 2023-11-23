<?php

declare(strict_types=1);

namespace App\ChatBot;

use App\Entity\Question;
use App\Repository\QuestionRepository;

final readonly class QuestionHandler implements CommandHandlerInterface
{
    private const QUESTION_PATTERN = '/^!question (?<question>.*)$/';

    public function __construct(private QuestionRepository $questionRepository)
    {
    }

    public function supports(Message $message): bool
    {
        return preg_match(self::QUESTION_PATTERN, $message->content) === 1;
    }

    public function handle(Message $message): string
    {
        /* @var array{question: string} $matches */
        preg_match(self::QUESTION_PATTERN, $message->content, $matches);

        $this->questionRepository->save(
            (new Question())
                ->setUsername($message->username)
                ->setContent($matches['question'])
        );

        return sprintf(
            'Merci @%s pour ta question, j\'y répondrais lors de la FAQ, un peu de patience.',
            $message->username
        );
    }
}
