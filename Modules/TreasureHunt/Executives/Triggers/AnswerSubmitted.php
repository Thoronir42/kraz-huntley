<?php declare(strict_types=1);

namespace CP\TreasureHunt\Executives\Triggers;

use CP\TreasureHunt\Model\Entity\Challenge;
use CP\TreasureHunt\Model\Entity\Notebook;

class AnswerSubmitted
{
    /** @var Notebook */
    private $notebook;
    /** @var Challenge */
    private $challenge;
    /** @var mixed */
    private $answer;

    public function __construct(Notebook $notebook, Challenge $challenge, $answer)
    {
        $this->notebook = $notebook;
        $this->challenge = $challenge;
        $this->answer = $answer;
    }

    public function getNotebook(): Notebook
    {
        return $this->notebook;
    }

    public function getChallenge(): Challenge
    {
        return $this->challenge;
    }

    public function getAnswer()
    {
        return $this->answer;
    }
}
