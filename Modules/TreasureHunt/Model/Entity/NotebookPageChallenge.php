<?php declare(strict_types=1);

namespace CP\TreasureHunt\Model\Entity;

class NotebookPageChallenge extends NotebookPage
{
    public function getChallengeId(): string
    {
        return $this->params['challengeId'];
    }
}
