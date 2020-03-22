<?php declare(strict_types=1);

namespace CP\TreasureHunt\Presenters;

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

        /** @var Form $form */
        $form = $this['actionForm'];

        $form->onSuccess[] = function (Form $form, $values) use ($challenge) {
            $action = new Action();
            $action->challenge = $challenge;
            $action->type = $values['type'];
            $action->params = $values['params'];

            $this->challengeService->saveAction($action);
            $this->redirect('Challenges:detail', ['id' => $challenge->id]);
        };
    }

    public function actionDetail(string $challengeId, string $actionId)
    {
        $action = $this->challengeService->getAction($actionId);
        if ($action->challenge->id != $challengeId) {
            throw new BadRequestException();
        }

        /** @var Form $var */
        $var = $this['actionForm'];

        $var->setDefaults($action->getData());
        $var->onSuccess[] = function($form, $values) use ($action, $challengeId) {
            $action->assign($values);
            $this->challengeService->saveAction($action);

            $this->redirect('Challenges:detail', ['id' => $challengeId]);
        };
    }

    public function createComponentActionForm()
    {
        $form = new Form();
        $form->addSelect('type', 'Typ akce', [
            Action::TYPE_ACTIVATE_CHALLENGE => 'Aktivovat výzvu',
            Action::TYPE_REVEAL_NARRATIVE => 'Zobrazit průpravu',
        ]);
        $form->addText('params', 'Parametry akce');
        $form->addSubmit('save', 'Vytvořit');

        return $form;
    }

}
