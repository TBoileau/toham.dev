<?php

declare(strict_types=1);

namespace App\Twitch\Overlay;

use App\Entity\Question;
use App\Repository\QuestionRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('twitch_overlay_question', template: 'components/twitch/question.html.twig')]
final class QuestionComponent
{
    use DefaultActionTrait;

    #[LiveProp]
    public ?Question $question = null;

    public function __construct(private readonly QuestionRepository $questionRepository)
    {
        $this->loadCurrentQuestion();
    }

    #[LiveAction]
    public function loadCurrentQuestion(): void
    {
        $this->question = $this->questionRepository->getCurrentQuestionToAnswer();
    }
}
