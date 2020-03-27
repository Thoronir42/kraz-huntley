<?php declare(strict_types=1);

namespace CP\TreasureHunt\Presenters;

use CP\TreasureHunt\Components\ActionForm\ActionForm;
use CP\TreasureHunt\Model\Entity\Action;
use CP\TreasureHunt\Model\Service\ChallengesService;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

class ChallengeActionPresenter extends Presenter
{

    /** @var ChallengesService @inject */
    public $challengeService;

    public function actionAdd(string $challengeId)
    {
        $challenge = $this->challengeService->getChallenge($challengeId);
        if (!$challenge) {
            throw new BadRequestException();
        }

        $this->setView('detail');

        /** @var ActionForm $form */
        $form = $this['actionForm'];

        $form->onSave[] = function (Form $form, Action $action, $conditions) use ($challenge) {
            $action->challenge = $challenge;

            $this->challengeService->saveAction($action, $conditions);
            $this->redirect('Challenges:detail', ['id' => $challenge->id]);
        };
    }

    public function actionDetail(string $challengeId, string $actionId)
    {
        $action = $this->challengeService->getAction($actionId);
        if ($action->challenge->id != $challengeId) {
            throw new BadRequestException();
        }

        /** @var ActionForm $form */
        $form = $this['actionForm'];


        $form->setAction($action);
        $form->onSave[] = function(Form $form, Action $action, $conditions) use ($challengeId) {
            $this->challengeService->saveAction($action, $conditions);

            $this->redirect('Challenges:detail', ['id' => $challengeId]);
        };
    }

    public function createComponentActionForm()
    {
        return new ActionForm();
    }

}
