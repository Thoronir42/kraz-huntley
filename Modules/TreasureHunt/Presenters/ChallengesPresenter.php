<?php declare(strict_types=1);

namespace CP\TreasureHunt\Presenters;

use CP\TreasureHunt\Components\ActionGridFactory;
use CP\TreasureHunt\Components\ChallengeFormFactory;
use CP\TreasureHunt\Components\ChallengesGridFactory;
use CP\TreasureHunt\Model\Entity\Challenge;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use CP\TreasureHunt\Model\Service\ChallengesService;

class ChallengesPresenter extends Presenter
{
    /** @var ChallengesService @inject */
    public $challengesService;

    /** @var ChallengesGridFactory @inject */
    public $challengesGridFactory;
    /** @var ChallengeFormFactory @inject */
    public $challengeFormFactory;

    /** @var ActionGridFactory @inject */
    public $actionGridFactory;

    public function actionCreateNew()
    {
        $this->setView('edit');

        /** @var Form $form */
        $form = $this['challengeForm'];

        $form->onSuccess[] = function (Form $form, $values) {
            $challenge = new Challenge($values);
            $this->challengesService->save($challenge);
            $this->redirect('detail', $challenge->id);
        };
    }

    public function actionDetail(string $id)
    {
        $this->setView('edit');
        $this->template->challenge = $challenge = $this->challengesService->getChallenge($id);
        if (!$challenge) {
            throw new BadRequestException("Challenge $id does not exist");
        }

        /** @var Form $form */
        $form = $this['challengeForm'];

        $form->setDefaults($challenge->getData());
        $form->onSuccess[] = function (Form $form, $values) use ($challenge) {
            $challenge->assign($values);
            $this->challengesService->save($challenge);

            $this->redirect('this');
        };

        $actionGrid = $this->actionGridFactory->create();
        $actionGrid->setDataSource($this->challengesService->getActionsDataSource($challenge));

        $actionGrid->addAction('edit', 'Upravit', 'ChallengeAction:detail', ['actionId' => 'id'])
            ->addParameters(['challengeId' => $challenge->id]);

        $this['actionGrid'] = $actionGrid;
    }

    public function renderIndex()
    {
        $grid = $this->challengesGridFactory->create();
        $grid->setDataSource($this->challengesService->getChallengesDataSource());

        $grid->setItemsPerPageList(['all']);
        $grid->addAction('detail', 'Upravit', 'detail');

        $this['challengesGrid'] = $grid;
    }

    public function createComponentChallengeForm()
    {
        return $this->challengeFormFactory->create();
    }
}
