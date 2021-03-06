<?php declare(strict_types=1);

namespace CP\TreasureHunt\Presenters;

use CP\TreasureHunt\Components\Challenge\ChallengeFormFactory;
use CP\TreasureHunt\Components\Challenge\ChallengeFormNotebookRenderer;
use CP\TreasureHunt\Components\ChallengesGridFactory;
use CP\TreasureHunt\Model\Entity\Challenge;
use CP\TreasureHunt\Model\Service\ChallengesService;
use CP\TreasureHunt\Model\Service\NotebookService;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Localization\ITranslator;
use SeStep\Executives\Model\ActionData;
use CP\TreasureHunt\Components\Challenge\OnSubmitActionsFormFactory;
use SeStep\NetteTypeful\Forms\PropertyControlFactory;

class ChallengesPresenter extends Presenter
{
    use Traits\ProtectManagement;

    /** @var ChallengesService @inject */
    public $challengesService;

    /** @var ChallengesGridFactory @inject */
    public $challengesGridFactory;
    /** @var ChallengeFormFactory @inject */
    public $challengeFormFactory;
    /** @var OnSubmitActionsFormFactory @inject */
    public $actionFormFactory;
    /** @var NotebookService @inject */
    public $notebookService;

    /** @var ITranslator @inject */
    public $translator;
    /** @var PropertyControlFactory @inject */
    public $propertyControlFactory;

    public function actionCreateNew()
    {
        $this->setView('edit');

        /** @var Form $form */
        $form = $this['challengeForm'];

        $form->onSuccess[] = function (Form $form, $values) {
            $isFirst = !$this->challengesService->getNames();

            $challenge = new Challenge($values);
            $this->challengesService->save($challenge);
            if ($isFirst) {
                $this->notebookService->setFirstChallengeId($challenge->id);
            }
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

        $actionForm = $this->actionFormFactory->create($challenge->onSubmit);

        $actionForm->onSave[] = function ($form, ActionData $action) use ($challenge) {
            $this->challengesService->setOnSubmitAction($challenge, $action);
            $this->flashMessage('th.challengeOnSave.updated');
            $this->redirect('this');
        };

        $this['actionForm'] = $actionForm;

        $correctAnswerForm = $this['correctAnswerForm'] = $this->createCorrectAnswerForm($challenge);
        $correctAnswerForm->setDefaults([
            'correctAnswer' => $challenge->correctAnswer,
        ]);
        $correctAnswerForm->onSuccess[] = function ($form, $values) use ($challenge) {
            $challenge->correctAnswer = $values['correctAnswer'];
            $this->challengesService->save($challenge);

            $this->redirect('this');
        };
    }

    protected function beforeRender()
    {
        parent::beforeRender();
        $this->setLayout('meta');
    }

    public function renderIndex()
    {
        if (!$this->challengesService->getNames()) {
            $this->redirect('createNew');
        }

        $firstChallenge = $this->notebookService->getFirstChallengeId();
        $firstChallengeSelection = $this['firstChallengeSelection'];
        $firstChallengeSelection->setDefaults([
            'firstChallenge' => $firstChallenge,
        ]);
    }

    public function createComponentChallengesGrid()
    {
        $grid = $this->challengesGridFactory->create();
        $grid->setDataSource($this->challengesService->getChallengesDataSource());

        $grid->setItemsPerPageList(['all']);
        $grid->addAction('detail', 'Upravit', 'detail');

        return $grid;
    }

    public function createComponentChallengeForm()
    {
        $form = $this->challengeFormFactory->create();
        $form->setRenderer($this->context->createInstance(ChallengeFormNotebookRenderer::class));

        return $form;
    }

    public function createComponentFirstChallengeSelection()
    {
        $form = new Form();
        $form->setTranslator($this->context->getService('translation.translator'));
        $firstChallenge = $form->addSelect('firstChallenge', 'appTreasureHunt.firstChallenge')
            ->setItems($this->challengesService->getNames());

        $firstChallenge->controlPrototype->data('ajax-on-change',
            $this->link('changeFirstChallenge!', ['challenge' => '__value__']));

        $form->elementPrototype->class[] = 'ajax';

        return $form;
    }

    public function handleChangeFirstChallenge(string $challenge)
    {
        $this->notebookService->setFirstChallengeId($challenge);
        $this->redirect('this');
    }

    private function createCorrectAnswerForm(Challenge $challenge)
    {
        $form = new Form();
        $form->setTranslator($this->translator);

        $control = $form['correctAnswer'] = $this->propertyControlFactory->create('appTreasureHunt.challenge.correctAnswer',
            $challenge->keyType, $challenge->keyTypeOptions);

        if ($control->controlPrototype->name === 'input') {
            $control->controlPrototype->class[] = 'form-control';
        }

        $form->addSubmit('save', 'messages.save');

        return $form;
    }
}
