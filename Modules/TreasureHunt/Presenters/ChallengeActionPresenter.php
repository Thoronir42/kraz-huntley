<?php declare(strict_types=1);

namespace CP\TreasureHunt\Presenters;

use CP\TreasureHunt\Model\Service\ChallengesService;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use SeStep\Executives\Components\ActionForm\ActionForm;
use SeStep\Executives\Components\ActionForm\ActionFormFactory;
use SeStep\Executives\Model\Entity\Action;
use SeStep\Executives\Model\Service\ActionsService;

class ChallengeActionPresenter extends Presenter
{
    /** @var ChallengesService @inject */
    public $challengeService;
    /** @var ActionsService @inject */
    public $actionsService;

    /** @var ActionFormFactory @inject */
    public $actionFormFactory;

    public function actionAdd(string $challengeId)
    {
        $challenge = $this->challengeService->getChallenge($challengeId);
        if (!$challenge) {
            throw new BadRequestException();
        }

        $this->setView('detail');

        $this['actionForm'] = $form = $this->actionFormFactory->create();

        $form->onSave[] = function (Form $form, Action $action, $conditions) use ($challenge) {
            $action->script = $challenge->submitScript;

            $this->actionsService->saveAction($action, $conditions);
            $this->redirect('Challenges:detail', ['id' => $challenge->id]);
        };
    }

    public function actionDetail(string $challengeId, string $actionId)
    {
        $challenge = $this->challengeService->getChallenge($challengeId);
        $action = $this->actionsService->getAction($actionId);

        if (!$challenge || !$action) {
            throw new BadRequestException();
        }
        if ($challenge->submitScript != $action->script) {
            throw new BadRequestException();
        }

        $this['actionForm'] = $form = $this->actionFormFactory->create($action);

        $form->setAction($action);
        $form->onSave[] = function (Form $form, Action $action, $conditions) use ($challengeId) {
            $this->actionsService->saveAction($action, $conditions);

            $this->redirect('Challenges:detail', ['id' => $challengeId]);
        };
    }
}
